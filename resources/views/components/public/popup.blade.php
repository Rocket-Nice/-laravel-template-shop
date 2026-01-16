@props(['alertButtonText' => false])
@props(['alertButtonLink' => false])
@props(['alertCloseButtonText' => false])
<div>
    <div {{ $attributes->merge(['class' => '!px-6 !py-8 sm:!px-6 w-full !max-w-[547px] w-full !rounded-4xl']) }} style="display: none">
        <div class="mb-8 flex items-start justify-between">
            <div class="w-7"></div>
            <div>
                @if(isset($icon))
                    {{ $icon }}
                @endif
            </div>
            <button class="outline-none" onclick="Fancybox.close()" tabindex="-1">
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14 25.6667C20.4167 25.6667 25.6667 20.4167 25.6667 14C25.6667 7.58333 20.4167 2.33333 14 2.33333C7.58333 2.33333 2.33333 7.58333 2.33333 14C2.33333 20.4167 7.58333 25.6667 14 25.6667Z" stroke="#2C2E35" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M10.6983 17.3017L17.3017 10.6983" stroke="#2C2E35" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M17.3017 17.3017L10.6983 10.6983" stroke="#2C2E35" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
        {{ $slot }}
        @if($alertButtonText&&$alertButtonLink)
            <div class="mt-8">
                <x-public.green-button href="{{ $alertButtonLink }}">{{ $alertButtonText }}</x-public.green-button>
            </div>
        @endif
        @if($alertCloseButtonText)
            <div class="m-text-body d-text-body text-center mt-8">
                <a href="#" onclick="Fancybox.close();return false" tabindex="-1" class="outline-none text-myGreen2 underline hover:no-underline underline-offset-4">{{ $alertCloseButtonText }}</a>
            </div>
        @endif
    </div>
</div>
