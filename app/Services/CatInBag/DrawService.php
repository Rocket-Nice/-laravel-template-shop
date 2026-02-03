<?php

namespace App\Services\CatInBag;

use App\Models\CatInBagPrize;
use Illuminate\Support\Collection;

class DrawService
{
    private $rand;
    private ?Collection $prizesCache = null;

    public function __construct(?callable $rand = null, ?Collection $prizes = null)
    {
        $this->rand = $rand ?? function (): float {
            return mt_rand(1, 1000000) / 1000000;
        };
        $this->prizesCache = $prizes;
    }

    /**
     * @param int $total
     * @param array $visibleCategoryIds
     * @param array|null $bagTypes Array of 'normal'/'golden'
     * @return array<int, array<string, mixed>>
     */
    public function draw(int $total, array $visibleCategoryIds, ?array $bagTypes = null): array
    {
        $tier = $this->getTier($total);
        if (!$tier) {
            return [];
        }

        $openCount = $tier['open_count'];
        $desiredCount = $openCount;
        if (is_array($bagTypes) && count($bagTypes) > 0) {
            $desiredCount = count($bagTypes);
        }
        $bagTypes = $bagTypes ?? array_fill(0, $desiredCount, 'normal');
        $bagTypes = array_slice(array_pad($bagTypes, $desiredCount, 'normal'), 0, $desiredCount);
        $openCount = $desiredCount;

        $prizes = $this->getAvailablePrizes();

        $productPrizes = $prizes->where('is_certificate', false);
        $goldenPrizes = $productPrizes->where('is_golden', true);
        $normalPrizes = $productPrizes->where('is_golden', false);
        $certificatePrizes = $prizes->where('is_certificate', true);

        $usedProductIds = [];
        $usedProductCategoryIds = [];
        $results = [];

        $certGiven = false;
        $visibleProductsGiven = 0;
        $requiredVisibleProducts = $tier['required_visible_products'] ?? 0;
        $needVisibleProduct = $tier['need_visible_product'] ?? false;

        foreach ($bagTypes as $index => $bagType) {
            $remainingBags = $openCount - $index;
            $remainingVisibleNeeded = max(0, $requiredVisibleProducts - $visibleProductsGiven);

            $outcome = $this->rollOutcome($tier, $bagType);

            if ($outcome['type'] === 'certificate' && $certGiven && $remainingVisibleNeeded === $remainingBags) {
                $outcome['type'] = 'product';
            }

            if ($outcome['type'] === 'certificate' && $certGiven) {
                $outcome['type'] = 'product';
            }

            if ($outcome['type'] === 'product') {
                $categoryIds = null;

                if ($tier['name'] === 'tier1') {
                    $categoryIds = $visibleCategoryIds;
                } elseif ($tier['name'] === 'tier2') {
                    if ($needVisibleProduct) {
                        $categoryIds = $visibleCategoryIds;
                    }
                } elseif ($tier['name'] === 'tier3') {
                    if ($remainingVisibleNeeded > 0) {
                        $categoryIds = $visibleCategoryIds;
                    }
                }

                $excludeCategoryIds = [];
                if ($tier['name'] === 'tier2' && count($usedProductCategoryIds) > 0) {
                    $excludeCategoryIds = $usedProductCategoryIds;
                }

                $source = $bagType === 'golden' && $outcome['is_golden_source'] ? $goldenPrizes : $normalPrizes;
                $productPrize = $this->pickProductPrize($source, $categoryIds, $excludeCategoryIds, $usedProductIds);

                if (!$productPrize) {
                    $certificatePrize = $this->pickCertificatePrize($certificatePrizes, $tier['certificates'], $usedProductIds);
                    if ($certificatePrize) {
                        $results[] = $this->formatResult($certificatePrize, $bagType, 'certificate');
                        $usedProductIds[] = $certificatePrize->product_id;
                        $certGiven = true;
                        continue;
                    }
                    continue;
                }

                $results[] = $this->formatResult($productPrize, $bagType, 'product');
                $usedProductIds[] = $productPrize->product_id;
                if (in_array($productPrize->category_id, $visibleCategoryIds, true)) {
                    $visibleProductsGiven++;
                    $needVisibleProduct = false;
                }
                $usedProductCategoryIds[] = $productPrize->category_id;
                continue;
            }

            $certificatePrize = $this->pickCertificatePrize($certificatePrizes, $tier['certificates'], $usedProductIds, $outcome['amount'] ?? null);
            if ($certificatePrize) {
                $results[] = $this->formatResult($certificatePrize, $bagType, 'certificate');
                $usedProductIds[] = $certificatePrize->product_id;
                $certGiven = true;
                continue;
            }

            $fallbackProduct = $this->pickProductPrize($normalPrizes, $visibleCategoryIds, [], $usedProductIds);
            if ($fallbackProduct) {
                $results[] = $this->formatResult($fallbackProduct, $bagType, 'product');
                $usedProductIds[] = $fallbackProduct->product_id;
                if (in_array($fallbackProduct->category_id, $visibleCategoryIds, true)) {
                    $visibleProductsGiven++;
                }
                $usedProductCategoryIds[] = $fallbackProduct->category_id;
            }
        }

        return $results;
    }

    private function getAvailablePrizes(): Collection
    {
        if ($this->prizesCache) {
            return $this->prizesCache->filter(function (CatInBagPrize $prize) {
                return $prize->is_enabled && $prize->total_qty > $prize->used_qty;
            })->values();
        }

        return CatInBagPrize::query()
            ->with('product')
            ->where('is_enabled', true)
            ->whereColumn('total_qty', '>', 'used_qty')
            ->get();
    }

    private function rollOutcome(array $tier, string $bagType): array
    {
        if ($bagType === 'golden') {
            $roll = $this->rand();
            if ($roll <= 0.6) {
                return ['type' => 'product', 'is_golden_source' => true];
            }
            $result = $this->rollByWeights($tier['weights']);
            $result['is_golden_source'] = false;
            return $result;
        }

        $result = $this->rollByWeights($tier['weights']);
        $result['is_golden_source'] = false;
        return $result;
    }

    private function rollByWeights(array $weights): array
    {
        $roll = $this->rand();
        $cursor = 0.0;
        foreach ($weights as $outcome) {
            $cursor += $outcome['weight'];
            if ($roll <= $cursor) {
                return $outcome;
            }
        }
        return $weights[count($weights) - 1];
    }

    private function pickProductPrize(Collection $prizes, ?array $categoryIds, array $excludeCategoryIds, array $usedProductIds): ?CatInBagPrize
    {
        $filtered = $prizes
            ->when($categoryIds !== null, function (Collection $query) use ($categoryIds) {
                return $query->whereIn('category_id', $categoryIds);
            })
            ->when(!empty($excludeCategoryIds), function (Collection $query) use ($excludeCategoryIds) {
                return $query->whereNotIn('category_id', $excludeCategoryIds);
            })
            ->when(!empty($usedProductIds), function (Collection $query) use ($usedProductIds) {
                return $query->whereNotIn('product_id', $usedProductIds);
            })
            ->values();

        if ($filtered->isEmpty()) {
            return null;
        }

        $index = (int)floor($this->rand() * $filtered->count());
        return $filtered[$index];
    }

    private function pickCertificatePrize(Collection $certificates, array $amounts, array $usedProductIds, ?int $preferredAmount = null): ?CatInBagPrize
    {
        $amount = $preferredAmount ?? $this->pickCertificateAmount($amounts);
        $filtered = $certificates
            ->when($amount !== null, function (Collection $query) use ($amount) {
                return $query->filter(function (CatInBagPrize $prize) use ($amount) {
                    return (int)($prize->product?->price ?? 0) === $amount;
                });
            })
            ->when(!empty($usedProductIds), function (Collection $query) use ($usedProductIds) {
                return $query->whereNotIn('product_id', $usedProductIds);
            })
            ->values();

        if ($filtered->isEmpty()) {
            $fallback = $certificates
                ->when(!empty($usedProductIds), function (Collection $query) use ($usedProductIds) {
                    return $query->whereNotIn('product_id', $usedProductIds);
                })
                ->values();
            if ($fallback->isEmpty()) {
                return null;
            }
            $index = (int)floor($this->rand() * $fallback->count());
            return $fallback[$index];
        }

        $index = (int)floor($this->rand() * $filtered->count());
        return $filtered[$index];
    }

    private function pickCertificateAmount(array $amounts): ?int
    {
        if (empty($amounts)) {
            return null;
        }
        $index = (int)floor($this->rand() * count($amounts));
        return $amounts[$index];
    }

    private function formatResult(CatInBagPrize $prize, string $bagType, string $type): array
    {
        return [
            'prize_id' => $prize->id,
            'product_id' => $prize->product_id,
            'category_id' => $prize->category_id,
            'type' => $type,
            'bag_type' => $bagType,
            'is_golden' => $prize->is_golden,
            'is_certificate' => $prize->is_certificate,
            'nominal' => $prize->product?->price ?? null,
        ];
    }

    private function getTier(int $total): ?array
    {
        if ($total >= 10000) {
            return [
                'name' => 'tier3',
                'open_count' => 3,
                'required_visible_products' => 2,
                'weights' => [
                    ['type' => 'product', 'weight' => 0.80],
                    ['type' => 'certificate', 'weight' => 0.15, 'amount' => 1500],
                    ['type' => 'certificate', 'weight' => 0.05, 'amount' => 2000],
                ],
                'certificates' => [1500, 2000],
            ];
        }

        if ($total >= 6500) {
            return [
                'name' => 'tier2',
                'open_count' => 2,
                'need_visible_product' => true,
                'weights' => [
                    ['type' => 'product', 'weight' => 0.80],
                    ['type' => 'certificate', 'weight' => 0.15, 'amount' => 1000],
                    ['type' => 'certificate', 'weight' => 0.05, 'amount' => 1500],
                ],
                'certificates' => [1000, 1500],
            ];
        }

        if ($total >= 4000) {
            return [
                'name' => 'tier1',
                'open_count' => 1,
                'weights' => [
                    ['type' => 'product', 'weight' => 0.80],
                    ['type' => 'certificate', 'weight' => 0.15, 'amount' => 750],
                    ['type' => 'certificate', 'weight' => 0.05, 'amount' => 1000],
                ],
                'certificates' => [750, 1000],
            ];
        }

        return null;
    }

    private function rand(): float
    {
        return ($this->rand)();
    }
}
