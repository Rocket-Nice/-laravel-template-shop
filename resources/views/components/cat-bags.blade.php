@props([
    'bagCount' => 3,
    'categoryIds' => [],
    'orderId' => null,
    'orderSlug' => null,
    'openLimit' => null,
])
@php($hasGoldenBag = ((int)$bagCount) === 4)
@php($categories = !empty($categoryIds)
    ? \App\Models\CatInBagCategory::query()->whereIn('id', $categoryIds)->get()->keyBy('id')
    : collect())
@php($categoryImages = collect($categoryIds)->map(function ($id) use ($categories) {
    $category = $categories->get($id);
    if (!$category) {
        return null;
    }
    return $category->data['image']['img'] ?? $category->data['image']['thumb'] ?? $category->image;
})->filter()->values()->all())
@php($routeOrder = request()->route('order'))
@php($routeOrderId = $routeOrder instanceof \App\Models\Order ? $routeOrder->id : (is_numeric($routeOrder) ? $routeOrder : null))
@php($routeOrderSlug = $routeOrder instanceof \App\Models\Order ? $routeOrder->slug : (is_string($routeOrder) ? $routeOrder : null))
@php($orderIdValue = $orderId ? (string)$orderId : (string)($routeOrderId ?: request()->get('InvId', '')))
@php($orderSlugValue = $orderSlug ? (string)$orderSlug : (string)($routeOrderSlug ?: ''))
@php($openLimitValue = $openLimit !== null ? (string)$openLimit : '')

<div x-data="{
    open: false,
    lock() {
        document.body.classList.add('overflow-hidden')
    },
    unlock() {
        document.body.classList.remove('overflow-hidden')
    },
    reset() {
        this.open = false;
        this.unlock();
    }
}" x-init="window.addEventListener('open-cat-bags', () => {
    open = true;
    lock();
});

window.addEventListener('keydown', e => {
    if (e.key === 'Escape' && open) {
        reset();
    }
});" x-show="open" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center">

    <div class="absolute inset-0 bg-white" @click="reset()"></div>
    <button @click="reset()" class="absolute top-4 right-4 z-20">
        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd"
                d="M26.3647 27.0708L27.0718 26.3637L20.7079 19.9998L27.0718 13.6359L26.3647 12.9288L20.0008 19.2927L13.6368 12.9287L12.9297 13.6358L19.2937 19.9998L12.9297 26.3638L13.6368 27.071L20.0008 20.7069L26.3647 27.0708Z"
                fill="#010101" />
        </svg>
    </button>
    <div class="relative w-full max-w-[480px] flex flex-col bg-white">
        <div class="pb-6 font-[400] overflow-y-auto no-scrollbar">
            <div class="cat-container"
                 data-category-images='@json($categoryImages)'
                 data-order-id="{{ $orderIdValue }}"
                 data-order-slug="{{ $orderSlugValue }}"
                 data-open-limit="{{ $openLimitValue }}">
                <div class="sack-cat-wrapper">
                    <div class="sack-cat-container">
                        <div class="title-container">
                            <h3 class="title">КОТ В МЕШКЕ</h3>
                            <p class="subtitle font-inter_font">Запустите игру и испытайте удачу!</p>
                        </div>

                        <div class="sacks-animation-container font-inter_font {{ $hasGoldenBag ? 'has-four-cards' : '' }}">
                            <div class="sack-card">
                                <img src="/img/cat-bag/animation-sack/pre-sack1.png" alt="pre">
                                <p class="sack-product-text">Очень длинное название для этого продукта будет находиться
                                    в этом</p>
                            </div>
                            <div class="sack-card">
                                <img src="/img/cat-bag/animation-sack/pre-sack2.png" alt="pre">
                                <p class="sack-product-text">Очень длинное название для этого продукта будет находиться
                                    в этом</p>
                            </div>
                            <div class="sack-card">
                                <img src="/img/cat-bag/animation-sack/pre-sack3.png" alt="pre">
                                <p class="sack-product-text">Очень длинное название для этого продукта будет находиться
                                    в этом</p>
                            </div>
                            <!-- 4 мешок, по коду 2 варианта перемешивания -->
                            @if($hasGoldenBag)
                                <div class="sack-card">
                                    <img src="/img/cat-bag/animation-sack/pre-sack4.png" alt="pre">
                                    <p class="sack-product-text">Очень длинное название для этого продукта будет находиться
                                        в этом</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="sack-price-container">
                        <h3 class="prize-title">Мои подарки</h3>
                        <div class="sack-price-cards font-inter_font">

                        </div>
                    </div>
                </div>

                <div class="btn-info-container">
                    <p class="info-text font-inter_font">осталось <span>3</span> из <span>3</span> попыток</p>

                    <x-cat-bag-button class="start-btn">
                        Перемешать
                    </x-cat-bag-button>

                    <x-cat-bag-button class="cabinet-btn hidden mt-2" type="button"
                        onclick="window.location.href='/cabinet/orders'">
                        В личный кабинет
                    </x-cat-bag-button>
                </div>
            </div>
        </div>
    </div>
</div>
