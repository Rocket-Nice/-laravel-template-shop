<?php

namespace App\Http\Controllers\Admin\CatInBag;

use App\Http\Controllers\Controller;
use App\Models\CatInBagCategory;
use App\Models\CatInBagPrize;
use App\Models\Product;
use App\Models\ProductType;
use App\Services\CompressModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrizeController extends Controller
{
    public function activePrizes()
    {
        $prizes = CatInBagPrize::query()
            ->with('product')
            ->where('is_enabled', true);

        if (request()->keyword) {
            $keyword = trim(request()->keyword);
            $prizes->where(function ($query) use ($keyword) {
                $keyword = mb_strtolower($keyword);
                $query->where(DB::raw('lower(name)'), 'like', '%' . $keyword . '%');
                return $query;
            });
        }

        $prizes = $prizes->orderBy('created_at', 'desc')->paginate(50);

        $seo = [
            'title' => 'Активные подарки «Кот в мешке»'
        ];

        return view('template.admin.cat_in_bag.prizes.active', compact('prizes', 'seo'));
    }

    public function index()
    {
        $prizes = CatInBagPrize::query()->with(['category', 'product']);

        if (request()->keyword) {
            $keyword = trim(request()->keyword);
            $prizes->where(function ($query) use ($keyword) {
                $keyword = mb_strtolower($keyword);
                $query->where(DB::raw('lower(name)'), 'like', '%' . $keyword . '%');
                return $query;
            });
        }

        if (request()->filled('category_id')) {
            $prizes->where('category_id', request()->category_id);
        }

        if (request()->filled('is_enabled')) {
            $prizes->where('is_enabled', (bool)request()->is_enabled);
        }

        if (request()->filled('is_golden')) {
            $prizes->where('is_golden', (bool)request()->is_golden);
        }

        if (request()->filled('is_certificate')) {
            $prizes->where('is_certificate', (bool)request()->is_certificate);
        }

        if (request()->filled('availability')) {
            if (request()->availability === 'available') {
                $prizes->whereColumn('total_qty', '>', 'used_qty');
            } elseif (request()->availability === 'empty') {
                $prizes->whereColumn('total_qty', '<=', 'used_qty');
            }
        }

        $prizes = $prizes->orderBy('created_at', 'desc')->paginate(50);

        $categories = CatInBagCategory::orderBy('name')->get();

        $seo = [
            'title' => 'Подарки «Кот в мешке»'
        ];

        return view('template.admin.cat_in_bag.prizes.index', compact('prizes', 'categories', 'seo'));
    }

    public function create()
    {
        $working_dir = '/shares/cat_in_bag/prizes';
        if (!file_exists(storage_path('app/public/photos' . $working_dir))) {
            mkdir(storage_path('app/public/photos' . $working_dir), 0777, true);
        }

        $categories = CatInBagCategory::orderBy('name')->get();
        $productTypes = ProductType::orderBy('name')->get();
        $products = Product::select('id', 'sku', 'name', 'type_id')->orderBy('name')->get();

        $seo = [
            'title' => 'Добавить подарок'
        ];

        return view('template.admin.cat_in_bag.prizes.create', compact('seo', 'working_dir', 'categories', 'products', 'productTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'total_qty' => 'required|integer|min:0',
            'category_id' => 'required|exists:cat_in_bag_categories,id',
            'product_id' => 'required|exists:products,id',
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

        CatInBagPrize::create([
            'name' => $request->name,
            'image' => $image['img'] ?? null,
            'total_qty' => $request->total_qty,
            'used_qty' => 0,
            'category_id' => $request->category_id,
            'product_id' => $request->product_id,
            'is_enabled' => $request->has('is_enabled'),
            'is_golden' => $request->has('is_golden'),
            'is_certificate' => $request->has('is_certificate'),
            'data' => [
                'image' => $image,
            ],
        ]);

        CatInBagPrize::flushQueryCache();

        return redirect()->route('admin.cat-in-bag.prizes.index')->with([
            'success' => 'Подарок успешно создан'
        ]);
    }

    public function edit(CatInBagPrize $prize)
    {
        $working_dir = '/shares/cat_in_bag/prizes';
        if (!file_exists(storage_path('app/public/photos' . $working_dir))) {
            mkdir(storage_path('app/public/photos' . $working_dir), 0777, true);
        }

        $categories = CatInBagCategory::orderBy('name')->get();
        $productTypes = ProductType::orderBy('name')->get();
        $products = Product::select('id', 'sku', 'name', 'type_id')->orderBy('name')->get();

        $seo = [
            'title' => 'Редактировать ' . $prize->name
        ];

        return view('template.admin.cat_in_bag.prizes.edit', compact('seo', 'working_dir', 'categories', 'products', 'productTypes', 'prize'));
    }

    public function update(Request $request, CatInBagPrize $prize)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'total_qty' => 'required|integer|min:0',
            'category_id' => 'required|exists:cat_in_bag_categories,id',
            'product_id' => 'required|exists:products,id',
            'image.img' => 'nullable|string',
            'image.thumb' => 'nullable|string',
        ]);

        $image = $request->image ?? null;
        $data = $prize->data ?? [];

        if ($image) {
            if (isset($image['img'])) {
                if (!isset($image['thumb']) || !$image['thumb']) {
                    $image['thumb'] = $image['img'];
                }
                $image['size'] = CompressModule::compressImage($image['img'], [200, 480, 768, 1200, 1920], 480);
            }
            $data['image'] = $image;
        }

        $prize->update([
            'name' => $request->name,
            'image' => $image['img'] ?? $prize->image,
            'total_qty' => $request->total_qty,
            'category_id' => $request->category_id,
            'product_id' => $request->product_id,
            'is_enabled' => $request->has('is_enabled'),
            'is_golden' => $request->has('is_golden'),
            'is_certificate' => $request->has('is_certificate'),
            'data' => $data,
        ]);

        CatInBagPrize::flushQueryCache();

        return redirect()->route('admin.cat-in-bag.prizes.index')->with([
            'success' => 'Подарок успешно обновлен'
        ]);
    }
}
