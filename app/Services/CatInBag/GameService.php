<?php

namespace App\Services\CatInBag;

use App\Models\CatInBagBag;
use App\Models\CatInBagPrize;
use App\Models\CatInBagSession;
use App\Models\Voucher;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class GameService
{
    public function createSessionForOrder(Order $order, array $visibleCategoryIds): ?CatInBagSession
    {
        return $this->createSession((int)$order->amount, $order->id, $order->user_id, $visibleCategoryIds);
    }

    public function createSession(int $total, ?int $orderId, ?int $userId, array $visibleCategoryIds): ?CatInBagSession
    {
        $tier = $this->getTier($total);
        if (!$tier) {
            return null;
        }

        if ($orderId) {
            $existing = CatInBagSession::query()->where('order_id', $orderId)->first();
            if ($existing) {
                return $existing->load('bags');
            }
        }

        return DB::transaction(function () use ($tier, $total, $orderId, $userId, $visibleCategoryIds) {
            $session = CatInBagSession::create([
                'order_id' => $orderId,
                'user_id' => $userId,
                'total' => $total,
                'visible_category_ids' => array_values($visibleCategoryIds),
                'bag_count' => $tier['bag_count'],
                'open_limit' => $tier['open_limit'],
                'opened_count' => 0,
                'status' => 'active',
                'data' => [
                    'seed' => random_int(1, 2147483647),
                ],
            ]);

            foreach ($tier['bag_types'] as $index => $bagType) {
                CatInBagBag::create([
                    'session_id' => $session->id,
                    'position' => $index + 1,
                    'type' => $bagType,
                ]);
            }

            $session->load('bags');
            $this->assignPreparedPrizes($session);

            return $session->load('bags');
        });
    }

    public function openBag(CatInBagBag $bag): CatInBagBag
    {
        if ($bag->opened_at) {
            $this->ensureGiftCode($bag);
            return $bag->refresh();
        }

        return DB::transaction(function () use ($bag) {
            $bag->refresh();
            if ($bag->opened_at) {
                return $bag;
            }

            $session = CatInBagSession::query()
                ->lockForUpdate()
                ->findOrFail($bag->session_id);

            if ($session->opened_count >= $session->open_limit) {
                return $bag;
            }

            $openIndex = (int)$session->opened_count + 1;
            $bag->open_index = $openIndex;
            $bag->opened_at = now();
            $bag->save();

            $session->opened_count = $openIndex;
            if ($session->opened_count >= $session->open_limit) {
                $session->status = 'completed';
            }
            $session->save();

            $bag->refresh();
            $this->createGiftVoucherIfNeeded($bag, $session);
            $this->ensureGiftCode($bag);

            return $bag->refresh();
        });
    }

    private function assignPreparedPrizes(CatInBagSession $session): void
    {
        $bags = $session->bags->values();
        if ($bags->isEmpty()) {
            return;
        }

        $bagTypes = $bags->pluck('type')->all();
        $drawService = new DrawService($this->makeSeededRand($session));
        $results = [];
        $maxAttempts = 200;
        $visibleCategoryIds = $session->visible_category_ids ?? [];
        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $results = $drawService->draw((int)$session->total, $visibleCategoryIds, $bagTypes);
            if ($this->validateBagAssignment($results, $session, $visibleCategoryIds)) {
                break;
            }
        }
        $usedProductIds = [];
        $assignedCount = 0;
        $bagsWithoutPrize = [];

        foreach ($bags as $index => $bag) {
            $result = $results[$index] ?? null;
            if (!$result || empty($result['prize_id'])) {
                $bagsWithoutPrize[] = $bag;
                continue;
            }

            $bag->prize_id = $result['prize_id'] ?? null;
            $bag->product_id = $result['product_id'] ?? null;
            $bag->prize_type = $result['type'] ?? null;
            $bag->nominal = $result['nominal'] ?? null;
            $bag->data = [
                'category_id' => $result['category_id'] ?? null,
                'is_golden' => $result['is_golden'] ?? false,
                'is_certificate' => $result['is_certificate'] ?? false,
            ];
            $bag->save();

            if ($bag->product_id) {
                $usedProductIds[] = $bag->product_id;
            }
            if ($bag->prize_id) {
                CatInBagPrize::query()->where('id', $bag->prize_id)->increment('used_qty');
                $assignedCount++;
            }
        }

        if (!empty($bagsWithoutPrize)) {
            $fallbackCandidates = CatInBagPrize::query()
                ->with('product')
                ->where('is_enabled', true)
                ->whereColumn('total_qty', '>', 'used_qty')
                ->when(!empty($usedProductIds), function ($query) use ($usedProductIds) {
                    $query->whereNotIn('product_id', $usedProductIds);
                })
                ->get()
                ->shuffle()
                ->values();

            foreach ($bagsWithoutPrize as $bag) {
                $fallbackPrize = $fallbackCandidates->shift();
                if (!$fallbackPrize) {
                    $bag->prize_type = 'empty';
                    $bag->save();
                    continue;
                }

                $bag->prize_id = $fallbackPrize->id;
                $bag->product_id = $fallbackPrize->product_id;
                $bag->prize_type = $fallbackPrize->is_certificate ? 'certificate' : 'product';
                $bag->nominal = $fallbackPrize->product?->price ?? null;
                $bag->data = [
                    'category_id' => $fallbackPrize->category_id,
                    'is_golden' => (bool)$fallbackPrize->is_golden,
                    'is_certificate' => (bool)$fallbackPrize->is_certificate,
                ];
                $bag->save();

                if ($fallbackPrize->product_id) {
                    $usedProductIds[] = $fallbackPrize->product_id;
                }
                CatInBagPrize::query()->where('id', $fallbackPrize->id)->increment('used_qty');
                $assignedCount++;
            }
        }

        $this->ensureNoEmptyBags($bags, $usedProductIds);

        if ($assignedCount === 0 && $bags->isNotEmpty()) {
            $fallbackPrize = CatInBagPrize::query()
                ->with('product')
                ->where('is_enabled', true)
                ->whereColumn('total_qty', '>', 'used_qty')
                ->inRandomOrder()
                ->first();

            if ($fallbackPrize) {
                $fallbackBag = $bags->first();
                $fallbackBag->prize_id = $fallbackPrize->id;
                $fallbackBag->product_id = $fallbackPrize->product_id;
                $fallbackBag->prize_type = $fallbackPrize->is_certificate ? 'certificate' : 'product';
                $fallbackBag->nominal = $fallbackPrize->product?->price ?? null;
                $fallbackBag->data = [
                    'category_id' => $fallbackPrize->category_id,
                    'is_golden' => (bool)$fallbackPrize->is_golden,
                    'is_certificate' => (bool)$fallbackPrize->is_certificate,
                ];
                $fallbackBag->save();
                CatInBagPrize::query()->where('id', $fallbackPrize->id)->increment('used_qty');
            }
        }
    }

    private function ensureNoEmptyBags($bags, array $usedProductIds): void
    {
        $emptyBags = $bags->filter(function ($bag) {
            return empty($bag->prize_id) || $bag->prize_type === 'empty';
        })->values();

        if ($emptyBags->isEmpty()) {
            return;
        }

        $certCount = $bags->filter(function ($bag) {
            return $bag->prize_type === 'certificate';
        })->count();

        $availableProducts = CatInBagPrize::query()
            ->with('product')
            ->where('is_enabled', true)
            ->where('is_certificate', false)
            ->whereColumn('total_qty', '>', 'used_qty')
            ->get();

        $availableCertificates = CatInBagPrize::query()
            ->with('product')
            ->where('is_enabled', true)
            ->where('is_certificate', true)
            ->whereColumn('total_qty', '>', 'used_qty')
            ->get();

        $uniqueProducts = $availableProducts
            ->when(!empty($usedProductIds), function ($query) use ($usedProductIds) {
                return $query->whereNotIn('product_id', $usedProductIds);
            })
            ->shuffle()
            ->values();

        $uniqueCertificates = $availableCertificates
            ->when(!empty($usedProductIds), function ($query) use ($usedProductIds) {
                return $query->whereNotIn('product_id', $usedProductIds);
            })
            ->shuffle()
            ->values();

        foreach ($emptyBags as $bag) {
            $picked = $uniqueProducts->shift();
            if (!$picked && $certCount < 1) {
                $picked = $uniqueCertificates->shift();
                if ($picked) {
                    $certCount++;
                }
            }

            if (!$picked && $availableProducts->isNotEmpty()) {
                $picked = $availableProducts->shuffle()->first();
            }

            if (!$picked && $availableCertificates->isNotEmpty()) {
                $picked = $availableCertificates->shuffle()->first();
            }

            if (!$picked) {
                $bag->prize_type = 'empty';
                $bag->save();
                continue;
            }

            $bag->prize_id = $picked->id;
            $bag->product_id = $picked->product_id;
            $bag->prize_type = $picked->is_certificate ? 'certificate' : 'product';
            $bag->nominal = $picked->product?->price ?? null;
            $bag->data = [
                'category_id' => $picked->category_id,
                'is_golden' => (bool)$picked->is_golden,
                'is_certificate' => (bool)$picked->is_certificate,
            ];
            $bag->save();

            $this->ensureGiftCode($bag);

            if ($picked->product_id) {
                $usedProductIds[] = $picked->product_id;
            }
            CatInBagPrize::query()->where('id', $picked->id)->increment('used_qty');
        }
    }

    private function createGiftVoucherIfNeeded(CatInBagBag $bag, CatInBagSession $session): void
    {
        if ($bag->prize_type !== 'certificate') {
            return;
        }

        $data = $bag->data;
        if (!is_array($data)) {
            $data = [];
        }

        if (!empty($data['voucher_id']) || !empty($data['voucher_code'])) {
            return;
        }

        $amount = (int)($bag->nominal ?? 0);
        if ($amount <= 0) {
            return;
        }

        $preferredCode = $data['gift_code'] ?? null;
        $voucher = $this->createGiftVoucher($amount, $session->order_id, $bag->id, $preferredCode);
        if (!$voucher) {
            return;
        }

        $data['voucher_id'] = $voucher->id;
        $data['voucher_code'] = $voucher->code;
        $bag->data = $data;
        $bag->save();
    }

    private function ensureGiftCode(CatInBagBag $bag): void
    {
        $data = $bag->data;
        if (!is_array($data)) {
            $data = [];
        }

        if (!empty($data['gift_code'])) {
            return;
        }

        if ($bag->prize_type === 'certificate' && !empty($data['voucher_code'])) {
            $data['gift_code'] = $data['voucher_code'];
            $bag->data = $data;
            $bag->save();
            return;
        }

        $maxAttempts = 20;
        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $code = getCode(12);
            $exists = CatInBagBag::query()
                ->where('data->gift_code', $code)
                ->exists();
            if (!$exists) {
                $data['gift_code'] = $code;
                $bag->data = $data;
                $bag->save();
                return;
            }
        }
    }

    private function createGiftVoucher(int $amount, ?int $orderId, int $bagId, ?string $preferredCode = null): ?Voucher
    {
        $code = null;
        if ($preferredCode) {
            $exists = Voucher::query()->where('code', $preferredCode)->exists();
            if (!$exists) {
                $code = $preferredCode;
            }
        }
        if (!$code) {
            $code = $this->generateVoucherCode();
        }
        if (!$code) {
            return null;
        }

        return Voucher::create([
            'code' => $code,
            'type' => 1,
            'amount' => $amount,
            'count' => 1,
            'order_id' => $orderId,
            'is_gift' => true,
            'options' => [
                'source' => 'cat_in_bag',
                'cat_in_bag_bag_id' => $bagId,
                'created_at' => now()->toDateTimeString(),
            ],
        ]);
    }

    private function generateVoucherCode(): ?string
    {
        $maxAttempts = 20;
        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $code = getCode(4, true) . '-' . getCode(4, true) . '-' . getCode(4, true);
            if (!Voucher::query()->where('code', $code)->exists()) {
                return $code;
            }
        }
        return null;
    }

    private function validateBagAssignment(array $results, CatInBagSession $session, array $visibleCategoryIds): bool
    {
        $bagCount = (int)$session->bag_count;
        if (count($results) !== $bagCount) {
            return false;
        }

        $productIds = [];
        $productCategoryIds = [];
        $visibleProductCount = 0;
        $nonVisibleProductCount = 0;
        $certCount = 0;

        foreach ($results as $result) {
            if (empty($result['prize_id']) || empty($result['product_id']) || empty($result['type'])) {
                return false;
            }

            $productIds[] = (int)$result['product_id'];

            if ($result['type'] === 'certificate') {
                $certCount++;
                continue;
            }

            $categoryId = (int)($result['category_id'] ?? 0);
            if ($categoryId > 0) {
                $productCategoryIds[] = $categoryId;
                if (in_array($categoryId, $visibleCategoryIds, true)) {
                    $visibleProductCount++;
                } else {
                    $nonVisibleProductCount++;
                }
            }
        }

        if (count(array_unique($productIds)) !== count($productIds)) {
            return false;
        }

        $uniqueCategories = array_unique($productCategoryIds);
        $hasDuplicateCategories = count($uniqueCategories) !== count($productCategoryIds);

        if ($bagCount === 4) {
            if ($certCount > 1) {
                return false;
            }
            if (($nonVisibleProductCount + $certCount) > 1) {
                return false;
            }
            if ($visibleProductCount < 2) {
                return false;
            }
            if ($hasDuplicateCategories) {
                return false;
            }
        } elseif ($bagCount === 3 && (int)$session->open_limit === 2) {
            if ($certCount > 1) {
                return false;
            }
            if ($certCount === 1 && $nonVisibleProductCount > 0) {
                return false;
            }
            if ($certCount === 0 && $nonVisibleProductCount > 1) {
                return false;
            }
            if ($hasDuplicateCategories) {
                return false;
            }
        }

        return true;
    }

    private function makeSeededRand(CatInBagSession $session): callable
    {
        $seed = (int)($session->data['seed'] ?? 1);
        $state = $seed;

        return function () use (&$state): float {
            $state = (int)(($state * 1103515245 + 12345) & 0x7fffffff);
            return ($state % 1000000) / 1000000;
        };
    }

    private function getTier(int $total): ?array
    {
        if ($total >= 10000) {
            return [
                'bag_count' => 4,
                'open_limit' => 3,
                'bag_types' => ['normal', 'normal', 'normal', 'golden'],
            ];
        }

        if ($total >= 6500) {
            return [
                'bag_count' => 3,
                'open_limit' => 2,
                'bag_types' => ['normal', 'normal', 'normal'],
            ];
        }

        if ($total >= 4000) {
            return [
                'bag_count' => 3,
                'open_limit' => 1,
                'bag_types' => ['normal', 'normal', 'normal'],
            ];
        }

        return null;
    }
}
