<?php

namespace App\Http\Controllers\CatInBag;

use App\Http\Controllers\Controller;
use App\Models\CatInBagBag;
use App\Models\CatInBagCategory;
use App\Models\CatInBagPrize;
use App\Models\CatInBagSession;
use App\Models\Order;
use App\Services\CatInBag\GameService;
use App\Services\CatInBag\PreviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GameController extends Controller
{
    private function parsePriceToInt($value): int
    {
        if (is_int($value)) {
            return $value;
        }
        if (is_float($value)) {
            return (int)round($value);
        }
        if (is_numeric($value)) {
            return (int)round((float)$value);
        }
        $normalized = str_replace([' ', '₽'], '', (string)$value);
        $normalized = str_replace(',', '.', $normalized);
        return (int)round((float)$normalized);
    }

    private function resolveGoodsTotal(Order $order, array $orderData): int
    {
        $goodsTotal = (int)($orderData['total'] ?? 0);
        $cartTotal = 0;
        if (!empty($order->data_cart) && is_array($order->data_cart)) {
            foreach ($order->data_cart as $item) {
                $price = $this->parsePriceToInt($item['price'] ?? 0);
                $qty = (int)($item['qty'] ?? 1);
                $cartTotal += $price * $qty;
            }
        }

        $goodsTotal = max($goodsTotal, $cartTotal);
        if ($goodsTotal <= 0 && isset($order->amount)) {
            $goodsTotal = (int)$order->amount;
        }
        return $goodsTotal;
    }

    private function resolveTier(int $total): ?array
    {
        if ($total >= 10000) {
            return ['bag_count' => 4, 'open_limit' => 3];
        }

        if ($total >= 6500) {
            return ['bag_count' => 3, 'open_limit' => 2];
        }

        if ($total >= 4000) {
            return ['bag_count' => 3, 'open_limit' => 1];
        }

        return null;
    }

    public function session(Request $request, Order $order): JsonResponse
    {
        if (!getSettings('catInBag')) {
            return response()->json([
                'session' => null,
                'bags' => [],
                'visible_categories' => [],
            ]);
        }

        if ($request->user() && $order->user_id && $request->user()->id !== (int)$order->user_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $session = CatInBagSession::query()
            ->where('order_id', $order->id)
            ->with(['bags.prize', 'bags.product'])
            ->first();

        $orderData = $order->data ?? [];
        $participated = true;
        $isVoucherOrder = (bool)($orderData['is_voucher'] ?? false);
        $total = $this->resolveGoodsTotal($order, $orderData);
        $expectedTier = $this->resolveTier($total);
        $visibleCategoryIds = $orderData['cat_in_bag_visible_category_ids'] ?? [];
        if (count($visibleCategoryIds) < 2) {
            $previewData = app(PreviewService::class)->getPreview($request);
            $visibleCategoryIds = $previewData['preview']?->category_ids ?? [];
        }

        if ($session && $participated && !$isVoucherOrder && $expectedTier) {
            if (empty($session->visible_category_ids) && count($visibleCategoryIds) >= 2) {
                $session->update(['visible_category_ids' => array_values($visibleCategoryIds)]);
                $session->refresh();
            }

            if (
                (int)$session->opened_count === 0
                && (
                    (int)$session->bag_count !== (int)$expectedTier['bag_count']
                    || (int)$session->open_limit !== (int)$expectedTier['open_limit']
                )
                && count($visibleCategoryIds) >= 2
            ) {
                foreach ($session->bags as $bag) {
                    if ($bag->prize_id) {
                        CatInBagPrize::query()
                            ->where('id', $bag->prize_id)
                            ->where('used_qty', '>', 0)
                            ->decrement('used_qty');
                    }
                }
                $session->bags()->delete();
                $session->delete();
                $session = null;
            }
        }

        if (!$session) {
            if ($participated && !$isVoucherOrder && $expectedTier && count($visibleCategoryIds) >= 2) {
                $session = app(GameService::class)->createSession($total, $order->id, $order->user_id, $visibleCategoryIds);
                if ($session) {
                    $session->load(['bags.prize', 'bags.product']);
                }
            }
        }

        if (!$session) {
            return response()->json([
                'session' => null,
                'bags' => [],
                'visible_categories' => [],
            ]);
        }

        $categories = [];
        if (!empty($session->visible_category_ids)) {
            $categories = CatInBagCategory::query()
                ->dontCache()
                ->whereIn('id', $session->visible_category_ids)
                ->get()
                ->map(function (CatInBagCategory $category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'image' => $category->data['image']['img'] ?? null,
                        'image_thumb' => $category->data['image']['thumb'] ?? $category->data['image']['img'] ?? null,
                    ];
                })
                ->values();
        }

        $bags = $session->bags->map(function (CatInBagBag $bag) {
            $isOpened = (bool)$bag->opened_at;
            $prize = null;
            if ($isOpened && $bag->prize_id) {
                $prizeModel = $bag->prize;
                if ($prizeModel) {
                    $image = $prizeModel->data['image']['img']
                        ?? $prizeModel->data['image']['thumb']
                        ?? $prizeModel->image
                        ?? null;
                    $prize = [
                        'id' => $prizeModel->id,
                        'name' => $prizeModel->name ?? $prizeModel->product?->name,
                        'image' => $image,
                    ];
                }
            }
            return [
                'id' => $bag->id,
                'position' => $bag->position,
                'type' => $bag->type,
                'opened_at' => optional($bag->opened_at)->toDateTimeString(),
                'prize_type' => $isOpened ? $bag->prize_type : null,
                'nominal' => $isOpened ? $bag->nominal : null,
                'data' => $isOpened ? $bag->data : null,
                'prize' => $prize,
            ];
        })->values();

        return response()->json([
            'session' => [
                'id' => $session->id,
                'total' => $session->total,
                'bag_count' => $session->bag_count,
                'open_limit' => $session->open_limit,
                'opened_count' => $session->opened_count,
                'status' => $session->status,
            ],
            'bags' => $bags,
            'visible_categories' => $categories,
            'assignment' => $session->data['bag_assignment'] ?? null,
        ]);
    }

    public function openBag(Request $request, Order $order, CatInBagBag $bag, GameService $service): JsonResponse
    {
        if (!getSettings('catInBag')) {
            return response()->json(['message' => 'Promo disabled'], 403);
        }

        if ($request->user() && $order->user_id && $request->user()->id !== (int)$order->user_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $session = CatInBagSession::query()
            ->where('order_id', $order->id)
            ->first();

        if (!$session || $bag->session_id !== $session->id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $bag = $service->openBag($bag)->load('session');

        $prize = null;
        if ($bag->prize_id) {
            $prizeModel = CatInBagPrize::query()->with('product')->find($bag->prize_id);
            if ($prizeModel) {
                $image = $prizeModel->data['image']['img'] ?? $prizeModel->data['image']['thumb'] ?? $prizeModel->image ?? null;
                $prize = [
                    'id' => $prizeModel->id,
                    'name' => $prizeModel->name ?? $prizeModel->product?->name,
                    'image' => $image,
                ];
            }
        }

        return response()->json([
            'bag' => [
                'id' => $bag->id,
                'position' => $bag->position,
                'type' => $bag->type,
                'opened_at' => optional($bag->opened_at)->toDateTimeString(),
                'prize_type' => $bag->prize_type,
                'nominal' => $bag->nominal,
                'data' => $bag->data,
                'prize' => $prize,
            ],
            'session' => [
                'opened_count' => $bag->session?->opened_count ?? $session->opened_count,
                'open_limit' => $bag->session?->open_limit ?? $session->open_limit,
                'status' => $bag->session?->status ?? $session->status,
            ],
        ]);
    }

    public function saveAssignment(Request $request, Order $order): JsonResponse
    {
        if (!getSettings('catInBag')) {
            return response()->json(['message' => 'Promo disabled'], 403);
        }

        if ($request->user() && $order->user_id && $request->user()->id !== (int)$order->user_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $session = CatInBagSession::query()
            ->where('order_id', $order->id)
            ->with('bags')
            ->first();

        if (!$session) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $assignments = $request->input('assignments');
        if (!is_array($assignments)) {
            return response()->json(['message' => 'Invalid assignments'], 422);
        }

        $bagIds = $session->bags->pluck('id')->map(fn ($id) => (string)$id)->all();
        $assignmentsStr = array_map('strval', $assignments);
        $assignmentsStr = array_values(array_filter($assignmentsStr, fn ($id) => $id !== ''));

        if (count($assignmentsStr) !== count($bagIds)) {
            return response()->json(['message' => 'Invalid assignments'], 422);
        }

        if (count(array_unique($assignmentsStr)) !== count($assignmentsStr)) {
            return response()->json(['message' => 'Invalid assignments'], 422);
        }

        if (count(array_diff($assignmentsStr, $bagIds)) > 0) {
            return response()->json(['message' => 'Invalid assignments'], 422);
        }

        $data = is_array($session->data) ? $session->data : [];
        if (!empty($data['bag_assignment'])) {
            return response()->json([
                'assignment' => $data['bag_assignment'],
            ]);
        }

        $data['bag_assignment'] = $assignmentsStr;
        $session->data = $data;
        $session->save();

        return response()->json([
            'assignment' => $data['bag_assignment'],
        ]);
    }
}
