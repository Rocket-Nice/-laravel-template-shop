<?php

namespace App\Http\Controllers\Admin\CatInBag;

use App\Http\Controllers\Controller;
use App\Models\CatInBagCategory;
use App\Services\CompressModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = CatInBagCategory::query();
        if (request()->keyword) {
            $keyword = trim(request()->keyword);
            $categories->where(function ($query) use ($keyword) {
                $keyword = mb_strtolower($keyword);
                $query->where(DB::raw('lower(name)'), 'like', '%' . $keyword . '%');
                return $query;
            });
        }

        $categories = $categories->orderBy('created_at', 'desc')->paginate(50);

        $seo = [
            'title' => 'Категории «Кот в мешке»'
        ];

        return view('template.admin.cat_in_bag.categories.index', compact('categories', 'seo'));
    }

    public function create()
    {
        $working_dir = '/shares/cat_in_bag/categories';
        if (!file_exists(storage_path('app/public/photos' . $working_dir))) {
            mkdir(storage_path('app/public/photos' . $working_dir), 0777, true);
        }

        $seo = [
            'title' => 'Добавить категорию'
        ];

        return view('template.admin.cat_in_bag.categories.create', compact('seo', 'working_dir'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image.img' => 'required|string',
            'image.thumb' => 'nullable|string',
        ]);

        $image = $request->image ?? null;
        if (isset($image['img'])) {
            if (!isset($image['thumb']) || !$image['thumb']) {
                $image['thumb'] = $image['img'];
            }
            $image['size'] = CompressModule::compressImage($image['img'], [200, 480, 768, 1200, 1920], 480);
        }

        CatInBagCategory::create([
            'name' => $request->name,
            'image' => $image['img'] ?? null,
            'data' => [
                'image' => $image,
            ],
            'is_enabled' => $request->has('is_enabled'),
        ]);

        CatInBagCategory::flushQueryCache();

        return redirect()->route('admin.cat-in-bag.categories.index')->with([
            'success' => 'Категория успешно добавлена'
        ]);
    }

    public function edit(CatInBagCategory $category)
    {
        $working_dir = '/shares/cat_in_bag/categories';
        if (!file_exists(storage_path('app/public/photos' . $working_dir))) {
            mkdir(storage_path('app/public/photos' . $working_dir), 0777, true);
        }

        $seo = [
            'title' => 'Изменить категорию'
        ];

        return view('template.admin.cat_in_bag.categories.edit', compact('seo', 'category', 'working_dir'));
    }

    public function update(Request $request, CatInBagCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image.img' => 'nullable|string',
            'image.thumb' => 'nullable|string',
        ]);

        $image = $request->image ?? null;
        $data = $category->data ?? [];

        if ($image) {
            if (isset($image['img'])) {
                if (!isset($image['thumb']) || !$image['thumb']) {
                    $image['thumb'] = $image['img'];
                }
                $image['size'] = CompressModule::compressImage($image['img'], [200, 480, 768, 1200, 1920], 480);
            }
            $data['image'] = $image;
        }

        $category->update([
            'name' => $request->name,
            'image' => $image['img'] ?? $category->image,
            'data' => $data,
            'is_enabled' => $request->has('is_enabled'),
        ]);

        CatInBagCategory::flushQueryCache();

        return redirect()->route('admin.cat-in-bag.categories.index')->with([
            'success' => 'Категория успешно обновлена'
        ]);
    }
}
