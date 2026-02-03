<div
    class="
    flex justify-center items-center
    bg-gradient-to-r from-[#E9EBE0] to-[#D8DACD]
    border border-[#EAEBE0]
    shadow-sm
    mx-[8px]
    sm:mx-0
">
    <div class="w-full sm:w-[72%] lg:w-[70%] flex items-center relative max-h-[132px] md:max-h-[127px] h-full">
        <picture class="sticky md:absolute top-[-35px] left-0">
            <source media="(max-width: 576px)" srcset="/img/cat-bag/cat-get-bag-mobile.png">
            <img src="/img/cat-bag/cat-get-bag.png" alt="cat" class="max-w-[96px] w-[96px] md:max-w-full md:w-auto h-auto">
        </picture>

        <div class="w-[128px] hidden md:block"></div>
        <div class="md:py-[25.5px] py-[16px] pl-0 md:pl-[8px] pr-[16px] md:pr-0 text-left">
            <p class="font-[700] text-[20px] leading-[24px] text-black mb-[12px]">
                У вас есть неоткрытые мешки с подарками!
            </p>
            <x-cat-bag-button class="md:max-w-[193px] w-full max-w-[216px] mx-auto md:mx-0" type="button"
                onclick="window.dispatchEvent(new CustomEvent('open-cat-bags'))">
                Получить подарки
            </x-cat-bag-button>
        </div>
    </div>
</div>
