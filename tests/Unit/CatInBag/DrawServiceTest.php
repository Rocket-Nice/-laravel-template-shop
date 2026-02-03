<?php

namespace Tests\Unit\CatInBag;

use App\Models\CatInBagPrize;
use App\Models\Product;
use App\Services\CatInBag\DrawService;
use Illuminate\Support\Collection;
use Tests\TestCase;

class DrawServiceTest extends TestCase
{
    private array $visibleCategoryIds = [];
    private Collection $prizes;

    protected function setUp(): void
    {
        parent::setUp();
        //mt_srand(1234);
        $this->prizes = $this->buildPrizes();
    }

    private function buildPrizes(): Collection
    {
        $this->visibleCategoryIds = [1, 2];
        $prizes = [];

        $productIndex = 1;
        for ($categoryId = 1; $categoryId <= 3; $categoryId++) {
            for ($i = 1; $i <= 6; $i++) {
                $product = $this->makeProduct($productIndex, 500 + $i);
                $prize = $this->makePrize($productIndex, $categoryId, $product, $i === 1, false);
                $prizes[] = $prize;
                $productIndex++;
            }
        }

        foreach ([750, 1000, 1500, 2000] as $amount) {
            $product = $this->makeProduct($productIndex, $amount);
            $prize = $this->makePrize($productIndex, 1, $product, false, true);
            $prizes[] = $prize;
            $productIndex++;
        }

        return collect($prizes);
    }

    private function makeProduct(int $id, int $price): Product
    {
        $product = new Product();
        $product->id = $id;
        $product->price = $price;
        return $product;
    }

    private function makePrize(int $id, int $categoryId, Product $product, bool $isGolden, bool $isCertificate): CatInBagPrize
    {
        $prize = new CatInBagPrize();
        $prize->id = $id;
        $prize->name = $isCertificate ? 'Сертификат ' . $product->price : 'Приз ' . $id;
        $prize->image = null;
        $prize->total_qty = 100;
        $prize->used_qty = 0;
        $prize->category_id = $categoryId;
        $prize->product_id = $product->id;
        $prize->is_enabled = true;
        $prize->is_golden = $isGolden;
        $prize->is_certificate = $isCertificate;
        $prize->data = null;
        $prize->setRelation('product', $product);
        return $prize;
    }

    public function testTier1Distribution(): void
    {
        $service = new DrawService(null, $this->prizes);

        $counts = [
            'product' => 0,
            'cert_750' => 0,
            'cert_1000' => 0,
        ];

        for ($i = 0; $i < 10000; $i++) {
            $result = $service->draw(5000, $this->visibleCategoryIds, ['normal']);
            $this->assertCount(1, $result);
            $outcome = $result[0];
            if ($outcome['type'] === 'product') {
                $counts['product']++;
            } else {
                if ((int)$outcome['nominal'] === 750) {
                    $counts['cert_750']++;
                } elseif ((int)$outcome['nominal'] === 1000) {
                    $counts['cert_1000']++;
                }
            }
        }

        $this->printTable('Tier1 (4000-6499)', $counts);

        $this->assertRatio($counts['product'], 0.80, 10000, 0.03);
        $this->assertRatio($counts['cert_750'], 0.15, 10000, 0.03);
        $this->assertRatio($counts['cert_1000'], 0.05, 10000, 0.03);
    }

    public function testTier2Constraints(): void
    {
        $service = new DrawService(null, $this->prizes);
        $counts = [
            'product' => 0,
            'cert_1000' => 0,
            'cert_1500' => 0,
        ];

        for ($i = 0; $i < 10000; $i++) {
            $result = $service->draw(7000, $this->visibleCategoryIds, ['normal', 'normal']);
            $this->assertCount(2, $result);

            foreach ($result as $row) {
                if ($row['type'] === 'product') {
                    $counts['product']++;
                    continue;
                }
                if ((int)$row['nominal'] === 1000) {
                    $counts['cert_1000']++;
                } elseif ((int)$row['nominal'] === 1500) {
                    $counts['cert_1500']++;
                }
            }

            $productIds = array_column($result, 'product_id');
            $this->assertCount(count(array_unique($productIds)), $productIds);

            $certCount = count(array_filter($result, fn($row) => $row['type'] === 'certificate'));
            $this->assertLessThanOrEqual(1, $certCount);

            $products = array_filter($result, fn($row) => $row['type'] === 'product');
            if (count($products) === 2) {
                $categories = array_unique(array_column($products, 'category_id'));
                $this->assertCount(2, $categories);
            }

            if (!empty($products)) {
                $visibleHits = array_filter($products, fn($row) => in_array($row['category_id'], $this->visibleCategoryIds, true));
                $this->assertNotEmpty($visibleHits);
            }
        }

        $this->printTable('Tier2 (6500-9999)', $counts);
    }

    public function testTier3GoldenDistributionAndConstraints(): void
    {
        $service = new DrawService(null, $this->prizes);
        $counts = [
            'product' => 0,
            'cert_1500' => 0,
            'cert_2000' => 0,
        ];

        $goldenHits = 0;
        $goldenTotal = 0;

        for ($i = 0; $i < 10000; $i++) {
            $result = $service->draw(12000, $this->visibleCategoryIds, ['golden', 'normal', 'normal']);
            $this->assertCount(3, $result);

            foreach ($result as $row) {
                if ($row['type'] === 'product') {
                    $counts['product']++;
                } elseif ((int)$row['nominal'] === 1500) {
                    $counts['cert_1500']++;
                } elseif ((int)$row['nominal'] === 2000) {
                    $counts['cert_2000']++;
                }
            }

            $productIds = array_column($result, 'product_id');
            $this->assertCount(count(array_unique($productIds)), $productIds);

            $certCount = count(array_filter($result, fn($row) => $row['type'] === 'certificate'));
            $this->assertLessThanOrEqual(1, $certCount);

            $visibleProducts = array_filter($result, fn($row) => $row['type'] === 'product' && in_array($row['category_id'], $this->visibleCategoryIds, true));
            $this->assertGreaterThanOrEqual(2, count($visibleProducts));

            foreach ($result as $row) {
                if ($row['bag_type'] === 'golden') {
                    $goldenTotal++;
                    if ($row['type'] === 'product' && $row['is_golden']) {
                        $goldenHits++;
                    }
                }
            }
        }

        $this->printTable('Tier3 (>=10000)', $counts);
        $this->printSimpleRatio('Golden bag product (is_golden)', $goldenHits, $goldenTotal);

        $this->assertRatio($goldenHits, 0.60, $goldenTotal, 0.05);
    }

    private function assertRatio(int $actualCount, float $expectedRatio, int $total, float $tolerance): void
    {
        $actualRatio = $actualCount / $total;
        $this->assertTrue(
            abs($actualRatio - $expectedRatio) <= $tolerance,
            'Expected ratio ' . $expectedRatio . ' +/- ' . $tolerance . ', got ' . $actualRatio
        );
    }

    private function printTable(string $title, array $counts): void
    {
        $total = array_sum($counts);
        $rows = [];
        foreach ($counts as $label => $count) {
            $ratio = $total > 0 ? $count / $total : 0;
            $rows[] = [
                'Outcome' => $label,
                'Count' => (string)$count,
                'Ratio' => number_format($ratio, 4, '.', ''),
            ];
        }

        $headers = ['Outcome', 'Count', 'Ratio'];
        $widths = [
            'Outcome' => strlen($headers[0]),
            'Count' => strlen($headers[1]),
            'Ratio' => strlen($headers[2]),
        ];

        foreach ($rows as $row) {
            foreach ($headers as $header) {
                $widths[$header] = max($widths[$header], strlen($row[$header]));
            }
        }

        $line = '+' . str_repeat('-', $widths['Outcome'] + 2)
            . '+' . str_repeat('-', $widths['Count'] + 2)
            . '+' . str_repeat('-', $widths['Ratio'] + 2) . "+\n";

        $out = "\n" . $title . "\n" . $line;
        $out .= '| ' . str_pad($headers[0], $widths['Outcome']) . ' | '
            . str_pad($headers[1], $widths['Count']) . ' | '
            . str_pad($headers[2], $widths['Ratio']) . " |\n";
        $out .= $line;
        foreach ($rows as $row) {
            $out .= '| ' . str_pad($row['Outcome'], $widths['Outcome']) . ' | '
                . str_pad($row['Count'], $widths['Count']) . ' | '
                . str_pad($row['Ratio'], $widths['Ratio']) . " |\n";
        }
        $out .= $line;
        fwrite(STDOUT, $out);
    }

    private function printSimpleRatio(string $label, int $count, int $total): void
    {
        $ratio = $total > 0 ? $count / $total : 0;
        $out = "\n" . $label . ': ' . $count . '/' . $total . ' (' . number_format($ratio, 4, '.', '') . ")\n";
        fwrite(STDOUT, $out);
    }
}
