<?php

namespace App\Services\CatInBag;

use App\Models\CatInBagCategory;
use App\Models\CatInBagPreview;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PreviewService
{
    public const COOKIE_NAME = 'cat_in_bag_preview';
    public const REFRESH_LIMIT = 3;

    /**
     * @return array{preview: ?CatInBagPreview, guest_key: ?string, expires_at: ?Carbon}
     */
    public function getPreview(Request $request, bool $refresh = false): array
    {
        $now = now();
        $guestKey = $request->cookie(self::COOKIE_NAME);
        $user = $request->user();

        $preview = null;
        if ($user) {
            $preview = CatInBagPreview::query()
                ->where('user_id', $user->id)
                ->where('expires_at', '>', $now)
                ->orderByDesc('id')
                ->first();
        }

        if (!$preview && $guestKey) {
            $preview = CatInBagPreview::query()
                ->where('guest_key', $guestKey)
                ->where('expires_at', '>', $now)
                ->orderByDesc('id')
                ->first();

            if ($preview && $user) {
                $preview->update([
                    'user_id' => $user->id,
                ]);
            }
        }

        if ($preview) {
            if ($refresh && $preview->refresh_count < self::REFRESH_LIMIT - 1) {
                $categoryIds = $this->pickCategoryIds();
                if (count($categoryIds) >= 2) {
                    $preview->update([
                        'category_ids' => $categoryIds,
                        'refresh_count' => $preview->refresh_count + 1,
                    ]);
                }
            }
            return [
                'preview' => $preview,
                'guest_key' => $guestKey,
                'expires_at' => $preview->expires_at,
            ];
        }

        $categoryIds = $this->pickCategoryIds();
        if (count($categoryIds) < 2) {
            return [
                'preview' => null,
                'guest_key' => $guestKey,
                'expires_at' => null,
            ];
        }

        $expiresAt = $this->getPreviewExpiresAt($now);
        $attributes = [
            'category_ids' => $categoryIds,
            'refresh_count' => 0,
            'expires_at' => $expiresAt,
        ];

        if ($user) {
            $attributes['user_id'] = $user->id;
        } else {
            $guestKey = $guestKey ?: Str::uuid()->toString();
            $attributes['guest_key'] = $guestKey;
        }

        $preview = CatInBagPreview::create($attributes);

        return [
            'preview' => $preview,
            'guest_key' => $guestKey,
            'expires_at' => $expiresAt,
        ];
    }

    /**
     * @return array<int, int>
     */
    private function pickCategoryIds(): array
    {
        $query = CatInBagCategory::query()
            ->dontCache()
            ->where('is_enabled', true)
            ->whereHas('prizes', function ($prizes) {
                $prizes->where('is_enabled', true)
                    ->whereColumn('total_qty', '>', 'used_qty');
            })
            ->inRandomOrder()
            ->limit(2);

        $categoryIds = $query->pluck('id')->all();
        if (count($categoryIds) >= 2) {
            return $categoryIds;
        }

        return CatInBagCategory::query()
            ->dontCache()
            ->where('is_enabled', true)
            ->inRandomOrder()
            ->limit(2)
            ->pluck('id')
            ->all();
    }

    private function getPreviewExpiresAt(Carbon $now): Carbon
    {
        return $now->copy()->addDay()->startOfDay();
    }
}
