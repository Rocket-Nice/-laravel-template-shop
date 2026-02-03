@props(['total' => null])

@php
    $cartTotal = $total;
    if ($cartTotal === null) {
        $cartTotal = (float) \Gloudemans\Shoppingcart\Facades\Cart::instance('cart')->subtotal(0, '.', '');
    }
    $cartTotal = (float) $cartTotal;
@endphp

<div x-data="{
    total: {{ $cartTotal }},
    visible: false,
    init() {
        try {
            this.visible = localStorage.getItem('cat-popup-participated') === '1';
        } catch (e) {}
        window.addEventListener('cat-popup-participated', () => {
            this.visible = true;
        });
        this.updateFromDom();
        const tableCart = document.getElementById('table-cart');
        if (tableCart) {
            tableCart.addEventListener('updateCart', () => {
                this.updateFromDom();
            });
        }
    },
    updateFromDom() {
        const totalInput = document.getElementById('cart-total');
        if (totalInput && totalInput.value !== undefined) {
            const parsed = Number(totalInput.value);
            if (!Number.isNaN(parsed)) {
                this.total = parsed;
            }
        }
    },
    formatRub(value) {
        const formatter = new Intl.NumberFormat('ru-RU');
        return formatter.format(Math.max(0, Math.round(value))) + ' ₽';
    },
    get needTo4000() {
        return Math.max(0, 4000 - this.total);
    },
    get needTo6500() {
        return Math.max(0, 6500 - this.total);
    },
    get needTo10000() {
        return Math.max(0, 10000 - this.total);
    }
}" x-init="init()" x-show="visible" x-cloak
    class="w-2xl py-[32px] flex flex-col gap-[32px]">

    <div x-show="total < 4000" x-cloak
        class="relative bg-[#E3E6D8] border border-[#DDCFBB] pt-[4px] pb-[12px] flex gap-[10px] pl-[132px] pr-[8px] md:max-h-[71px] h-full">
        <div class="absolute -top-[4px] md:-top-5 -left-4">
            <img class="object-contain w-full h-full" src="{{ asset('img/cat-bag/cat-card-text.png') }}" alt="cat-card">
        </div>
        <div class="flex flex-col gap-[4px]">
            <p class="font-bold text-[16px] text-[#000000] ">
                Пополните корзину на
                <span class="text-[32px] italic text-[#B1908E] leading-none whitespace-nowrap"
                    x-text="formatRub(needTo4000)"></span>
            </p>
            <p class="font-[16px] text-base  leading-none">чтобы принять участие в акции «Кот в мешке»</p>
        </div>
    </div>

    <div x-show="total >= 4000 && total < 6500" x-cloak
        class="relative bg-[#E3E6D8] border border-[#DDCFBB] pt-[4px] pb-[12px] flex gap-[10px] pl-[132px] pr-[8px] md:max-h-[71px] h-full">
        <div class="absolute -top-[4px] md:-top-5 -left-4">
            <img class="object-contain w-full h-full" src="{{ asset('img/cat-bag/cat-card-text.png') }}" alt="cat-card">
        </div>
        <div class="flex flex-col gap-[4px]">

            <div class="flex items-end gap-[4px]">
                <p class="font-bold text-[16px] text-[#000000]">Доступен </p>
                <span class="text-[32px] font-bold italic text-[#B1908E] leading-none"> 1 </span>
                <div class="w-[20px] h-[20px] self-center">
                    <img class="object-contain" src="{{ asset('img/cat-bag/sack-icon.png') }}" alt="sack-icon">
                </div>
            </div>

            <div class="flex items-end gap-[4px]">
                <p class="font-[16px] text-base  leading-none">
                    Пополните корзину на <span x-text="formatRub(needTo6500)"></span> и заберите ещё 1
                    <img class="inline-block w-[20px] h-auto align-middle ml-1"
                        src="{{ asset('img/cat-bag/sack-icon.png') }}" alt="sack-icon">
                </p>

            </div>

        </div>
    </div>

    <div x-show="total >= 6500 && total < 10000" x-cloak
        class="relative bg-[#E3E6D8] border border-[#DDCFBB] pt-[4px] pb-[12px] flex gap-[10px] pl-[132px] pr-[8px] md:max-h-[71px] h-full">
        <div class="absolute -top-[4px] md:-top-5 -left-4">
            <img class="object-contain w-full h-full" src="{{ asset('img/cat-bag/cat-card-text.png') }}" alt="cat-card">
        </div>
        <div class="flex flex-col gap-[4px]">

            <div class="flex items-end gap-[4px]">
                <p class="font-bold text-[16px] text-[#000000]">Доступно </p>
                <span class="text-[32px] font-bold italic text-[#B1908E] leading-none"> 2 </span>
                <div class="w-[20px] h-[20px] self-center">
                    <img class="object-contain" src="{{ asset('img/cat-bag/sack-icon.png') }}" alt="sack-icon">
                </div>
            </div>

            <div class="flex items-end gap-[4px] ">
                <p class="font-[16px] text-base leading-none">
                    Пополните корзину на <span x-text="formatRub(needTo10000)"></span> и заберите +1 золотой
                    <img class="inline-block w-[20px] h-auto align-middle ml-1"
                        src="{{ asset('img/cat-bag/sack-icon-g.png') }}" alt="sack-icon">
                </p>
            </div>

        </div>
    </div>

    <div x-show="total >= 10000" x-cloak
        class="relative bg-[#E3E6D8] border border-[#DDCFBB] pt-[4px] pb-[12px] flex gap-[10px] pl-[132px] pr-[8px] md:max-h-[71px] h-full">
        <div class="absolute -top-[4px] md:-top-5 -left-4">
            <img class="object-contain w-full h-full" src="{{ asset('img/cat-bag/cat-card-text.png') }}" alt="cat-card">
        </div>
        <div class="flex flex-col gap-[4px]">

            <p class="font-bold text-[16px] text-[#000000] leading-none">
                Доступно
                <span class="text-[32px] font-bold italic text-[#B1908E]">3</span>
                <img class="inline-block w-[20px] align-middle mx-[4px]" src="{{ asset('img/cat-bag/sack-icon.png') }}"
                    alt="sack-icon">
                — один из них может быть золотым
            </p>

            <div class="flex items-end gap-[4px]">
                <p class="font-[16px] text-base  leading-none">Откройте мешочки и испытайте удачу!</p>
            </div>

        </div>
    </div>

</div>
