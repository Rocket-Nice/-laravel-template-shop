<?php

namespace App\Http\Controllers\CatInBag;

use App\Http\Controllers\Controller;
use App\Models\CatInBagCategory;
use App\Services\CatInBag\PreviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PreviewController extends Controller
{
    public function categories(Request $request, PreviewService $service): JsonResponse
    {
        if (!getSettings('catInBag')) {
            return response()->json([
                'categories' => [],
                'expires_at' => null,
                'refresh_count' => 0,
                'refresh_limit' => PreviewService::REFRESH_LIMIT,
            ]);
        }

        $result = $service->getPreview($request, $request->boolean('refresh'));
        $preview = $result['preview'];

        if (!$preview) {
            return response()->json([
                'categories' => [],
                'expires_at' => null,
                'refresh_count' => 0,
                'refresh_limit' => PreviewService::REFRESH_LIMIT,
            ]);
        }

        $categories = CatInBagCategory::query()
            ->whereIn('id', $preview->category_ids ?? [])
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

        $response = response()->json([
            'categories' => $categories,
            'expires_at' => optional($result['expires_at'])->toDateTimeString(),
            'refresh_count' => $preview->refresh_count ?? 0,
            'refresh_limit' => PreviewService::REFRESH_LIMIT,
        ]);

        if (!$request->user() && $result['guest_key'] && $result['expires_at']) {
            $seconds = max(0, $result['expires_at']->timestamp - now()->timestamp);
            $response->withCookie(cookie(PreviewService::COOKIE_NAME, $result['guest_key'], (int)ceil($seconds / 60)));
        }

        return $response;
    }
}
