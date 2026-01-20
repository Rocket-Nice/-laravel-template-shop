<div class="flex flex-col lg:gap-[40px] gap-[32px]">
    <div
        class="bg-[#D8DACD] relative flex gap-[20px] items-center pt-2 pb-2 pl-[85px] pr-[85px] md:pt-4 md:pb-4 md:pl-[88px] md:pr-[88px]">
        <div class="w-[88px] h-[88px] absolute -top-5 left-0">
            <img src="img/cat-bag/cat-single.png" alt="cat-single">
        </div>
        <p class="font-bold text-[16px] text-[#000000] leading-[20px] whitespace-nowrap md:whitespace-normal">
            Вам доступно участие
            <br class="md:hidden" />
            в акции «Кот в мешке»!
        </p>
    </div>
    <div class="flex gap-[8px]">
        {{-- Primary button --}}
        <x-cat-bag-button>
            Открыть мешки
        </x-cat-bag-button>

        {{-- Secondary button счетчиком --}}
        <x-cat-bag-button variant="outline">
            В личный кабинет
        </x-cat-bag-button>
    </div>
</div>
