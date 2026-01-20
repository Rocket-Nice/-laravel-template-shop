<div class="max-w-[480px] rounded-[10px] bg-[#F6EFF2] pt-6 relative font-inter_font">
    {{-- Кнопка закрытия --}}
    <button class="absolute top-4 right-4">
        <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g clip-path="url(#clip0_17619_6089)">
                <path
                    d="M28 13.9993C28 21.7308 21.7321 27.9987 14.0007 27.9987C6.26921 27.9987 0 21.7321 0 13.9993C0 6.26655 6.26788 0 13.9993 0C21.7308 0 27.9987 6.26788 27.9987 13.9993H28ZM26.2429 13.994C26.2429 7.23381 20.7622 1.75309 14.002 1.75309C7.24182 1.75309 1.76242 7.23381 1.76242 13.994C1.76242 20.7542 7.24315 26.2349 14.0033 26.2349C20.7635 26.2349 26.2442 20.7542 26.2442 13.994H26.2429Z"
                    fill="#C5C5C5" />
                <path
                    d="M18.7449 9.27229C19.1772 9.74458 19.0998 10.3396 18.6942 10.7959L15.5763 13.8458C15.5496 14.0139 15.6283 14.1126 15.7123 14.2407C16.3354 15.1893 18.4167 16.5261 18.9037 17.452C19.4213 18.438 18.4487 19.3225 17.4561 18.8996L14.122 15.5628L13.9206 15.5001L10.5131 18.8996C9.82604 19.1944 8.9108 18.7742 8.92948 17.9657C8.94949 17.0851 11.9047 14.7944 12.5144 13.9659L9.12694 10.5531C8.53724 9.62851 9.61657 8.61588 10.5665 9.11352C11.1842 9.43772 13.6831 12.4836 14.034 12.4222C15.0546 11.7578 16.4475 9.61383 17.4041 9.11352C17.8457 8.88271 18.3953 8.88805 18.7462 9.27095L18.7449 9.27229Z"
                    fill="#C5C5C5" />
            </g>
            <defs>
                <clipPath id="clip0_17619_6089">
                    <rect width="28" height="28" fill="white" />
                </clipPath>
            </defs>
        </svg>
    </button>

    {{-- Заголовок --}}
    <h2 class="text-center text-2xl font-medium leading-7 mb-4 px-4 uppercase font-main-font">
        Выберите подарки,<br>
        которые хотите забрать
    </h2>

    {{-- Подарки --}}
    <div class="grid grid-cols-4 mb-4 bg-white py-4">
        @php
            $gifts = [
                ['title' => 'Сыворотка из ассортимента', 'img' => 'img/cat-bag/gift-1.png'],
                ['title' => 'Крем из ассортимента', 'img' => 'img/cat-bag/gift-2.png'],
                ['title' => 'Кот в мешке', 'img' => 'img/cat-bag/gift-cat.png'],
                ['title' => 'Золотой мешок от 10 000 ₽', 'img' => 'img/cat-bag/gift-gold.png'],
            ];
        @endphp

        @foreach ($gifts as $gift)
            <label class="group cursor-pointer text-center">
                <input type="checkbox" class="hidden">
                <img src="{{ $gift['img'] }}" alt="" class="mx-auto mb-2 h-auto object-contain">
                <p class="text-[10px] text-[#414141] leading-3 font-light px-2">
                    {{ $gift['title'] }}
                </p>
            </label>
        @endforeach
    </div>

    {{-- Кнопки --}}
    <div class="flex flex-row gap-2 justify-center mb-4 px-4">
        {{-- Primary button --}}
        <x-cat-bag-button>
            Участвовать
        </x-cat-bag-button>

        {{-- Secondary button счетчиком --}}
        <x-cat-bag-button variant="outline" counter="1 из 3">
            Обновить
        </x-cat-bag-button>
    </div>

    {{-- Текст --}}
    <p class="text-center text-[12px] text-[#707070] mx-auto mb-2 leading-4 max-w-[220px]">
        После обновления предыдущие подарки не сохраняются
    </p>

    {{-- Инфа снизу --}}
    <div class="grid grid-cols-3 text-[13px] leading-tight text-[#414141] text-center">
        <div class="p-[22px] flex flex-row justify-center items-center">
            От 4 000 ₽ —<br>откроете 1 🎁
        </div>
        <div class="p-[22px] flex flex-row justify-center items-center">
            От 6 500 ₽ —<br>откроете 2 🎁
        </div>
        <div class="p-[22px] flex flex-row justify-center items-center">
            От 10 000 ₽ —<br>откроете 3 🎁<br>
            один из них может быть золотым
        </div>
    </div>
</div>
