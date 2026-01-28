@section('title', $seo['title'] ?? config('app.name'))
<x-cabinet-layout>
    <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-3 sm:py-4 md:py-12">
        <div>
            <h1 class="flex-1 d-headline-1 m-headline-1 text-center">{{ $seo['title'] }}</h1>
        </div>
    </div>
    <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16">
        <div class="border-b md:border-none border-myDark"></div>
    </div>
    <div class="md:px-8 lg:px-14 xl:px-16 py-3 sm:py-4 md:py-0">
        <div id="wrapper" class="flex relative">
            <div id="leftMenu"
                class="hidden lg:block min-w-[260px] md:w-[24.936061%] relative top-0 border-r border-r-myGreen sm:mr-[15px] md:mr-[45px] lg:mr-[74px] xl:mr-[104px] 2xl:mr-[148px]">
                <div id="leftMenu-content" class="relative">
                    <div
                        class="pb-6 sm:pb-9 md:pb-12 space-y-6 px-6 z-20 fixed top-0 right-0 w-full max-w-[390px] h-screen bg-myLightGray shadow-xl transform translate-x-full transition-transform duration-300 overflow-y-auto lg:shadow-none lg:relative lg:top-auto lg:right-auto lg:overflow-y-visible lg:translate-x-0 lg:bg-transparent">
                        @include('_parts.cabinet.leftMenu')
                    </div>
                </div>
            </div>
            <!-- Main Content -->
            <div class="flex-1">
                <div class="text-center flex flex-col md:flex-row md:justify-between">
                    <div class="text-2xl">{{ auth()->user()->name }}</div>
                    <div class="text-xl cormorantInfant">{{ auth()->user()->phone }}</div>
                </div>
                <div class="space-y-7 mt-8 mb-12">
                    @if ($coupons->count())
                        <div
                            class="bg-springGreen text-white text-center text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl !leading-1.8 mt-12 mb-6 overflow-hidden">
                            <div class="flex justify-center items-end">
                                <div class="relative flex-1">
                                    <img src="{{ asset('img/success-page/gift-lg.png') }}" alt=""
                                        class="absolute bottom-0 right-0 max-w-[192px] w-[25vw] hidden md:block">
                                </div>
                                <div class="p-4 pb-0 md:pb-4">
                                    @if ($coupons->count() == 1)
                                        <h2 class="text-xl md:text-2xl leading-normal uppercase">Подарочный промокод на
                                            <span
                                                class="cormorantInfant font-bold">{{ $coupons->first()->amount }}</span>₽
                                        </h2>
                                        <div class="py-3 md:py-5 uppercase font-bold cormorantInfant">
                                            {{ $coupons->first()->code }}</div>
                                        <div class="">Используйте его в период <span class="font-bold">с 1 по 30
                                                ноября 2025 года</span><br />
                                            и выгодно приобретайте любимые товары!</div>
                                    @else
                                        <h2 class="text-xl md:text-2xl leading-normal uppercase">Вам доступно
                                            {!! denum($coupons->count(), [
                                                '<span class="cormorantInfant">%d</span> подарочный промокод',
                                                '<span class="cormorantInfant">%d</span> подарочных промокода',
                                                '<span class="cormorantInfant">%d</span> подарочных промокодов',
                                            ]) !!}</h2>
                                        <div class="py-3 md:py-5">

                                            <div x-data="{ open: false }">
                                                <x-public.primary-button type="button" x-show="!open"
                                                    @click="open = ! open"
                                                    class="md:w-full max-w-[357px] border-white">Открыть
                                                    промокоды</x-public.primary-button>
                                                <div x-show="open">
                                                    <div class="flex flex-wrap justify-center -m-2 p-2">
                                                        @foreach ($coupons as $coupon)
                                                            <div class="p-2">
                                                                <div class="bg-white/80 text-sm text-black p-2">
                                                                    <div>Промокод на {!! formatPrice($coupon->amount, '₽') !!}</div>
                                                                    <div
                                                                        class="font-bold uppercase text-base cormorantInfant">
                                                                        {{ $coupon->code }}</div>
                                                                    <div class="cormorantInfant">
                                                                        {{ getRusDate($coupon->available_from->timestamp) . ' - ' . getRusDate($coupon->available_until->timestamp) }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="">Используйте их в указанный период и<br />
                                            выгодно приобретайте любимые товары!</div>
                                    @endif

                                    <div class="text-center md:hidden ">
                                        <img src="{{ asset('img/success-page/gift-sm.png') }}" alt=""
                                            class="block mx-auto max-w-[190px]">
                                    </div>
                                </div>
                                <div class="relative flex-1">
                                    <img src="{{ asset('img/success-page/gift-sm.png') }}" alt=""
                                        class="absolute bottom-0 left-0 max-w-[138px] w-[18vw] hidden md:block">
                                </div>
                            </div>
                        </div>
                    @endif
                    @if (getSettings('promo_1+1=3') &&
                            auth()->user()->orders()->where('confirm', 1)->where('created_at', '>', '2025-09-10 10:00:00')->exists())
                        <div class="border-springGreen border-[3px] p-5 pb-0 md:p-7 text-center md:px-16">
                            <h2 class="text-xl md:text-2xl leading-normal uppercase">Не упустите возможность забрать
                                <span class="font-bold">моно-масло в подарок!</span>
                            </h2>
                            <div class="bg-white py-2 px-2 md:px-4 lg:px-8 my-4 relative">
                                <img src="{{ asset('img/promo113/tube-left.png?2') }}" alt=""
                                    class="absolute bottom-0 left-0 -translate-x-1/2 translate-y-1/2 max-w-[86px] w-[6vw] hidden md:block">
                                <img src="{{ asset('img/promo113/tube-right.png?2') }}" alt=""
                                    class="absolute bottom-0 right-0 translate-x-1/2 translate-y-1/2 max-w-[107px] w-[7.5vw] hidden md:block">
                                <ul class="text-sm md:text-lg mb-2">
                                    <li><span class="cormorantInfant">1.</span> Опубликуйте <span
                                            class="font-bold">сторис с отметкой @le__mousse</span> о том, почему
                                        выбираете Le Mousse <span class="font-bold whitespace-nowrap">до <span
                                                class="cormorantInfant">12</span> сентября, <span
                                                class="cormorantInfant">10:00</span> Мск</span></li>
                                    <li><span class="cormorantInfant">2.</span> Не удаляйте сторис в течение <span
                                            class="cormorantInfant">24</span> часов</li>
                                    <li><span class="cormorantInfant">3.</span> Отправьте скриншот сторис в <a
                                            href="https://t.me/lemousse_support_bot" target="_blank">бот
                                            техподдержки</a></li>
                                </ul>
                                <div class="text-[10px] sm:text-xs">* Ваш профиль должен быть открыт</div>
                            </div>
                            <div class="text-xl md:text-2xl leading-normal uppercase px-2 md:px-4 lg:px-8">Выполнив эти
                                условия, вы получите в подарок<br /><span class="font-bold">моно-масло «Le
                                    Mousse»!</span></div>
                            <div class="text-center md:hidden ">
                                <img src="{{ asset('img/promo113/prizes-lk.png?2') }}" alt=""
                                    class="block mx-auto max-w-[220px]">
                            </div>
                        </div>
                    @endif
                    {{--          <div class="p-6 m-text-body d-text-body text-center space-y-4" style="background: url('{{ asset('img/cabinet-form.jpg?1') }}');background-size: cover;"> --}}
                    {{--            <div class="text-lg md:text-[32px] leading-normal uppercase text-center">ИСПОЛНЯЕМ ЗАВЕТНЫЕ<br class="inline sm:hidden"/> ЖЕЛАНИЯ 3-Х ПОКУПАТЕЛЕЙ<br/> --}}
                    {{--              <span class="font-bold">В РОЖДЕСТВЕНСКУЮ НОЧЬ!</span> --}}
                    {{--            </div> --}}
                    {{--            <div class="relative z-10"> --}}
                    {{--              <div class="text-center"> --}}
                    {{--                <img src="{{ asset('img/magicball.png?1') }}" alt="" class="bold mx-auto w-[116px] sm:w-[190px]"> --}}
                    {{--              </div> --}}
                    {{--            </div> --}}
                    {{--            <div class="text-sm md:text-2xl"> --}}
                    {{--              1. Переходите в анкету и описывайте одно своё <br class="sm:hidden"/><span class="font-bold">самое заветное желание</span><br/> --}}
                    {{--              2. После отправки анкеты вы автоматически становитесь <br class="sm:hidden"/>участником акции<br/> --}}
                    {{--              <span class="font-bold">Срок проведения акции: <br class="sm:hidden"/>с <span class="cormorantInfant">10:00</span> мск <span class="cormorantInfant">25</span> декабря <br class="sm:hidden"/>по <span class="cormorantInfant">15:00</span> мск <span class="cormorantInfant">7</span> января</span> --}}
                    {{--            </div> --}}
                    {{--            @if (!$user->forms()->where('custom_forms.id', 1)->exists() && $wishesButton) --}}
                    {{--              <div class="text-center mt-4"> --}}
                    {{--                <a href="{{ route('cabinet.form.index', 'wishes') }}" class="uppercase h-11 inline-flex items-center justify-center md:px-4 px-7 bg-formGreen text-white text-xl leading-none font-medium"> --}}
                    {{--                  ЗАГАДАТЬ ЖЕЛАНИЕ --}}
                    {{--                </a> --}}
                    {{--              </div> --}}
                    {{--            @endif --}}
                    {{--          </div> --}}
                    {{--          @if ($instaPromo && $instaPromo->value && $giftOrder) --}}
                    {{--            <div class=" bg-myBeige p-6 m-text-body d-text-body text-center space-y-4"> --}}
                    {{--              <div class="text-center uppercase"> --}}
                    {{--                <span class="font-bold">ТОЛЬКО <span class="cormorantInfant">24</span> ЧАСА</span><br/> --}}
                    {{--                НЕ УПУСКАЙТЕ ВОЗМОЖНОСТЬ <span class="font-bold">ЗАБРАТЬ IPHONE <span class="cormorantInfant">16</span>!</span> --}}
                    {{--              </div> --}}
                    {{--              <div class="text-center mt-6 mb-5"> --}}
                    {{--                <img src="{{ asset('img/iphone.png') }}" alt="" class="block mx-auto" style="width: 88px;"> --}}
                    {{--              </div> --}}
                    {{--              <div class="text-sm space-y-3 mb-6"> --}}
                    {{--                <div><span class="cormorantInfant">1.</span> Оформите заказ на сайте и получайте подарки после оплаты</div> --}}
                    {{--                <div><span class="cormorantInfant">2.</span> Сделайте скриншот своих подарков</div> --}}
                    {{--                <div><span class="cormorantInfant">3.</span> Выложите его в своих сторис с отметкой <span class="font-bold">@le__mousse</span> и <span class="font-bold">@nechaeva__proekt</span> до <span class="cormorantInfant">20</span> декабря <span class="cormorantInfant">10:00</span> мск <br/><span class="text-[10px]">*ваш профиль должен быть открыт</span></div> --}}
                    {{--              </div> --}}
                    {{--              <div class="text-sm font-bold">Выполнив эти условия, вы можете получить в подарок <span class="whitespace-nowrap">IPHONE <span class="cormorantInfant">16</span></span>!</div> --}}
                    {{--            </div> --}}
                    {{--          @endif --}}
                    {{--          @if (!auth()->user()->surveys()->exists()) --}}
                    {{--          <div x-data="{ open: true }" class="bg-myGreen2 text-white p-6 m-text-body d-text-body text-center space-y-4"> --}}
                    {{--            <button @click="open = ! open" type="button" class="uppercase flex justify-center items-center w-full mx-auto py-2 max-h-[60px] sm:max-h-none"> --}}
                    {{--              <h2 class="text-2xl uppercase font-medium">МЕНЯЕМ ВАШЕ МНЕНИЕ<br/><span class="font-bold">НА <span class="cormorantInfant">250</span> БОНУСОВ</span></h2> --}}
                    {{--              <svg :style="open ? 'transform: rotate(180deg)' : ''" width="16" height="11" class="ml-2" viewBox="0 0 16 11" fill="none" xmlns="http://www.w3.org/2000/svg"> --}}
                    {{--                <g filter="url(#filter0_b_9693_56)"> --}}
                    {{--                  <path d="M16 2.24023L8 10.1666L-3.46474e-07 2.24023L1.42 0.833293L8 7.35275L14.58 0.833292L16 2.24023Z" fill="currentColor" /> --}}
                    {{--                </g> --}}
                    {{--                <defs> --}}
                    {{--                  <filter id="filter0_b_9693_56" x="-32" y="-31.1667" width="80" height="73.3334" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> --}}
                    {{--                    <feFlood flood-opacity="0" result="BackgroundImageFix" /> --}}
                    {{--                    <feGaussianBlur in="BackgroundImageFix" stdDeviation="16" /> --}}
                    {{--                    <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_9693_56" /> --}}
                    {{--                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_9693_56" result="shape" /> --}}
                    {{--                  </filter> --}}
                    {{--                </defs> --}}
                    {{--              </svg> --}}
                    {{--            </button> --}}
                    {{--            <div x-show="open"> --}}
                    {{--              <p>(1 балл = 1 рубль)</p> --}}
                    {{--              <p>Пройдите небольшую анкету для оценки качества нашего бренда.</p> --}}
                    {{--              <x-public.primary-button href="{{ route('cabinet.survey.index', 'opros') }}" class="md:w-full max-w-[357px] border-white uppercase">Пройти</x-public.primary-button> --}}
                    {{--            </div> --}}
                    {{--          </div> --}}
                    {{--          @endif --}}
                    <div x-data="{ open: false }" class="bg-myGreen p-6 m-text-body d-text-body text-center space-y-4">
                        <button @click="open = ! open" type="button"
                            class="uppercase flex justify-center items-center w-full mx-auto py-2 max-h-[60px] sm:max-h-none">
                            <h2 class="text-2xl font-medium">Забота о покупателях</h2>
                            <svg :style="open ? 'transform: rotate(180deg)' : ''" width="16" height="11"
                                class="ml-2" viewBox="0 0 16 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g filter="url(#filter0_b_9693_56)">
                                    <path
                                        d="M16 2.24023L8 10.1666L-3.46474e-07 2.24023L1.42 0.833293L8 7.35275L14.58 0.833292L16 2.24023Z"
                                        fill="currentColor" />
                                </g>
                                <defs>
                                    <filter id="filter0_b_9693_56" x="-32" y="-31.1667" width="80" height="73.3334"
                                        filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                        <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                        <feGaussianBlur in="BackgroundImageFix" stdDeviation="16" />
                                        <feComposite in2="SourceAlpha" operator="in"
                                            result="effect1_backgroundBlur_9693_56" />
                                        <feBlend mode="normal" in="SourceGraphic"
                                            in2="effect1_backgroundBlur_9693_56" result="shape" />
                                    </filter>
                                </defs>
                            </svg>
                        </button>
                        <div x-show="open">
                            <p>Если вы столкнулись с какой-либо проблемой или озабочены сложным выбором нового продукта,
                                вы можете обратиться за консультацией к нашему врачу-дерматологу</p>
                            <x-public.primary-button href="https://t.me/dermatolog_lm_bot" target="_blank"
                                class="md:w-full max-w-[357px]">Обратиться к
                                врачу-дерматологу</x-public.primary-button>
                        </div>

                    </div>
                    @if (!auth()->user()->birthday)
                        <div class="p-6 bg-myBeige m-text-body d-text-body text-center bg-myGreen">
                            Укажите дату рождения - за <span class="cormorantInfant">3</span> дня до вашего праздника
                            мы начислим вам 500 бонусов в подарок<br />
                            <x-public.primary-button href="{{ route('cabinet.profile.index') }}"
                                class="md:w-full max-w-[357px]">Открыть настройки профиля</x-public.primary-button>
                        </div>
                    @endif
                    @php($super_bonuses = auth()->user()->getSuperBonuses())
                    @if ($super_bonuses)
                        <div class="p-6 m-text-body d-text-body text-center flex justify-center items-center"
                            style="background: #FADFE9;">
                            <x-public.svg.diamonds-l />
                            <span class="mx-5">{!! denum($super_bonuses, [
                                '<span class="cormorantInfant">%d</span> бриллиантовый бонус',
                                '<span class="cormorantInfant">%d</span> бриллиантовых бонуса',
                                '<span class="cormorantInfant">%d</span> бриллиантовых бонусов',
                            ]) !!}</span>
                            <x-public.svg.diamonds-r />
                        </div>
                    @endif
                    <div
                        class="py-3 flex justify-center items-center bg-myGreen2 text-white m-text-body d-text-body text-center font-medium uppercase">
                        <div>
                            баланс: <span class="font-bold">{!! denum(auth()->user()->getBonuses(), [
                                '<span class="cormorantInfant">%d</span> бонус',
                                '<span class="cormorantInfant">%d</span> бонуса',
                                '<span class="cormorantInfant">%d</span> бонусов',
                            ]) !!}</span>
                        </div>
                    </div>

                    @if (now() <= \Carbon\Carbon::parse('2026-01-31 23:59:59'))
                        @if (auth()->user()->getBonuses() > 0)
                            <div class="bunus-will-expire">
                                <div class="title">
                                    <b>Успейте использовать</b>
                                    <div>накопленные бонусы</div>
                                </div>
                                <div class="desc">Бонусы, начисленные <b>до 3 декабря 2025 г</b>,<br> действуют до 31 января
                                    2026 г, 23:59 МСК</div>
                                <div class="go">
                                    <a class="block text-[16px] w-full h-[48px] md:w-[328px] bg-[#545454] text-[#FFFFFF] p-[13px] ml-auto mr-auto"
                                        href="{{ route('product.catalog') }}">Потратить</a>
                                </div>
                                <div class="extra">Оплата бонусами  — до 50% суммы заказа</div>
                            </div>
                        @endif
                    @endif

                    <div class="">
                        @php($reviewOrder = auth()->user()->orders()->forReview()->first())
                        @if ($reviewOrder)
                            <div class="bg-myGreen2 text-white p-6 m-text-body d-text-body text-center space-y-4 mb-[24px]">
                                <h2 class="text-xl md:text-2xl leading-normal uppercase font-bold">ДАРИМ <span
                                        class="cormorantInfant">250</span> БОНУСОВ ЗА КАЖДЫЙ ОТЗЫВ</h2>
                                <p>Поделитесь мнением о приобретённых товарах на нашем сайте и получите <span
                                        class="cormorantInfant">250</span> бонусов за каждый отзыв!</p>
                                <p>* Будут доступны через <span class="cormorantInfant">7</span> дней после модерации.</p>
                                <p>* Можно оплатить до <span class="cormorantInfant">50%</span> стоимости следующего
                                    заказа.</p>
                                <x-public.primary-button href="{{ route('cabinet.order.show', $reviewOrder->slug) }}"
                                    class="md:w-full max-w-[357px] border-white">Оставить отзыв</x-public.primary-button>
                            </div>
                        @endif
                        <div x-data="{ open: false }" class="text-myDark m-text-body d-text-body text-center">
                        <div class="bg-myGreen">
                            <button @click="open = ! open" type="button"
                                class="uppercase flex justify-center items-center w-full mx-auto py-5 max-h-[60px] sm:max-h-none">
                                <h2 class="text-xl md:text-2xl leading-normal uppercase">личные данные</h2>
                                <svg :style="open ? 'transform: rotate(180deg)' : ''" width="16" height="11"
                                    class="ml-2" viewBox="0 0 16 11" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g filter="url(#filter0_b_9693_56)">
                                        <path
                                            d="M16 2.24023L8 10.1666L-3.46474e-07 2.24023L1.42 0.833293L8 7.35275L14.58 0.833292L16 2.24023Z"
                                            fill="currentColor" />
                                    </g>
                                    <defs>
                                        <filter id="filter0_b_9693_56" x="-32" y="-31.1667" width="80"
                                            height="73.3334" filterUnits="userSpaceOnUse"
                                            color-interpolation-filters="sRGB">
                                            <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                            <feGaussianBlur in="BackgroundImageFix" stdDeviation="16" />
                                            <feComposite in2="SourceAlpha" operator="in"
                                                result="effect1_backgroundBlur_9693_56" />
                                            <feBlend mode="normal" in="SourceGraphic"
                                                in2="effect1_backgroundBlur_9693_56" result="shape" />
                                        </filter>
                                    </defs>
                                </svg>
                            </button>
                        </div>
                        <div x-show="open" class="text-sm md:text-2xl">
                            <div class="bg-myGreen px-5 md:px-9 py-4 md:py-5">
                                <form action="{{ route('cabinet.profile.update') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="flex flex-wrap -mx-2 md:-mx-5">
                                        <div class="w-1/2 md:w-1/3 px-2 md:px-5 mb-2.5">
                                            <label for="last_name" class="block text-sm md:text-2xl text-left">Ваша
                                                фамилия</label>
                                            <input type="text" id="last_name" name="last_name"
                                                value="{{ old('last_name') ?? $user->last_name }}" required
                                                class="block w-full border border-myDark bg-transparent placeholder-myGray text-sm md:text-2xl !leading-none px-3 py-1.5 md:py-0.5 focus:ring-0 focus:border-black" />
                                        </div>
                                        <div class="w-1/2 md:w-1/3 px-2 md:px-5 mb-2.5">
                                            <label for="first_name" class="block text-sm md:text-2xl text-left">Ваше
                                                имя</label>
                                            <input type="text" id="first_name" name="first_name"
                                                value="{{ old('first_name') ?? $user->first_name }}" required
                                                class="block w-full border border-myDark bg-transparent placeholder-myGray text-sm md:text-2xl !leading-none px-3 py-1.5 md:py-0.5 focus:ring-0 focus:border-black" />
                                        </div>
                                        <div class="w-1/2 md:w-1/3 px-2 md:px-5 mb-2.5">
                                            <label for="middle_name" class="block text-sm md:text-2xl text-left">Ваше
                                                отчество</label>
                                            <input type="text" id="middle_name" name="middle_name"
                                                value="{{ old('middle_name') ?? $user->middle_name }}" required
                                                class="block w-full border border-myDark bg-transparent placeholder-myGray text-sm md:text-2xl !leading-none px-3 py-1.5 md:py-0.5 focus:ring-0 focus:border-black" />
                                        </div>
                                        <div class="w-1/2 md:w-1/3 px-2 md:px-5 mb-2.5">
                                            <label for="phone" class="block text-sm md:text-2xl text-left">Ваш
                                                телефон</label>
                                            <input type="text" id="phone" name="phone"
                                                value="{{ old('phone') ?? $user->phone }}" required
                                                class="cormorantInfant block w-full border border-myDark bg-transparent placeholder-myGray text-sm md:text-2xl !leading-none px-3 py-1.5 md:py-0.5 focus:ring-0 focus:border-black" />
                                        </div>
                                        <div class="w-1/2 md:w-1/3 px-2 md:px-5 mb-2.5">
                                            <label for="email" class="block text-sm md:text-2xl text-left">E-mail
                                                адрес</label>
                                            <input type="text" id="email" name="email"
                                                value="{{ old('email') ?? $user->email }}" required
                                                class="block w-full border border-myDark bg-transparent placeholder-myGray text-sm md:text-2xl !leading-none px-3 py-1.5 md:py-0.5 focus:ring-0 focus:border-black" />
                                        </div>
                                        <div class="w-1/2 md:w-1/3 px-2 md:px-5 mb-2.5">
                                            <label for="birthday" class="block text-sm md:text-2xl text-left">Дата
                                                рождения</label>
                                            <input type="text" id="birthday" name="birthday"
                                                value="{{ old('birthday') ?? $user->birthday?->format('d.m.Y') }}"
                                                @if ($user->birthday) disabled @endif
                                                class="birthday cormorantInfant block w-full border border-myDark bg-transparent placeholder-myGray text-sm md:text-2xl !leading-none px-3 py-1.5 md:py-0.5 focus:ring-0 focus:border-black" />
                                        </div>
                                        <div class="w-full">
                                            <button
                                                class="h-9 md:w-full md:max-w-[285px] mx-auto inline-flex items-center justify-center px-7 border-none bg-myGreen2 text-white uppercase text-xl md:text-2xl leading-none font-medium">Сохранить</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="bg-myGreen mt-4 px-5 md:px-9 py-4 md:py-5">
                                <h2 class="text-xl md:text-2xl leading-normal uppercase mb-6">Изменить пароль</h2>
                                <form action="{{ route('cabinet.profile.update') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="flex flex-wrap -mx-2 md:-mx-5 items-end">
                                        <div class="w-1/2 md:w-1/3 px-2 md:px-5 mb-2.5">
                                            <label for="password" class="block text-sm md:text-2xl text-left">Новый
                                                пароль</label>
                                            <input type="text" id="password" name="password"
                                                class="block w-full border border-myDark bg-transparent placeholder-myGray text-sm md:text-2xl !leading-none px-3 py-1.5 md:py-0.5 focus:ring-0 focus:border-black" />
                                        </div>
                                        <div class="w-1/2 md:w-1/3 px-2 md:px-5 mb-2.5">
                                            <label for="password_confirmation"
                                                class="block text-sm md:text-2xl text-left">Повторите пароль</label>
                                            <input type="text" id="password_confirmation"
                                                name="password_confirmation"
                                                class="block w-full border border-myDark bg-transparent placeholder-myGray text-sm md:text-2xl !leading-none px-3 py-1.5 md:py-0.5 focus:ring-0 focus:border-black" />
                                        </div>
                                        <div class="w-full md:w-1/3 px-2 md:px-5 mb-2.5">
                                            <button
                                                class="h-9 md:w-full md:max-w-[285px] mx-auto inline-flex items-center justify-center px-7 border-none bg-myGreen2 text-white uppercase text-xl md:text-2xl leading-none font-medium">Сохранить</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="px-5 md:px-0">
                    <h2 class="text-xl md:text-2xl leading-normal uppercase mb-6">Мои заказы</h2>
                    <div class="flex flex-col text-sm md:text-2xl">
                        @forelse($orders as $order)
                            <div class="flex justify-between items-center h-[50px] relative border-b border-myDark">
                                <a href="{{ route('cabinet.order.show', $order->slug) }}"
                                    class="block absolute top-0 left-0 w-full h-full z-10"></a>
                                <div class="flex-1 flex items-center space-x-1whitespace-nowrap">
                                    <span class="cormorantInfant">{{ $order->getOrderNumber() }}</span>
                                    @if ($order->coupones()->count() > 0)
                                        {!! '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-gift" width="20" height="20" viewBox="0 0 24 24" stroke-width="1" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 8m0 1a1 1 0 0 1 1 -1h16a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1z" /><path d="M12 8l0 13" /><path d="M19 12v7a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-7" /><path d="M7.5 8a2.5 2.5 0 0 1 0 -5a4.8 8 0 0 1 4.5 5a4.8 8 0 0 1 4.5 -5a2.5 2.5 0 0 1 0 5" /></svg>' !!}
                                    @endif
                                </div>
                                <div class="flex-1 text-center">
                                    <span
                                        class="cormorantInfant">{{ date('d.m.Y H:i', strtotime($order->created_at)) }}</span>
                                </div>
                                <div class="flex-1 text-right">
                                    {!! formatPrice($order->amount, true) !!}
                                </div>
                            </div>
                        @empty
                            <div class="p4 text-center">нет заказов</div>
                        @endforelse
                    </div>
                </div>
                @if (!auth()->user()->tgChats()->where('active', true)->exists())
                    <div x-data="{ open: false }"
                        class="mt-12 mb-12 bg-myBeige p-6 m-text-body d-text-body text-center space-y-4">
                        <button @click="open = ! open" type="button"
                            class="uppercase flex justify-center items-center w-full mx-auto py-2 max-h-[60px] sm:max-h-none">
                            <h2 class="text-xl md:text-2xl leading-normal uppercase">подпишись на бот и получи <span
                                    class="cormorantInfant">250</span> бонусов<br /><span
                                    class="text-sm md:text-2xl">(<span class="cormorantInfant">1</span> бонус = <span
                                        class="cormorantInfant">1</span> рубль)</span></h2>
                            <svg :style="open ? 'transform: rotate(180deg)' : ''" width="16" height="11"
                                class="ml-2" viewBox="0 0 16 11" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <g filter="url(#filter0_b_9693_56)">
                                    <path
                                        d="M16 2.24023L8 10.1666L-3.46474e-07 2.24023L1.42 0.833293L8 7.35275L14.58 0.833292L16 2.24023Z"
                                        fill="currentColor" />
                                </g>
                                <defs>
                                    <filter id="filter0_b_9693_56" x="-32" y="-31.1667" width="80"
                                        height="73.3334" filterUnits="userSpaceOnUse"
                                        color-interpolation-filters="sRGB">
                                        <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                        <feGaussianBlur in="BackgroundImageFix" stdDeviation="16" />
                                        <feComposite in2="SourceAlpha" operator="in"
                                            result="effect1_backgroundBlur_9693_56" />
                                        <feBlend mode="normal" in="SourceGraphic"
                                            in2="effect1_backgroundBlur_9693_56" result="shape" />
                                    </filter>
                                </defs>
                            </svg>
                        </button>
                        <div x-show="open">
                            <div class="text-sm md:text-2xl">
                                Для этого подпишитесь на наш БОТ В TELEGRAM<br />
                                <span class="text-xs">* (для начала нужно авторизоваться на сайте)</span><br /><br />

                                <span class="font-semibold">В нем будете получать:</span><br /><br />

                                <div class="flex items-center justify-center">
                                    <svg class="mr-2" width="27" height="27" viewBox="0 0 27 27"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M23.0625 8.73754L20.0576 5.73267L10.4163 15.3739L6.94384 11.9014L3.93896 14.9063L10.4715 21.4463L23.0625 8.73754Z"
                                            fill="#15BE0B" />
                                        <path
                                            d="M3.9375 14.91L10.47 21.45L23.0625 8.7412L20.0576 5.73633L10.4164 15.3776L6.94388 11.9051L3.9375 14.91Z"
                                            stroke="black" stroke-width="0.75" stroke-miterlimit="10"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg> эксклюзивные скидки и акции
                                </div>
                                <div class="flex items-center justify-center">
                                    <svg class="mr-2" width="27" height="27" viewBox="0 0 27 27"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M23.0625 8.73754L20.0576 5.73267L10.4163 15.3739L6.94384 11.9014L3.93896 14.9063L10.4715 21.4463L23.0625 8.73754Z"
                                            fill="#15BE0B" />
                                        <path
                                            d="M3.9375 14.91L10.47 21.45L23.0625 8.7412L20.0576 5.73633L10.4164 15.3776L6.94388 11.9051L3.9375 14.91Z"
                                            stroke="black" stroke-width="0.75" stroke-miterlimit="10"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg> изменения статуса заказа
                                </div>
                                <br />
                                *бонусы будут начислены в течении <span class="cormorantInfant">7</span>
                                дней<br /><br />
                            </div>
                            <x-public.primary-button
                                href="https://t.me/lemousse_notifications_bot?start={{ auth()->user()->uuid }}"
                                target="_blank"
                                class="md:w-full max-w-[357px] uppercase">Подписаться</x-public.primary-button>
                        </div>

                    </div>
                @endif
                @if (isset($certs) && !empty($certs))
                    <div class="m-text-body d-text-body p-4">
                        <h5 class="d-headline-4 m-headline-3">Счастливый купон</h5>
                        @foreach ($certs as $cert)
                            <p>Ваш сертификат на 1000 рублей: <span
                                    class="badge badge-success">{{ $cert }}</span></p>
                        @endforeach
                        <p>Чтобы использовать сертификат, введите код на странице оформления заказа в поле "подарочный
                            сертификат"</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @if (!$user->pages()->where('id', 1)->exists())
        <x-public.popup id="oferta-alert">
            <x-slot name="icon">
                <svg width="65" height="64" viewBox="0 0 65 64" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M59.1667 44.64V12.4533C59.1667 9.25334 56.5533 6.88001 53.38 7.14667H53.22C47.62 7.62667 39.1133 10.48 34.3667 13.4667L33.9133 13.76C33.14 14.24 31.86 14.24 31.0867 13.76L30.42 13.36C25.6733 10.4 17.1933 7.57334 11.5933 7.12001C8.42 6.85334 5.83333 9.25334 5.83333 12.4267V44.64C5.83333 47.2 7.91333 49.6 10.4733 49.92L11.2467 50.0267C17.0333 50.8 25.9667 53.7333 31.0867 56.5333L31.1933 56.5867C31.9133 56.9867 33.06 56.9867 33.7533 56.5867C38.8733 53.76 47.8333 50.8 53.6467 50.0267L54.5267 49.92C57.0867 49.6 59.1667 47.2 59.1667 44.64Z"
                        stroke="#B1908E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M32.5 14.64V54.64" stroke="#B1908E" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M21.1667 22.64H15.1667" stroke="#B1908E" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M23.1667 30.64H15.1667" stroke="#B1908E" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </x-slot>
            <div class="m-text-body d-text-body text-center">
                <div class="d-headline-4 m-headline-3 mb-4">Мы обновили публичную оферту</div>
                <p class="m-text-body d-text-body mb-4">Уделите минуту, чтобы ознакомиться с изменениями и подтвердить
                    согласие</p>
                <p class="m-text-body d-text-body mb-8"><a href="{{ route('page', 'oferta') }}" target="_blank"
                        class="text-myGreen underline hover:no-underline">Читать оферту</a></p>
                <div class="mb-8">
                    <form action="{{ route('cabinet.document.accept', 1) }}" method="POST" class="text-center">
                        @csrf
                        <input type="hidden" name="accepted" value="1">
                        <x-public.green-button>
                            ознакомлен(а) и принимаю
                        </x-public.green-button>
                    </form>

                </div>
            </div>
        </x-public.popup>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Fancybox.show(
                    [{
                        src: '#oferta-alert',
                        width: "900px",
                        height: "700px",
                    }, ], {
                        closeButton: false,
                        Toolbar: {
                            display: {
                                left: [],
                                middle: [],
                                right: [],
                            },
                        },
                        loop: false,
                        touch: false,
                        contentClick: false,
                        dragToClose: false,
                    }
                );
            });
        </script>
    @endif
</x-cabinet-layout>
