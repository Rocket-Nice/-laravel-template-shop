<script>
    if (!window.__catPopupInit) {
        window.__catPopupInit = true;
        window.addEventListener('load', function () {
            const storageKey = 'cat-popup-opened-date';
            const participateKey = 'cat-popup-participated';
            const formatter = new Intl.DateTimeFormat('en-CA', {
                timeZone: 'Europe/Moscow',
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            });
            const todayMsk = formatter.format(new Date());
            try {
                if (localStorage.getItem(storageKey) === todayMsk) {
                    window.__catPopupOpenedOnce = true;
                }
            } catch (e) {}

            try {
                if (localStorage.getItem(participateKey) === '1') {
                    window.__catPopupParticipated = true;
                }
            } catch (e) {}

            window.addEventListener('open-cat-popup', function () {
                window.__catPopupOpenedOnce = true;
                try {
                    localStorage.setItem(storageKey, todayMsk);
                } catch (e) {}
            }, { once: true });

            if (window.__catPopupTimer) {
                clearTimeout(window.__catPopupTimer);
            }
            window.__catPopupOpenedOnce = window.__catPopupOpenedOnce || false;
            window.__catPopupTimer = setTimeout(function () {
                if (!window.__catPopupOpenedOnce && !window.__catPopupParticipated) {
                    window.dispatchEvent(new CustomEvent('open-cat-popup'));
                }
            }, 30000);
        });
    }
</script>

<div x-data="{
    open: false,
    participated: false,
    loading: false,
    categories: [],
    refreshCount: 0,
    refreshLimit: 3,
    getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) {
            return parts.pop().split(';').shift();
        }
        return null;
    },
    setParticipatedCookie() {
        try {
            document.cookie = 'cat_in_bag_participated=1; path=/; max-age=86400';
        } catch (e) {}
    },
    init() {
        try {
            this.participated = localStorage.getItem('cat-popup-participated') === '1';
        } catch (e) {}
        if (!this.participated) {
            this.participated = this.getCookie('cat_in_bag_participated') === '1';
        }
        if (window.__catPopupParticipated) {
            this.participated = true;
        }
        window.addEventListener('open-cat-popup', () => {
            if (this.participated) {
                return;
            }
            this.open = true;
            this.lock();
            if (this.categories.length === 0) {
                this.loadCategories(false);
            }
        });
        window.addEventListener('cat-popup-participated', () => {
            this.participated = true;
        });
        window.addEventListener('keydown', e => {
            if (e.key === 'Escape' && this.open) {
                this.reset();
            }
        });
    },
    lock() {
        document.body.classList.add('overflow-hidden')
    },
    unlock() {
        document.body.classList.remove('overflow-hidden')
    },
    reset() {
        this.open = false;
        this.unlock();
    },
    participate() {
        this.participated = true;
        try {
            localStorage.setItem('cat-popup-participated', '1');
        } catch (e) {}
        this.setParticipatedCookie();
        window.dispatchEvent(new CustomEvent('cat-popup-participated'));
        this.reset();
    },
    get refreshLabel() {
        if (this.refreshCount >= (this.refreshLimit - 1)) {
            return `${this.refreshLimit} из ${this.refreshLimit}`;
        }
        return `Обновить ${this.refreshCount + 1} из ${this.refreshLimit}`;
    },
    get canRefresh() {
        return this.refreshCount < (this.refreshLimit - 1);
    },
    async loadCategories(forceRefresh = false) {
        if (this.loading) return;
        this.loading = true;
        try {
            const url = new URL('/cat-in-bag/preview-categories', window.location.origin);
            if (forceRefresh) {
                url.searchParams.set('refresh', '1');
            }
            const response = await fetch(url.toString(), { credentials: 'same-origin' });
            const data = await response.json();
            this.categories = Array.isArray(data.categories) ? data.categories.slice(0, 2) : [];
            this.refreshCount = data.refresh_count ?? 0;
            this.refreshLimit = data.refresh_limit ?? 3;
        } catch (e) {
            this.categories = [];
        } finally {
            this.loading = false;
        }
    }
}" x-init="init()">

<button x-show="!participated" x-cloak onclick="window.dispatchEvent(new CustomEvent('open-cat-popup'))"
    class="fixed bottom-[16px] right-[16px] md:bottom-[32px] md:right-[32px] z-40 hover:scale-105 transition-transform duration-200">
    <picture>
        <source media="(max-width: 767px)" srcset="{{ asset('img/cat-bag/popup-cat-open-mobile.png') }}">
        <source media="(min-width: 768px)" srcset="{{ asset('img/cat-bag/popup-cat-open.png') }}">
        <img src="{{ asset('img/cat-bag/popup-cat-open.png') }}" alt="Открыть подарки" class="w-full">
    </picture>
</button>

<div x-show="open && !participated" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">

    <div class="absolute inset-0 bg-black/50" @click="reset()"></div>

    <div class="max-w-[480px] rounded-[10px] bg-[#F6EFF2] pt-6 relative font-inter_font">
        <button @click="reset()" class="absolute top-4 right-4">
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

        <h2 class="text-center text-2xl font-medium leading-7 mb-4 px-4 uppercase font-main-font">
            Выберите подарки,<br>
            которые хотите забрать
        </h2>

        <div class="grid grid-cols-4 mb-4 bg-white py-4">
            <template x-if="loading && categories.length === 0">
                <div class="col-span-4 text-center text-sm text-gray-500">Загружаем категории...</div>
            </template>

            <template x-for="category in categories" :key="category.id">
                <label class="group cursor-pointer text-center">
                    <input type="checkbox" class="hidden">
                    <img :src="category.image_thumb || category.image" alt="" class="mx-auto mb-2 h-auto object-contain">
                    <p class="text-[10px] text-[#414141] leading-3 font-light px-2" x-text="category.name"></p>
                </label>
            </template>

            <label class="group cursor-pointer text-center">
                <input type="checkbox" class="hidden">
                <img src="{{ asset('img/cat-bag/gift-cat.png') }}" alt="" class="mx-auto mb-2 h-auto object-contain">
                <p class="text-[10px] text-[#414141] leading-3 font-light px-2">Кот в мешке</p>
            </label>

            <label class="group cursor-pointer text-center">
                <input type="checkbox" class="hidden">
                <img src="{{ asset('img/cat-bag/gift-gold.png') }}" alt="" class="mx-auto mb-2 h-auto object-contain">
                <p class="text-[10px] text-[#414141] leading-3 font-light px-2">Золотой мешок от 10 000 ₽</p>
            </label>
        </div>

        <div class="flex flex-row gap-2 justify-center mb-4 px-4">
            <x-cat-bag-button type="button" @click="participate(); window.location.href='{{ url('/catalog') }}'">
                Участвовать
            </x-cat-bag-button>

            <x-cat-bag-button variant="outline" x-bind:disabled="!canRefresh || loading" @click="loadCategories(true)">
                <span x-text="refreshLabel"></span>
            </x-cat-bag-button>
        </div>

        <p class="text-center text-[12px] text-[#707070] mx-auto mb-2 leading-4 max-w-[220px]">
            После обновления предыдущие подарки не сохраняются
        </p>

        <div class="grid grid-cols-3 text-[13px] leading-tight text-[#414141] text-center">
            <div class="p-[22px] md:p-[19px] flex flex-row justify-center items-center">
                От 4 000 ₽ —<br>откроете 1 🎁
            </div>
            <div class="p-[22px] md:p-[19px] flex flex-row justify-center items-center">
                От 6 500 ₽ —<br>откроете 2 🎁
            </div>
            <div class="p-[22px] md:p-[19px] flex flex-row justify-center items-center">
                От 10 000 ₽ —<br>откроете 3 🎁<br>
                один из них может быть золотым
            </div>
        </div>
    </div>
</div>
</div>
