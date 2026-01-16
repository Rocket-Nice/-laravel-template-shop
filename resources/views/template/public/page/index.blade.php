@section('title', $seo['title'] ?? config('app.name'))
@php
    if (getSettings('winterMode')) {
        $bgColor = 'bg-winterGreen';
        $textColor = 'text-white';
        $borderColor = 'border-white';
    } else {
        $bgColor = 'bg-transparent';
        $textColor = 'text-myDark';
        $borderColor = 'border-myDark';
    }
@endphp
<x-app-layout>
    {{--  <div class="text-center text-sm lg:text-lg text-white uppercase py-3 leading-none font-semibold" style="background: #78796E;"> --}}
    {{--    С <span class="cormorantInfant">1</span> ДЕКАБРЯ ПОВЫШЕНИЕ ЦЕН! --}}
    {{--  </div> --}}
    <div class="md:flex p-0 md:flex-row-reverse {{ $bgColor }} {{ $textColor }}">
        <div class="md:max-w-[40%] xl:max-w-[546px] mx-auto flex flex-col">
            <style>
                @media only screen and (max-width: 768px) {
                    .mobile-item-square {
                        position: relative;
                        padding: 0;
                    }

                    .mobile-item-square>* {
                        position: absolute;
                        top: 0;
                        bottom: 0;
                        left: 0;
                        right: 0;
                        width: 100%;
                        height: 100%;
                        margin: 0;
                    }

                    .mobile-item-square:after {
                        content: "";
                        display: block;
                        /*padding-top: 107.847533632%;*/
                        padding-top: 122%;
                    }
                }
            </style>
            <div id="firstSlider" class="swiper flex-1 w-full h-full">
                <div class="swiper-wrapper">
                    @if (isset($content->carousel_data['firstSlider']))
                        <style>
                            #firstSlider .swiper-wrapper {
                                align-items: stretch;
                            }

                            #firstSlider .swiper-slide {
                                height: auto;
                            }
                        </style>
                        @foreach ($content->carousel_data['firstSlider'] as $key => $slide)
                            @if (isset($slide['id']) && in_array($slide['id'], ['hidden', 'diamond_hour']))
                                @continue
                            @endif
                            @if (getSettings('goldTicket') && isset($slide['id']) && in_array($slide['id'], ['gift10000', 'shippingFree']))
                                @continue
                            @endif
                            <div class="swiper-slide mobile-item-square">
                                @if (isset($slide['image']['size']))
                                    @if (isset($slide['link']) && !empty($slide['link']) && (!isset($slide['button']) || !$slide['button']))
                                        <a href="{{ $slide['link'] }}">
                                            <picture class="block w-full h-full object-cover">
                                                {!! generatePictureHtml($slide['image']['size']) !!}
                                            </picture>
                                            {{--                      <input type="hidden" data-id="firstSlider-{{ $key }}" class="json-image" value="{{ e(json_encode($slide['image']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block w-full h-full object-cover"> --}}
                                        </a>
                                    @else
                                        <div class="relative h-full">
                                            <div style="opacity:0;position:absolute;font-size:.1em;">{{ $key }}
                                            </div>
                                            <picture class="block w-full h-full object-cover">
                                                {!! generatePictureHtml($slide['image']['size']) !!}
                                            </picture>
                                            {{--                      <input type="hidden" data-id="firstSlider-{{ $key }}" class="json-image" value="{{ e(json_encode($slide['image']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block w-full h-full object-cover"> --}}
                                        </div>
                                    @endif
                                @endif
                                @if (isset($slide['link']) && !empty($slide['link']) && isset($slide['button']) && !empty($slide['button']))
                                    <div class="absolute w-full z-10 bottom-12 left-0 p-4 text-white  text-center"
                                        style="padding-bottom:0;">
                                        <x-public.primary-button href="{{ $slide['link'] }}"
                                            class="rounded-full border-white m-text-body d-text-body">Подробнее</x-public.primary-button>
                                    </div>
                                @endif
                                @if (isset($slide['id']) && $slide['id'] == 'timer')
                                    <div>
                                        <div id="timer-container"
                                            class="absolute w-full z-10 left-0 p-4 cormorantInfant text-center text-black"
                                            style="opacity:0;top:82%;transform: translateY(-50%);padding-bottom:0;">
                                            <div id="timer" class="text-center flex justify-center space-x-2">
                                                <div
                                                    class="scale-text cormorantInfant flex text-[28px] items-center px-2.5 py-0.5">
                                                    {{--                            <div class="relative"> --}}
                                                    {{--                              <div class="flex space-x-1"> --}}
                                                    {{--                                <div class="time w-full flex justify-center items-center text-center digit" style="width: .4em;" id="days-1">0</div> --}}
                                                    {{--                                <div class="time w-full flex justify-center items-center text-center digit" style="width: .4em;" id="days-2">0</div> --}}
                                                    {{--                              </div> --}}
                                                    {{--                              <div id="days-text" class="timer-text font-roboto_condensed text-center left-1/2 -translate-x-1/2 absolute bottom-[-5px]">дня</div> --}}
                                                    {{--                            </div> --}}
                                                    {{--                            <div class="font-foglihten_no06 inline-block mx-1">:</div> --}}
                                                    <div class="relative">
                                                        <div class="flex space-x-1">
                                                            <div class="time w-full flex justify-center items-center text-center digit"
                                                                style="width: .4em;" id="hours-1">0</div>
                                                            <div class="time w-full flex justify-center items-center text-center digit"
                                                                style="width: .4em;" id="hours-2">0</div>
                                                        </div>
                                                        <div id="hours-text"
                                                            class="timer-text font-roboto_condensed text-center left-1/2 -translate-x-1/2 absolute bottom-[-5px]">
                                                            часов</div>
                                                    </div>
                                                    <div class="font-foglihten_no06 inline-block mx-1">:</div>
                                                    <div class="relative">
                                                        <div class="flex space-x-1">
                                                            <div class="time w-full flex justify-center items-center text-center digit"
                                                                style="width: .4em;" id="minutes-1">0</div>
                                                            <div class="time w-full flex justify-center items-center text-center digit"
                                                                style="width: .4em;" id="minutes-2">0</div>
                                                        </div>
                                                        <div id="min-text"
                                                            class="timer-text font-roboto_condensed text-center left-1/2 -translate-x-1/2 absolute bottom-[-5px]">
                                                            минут</div>
                                                    </div>
                                                    <div class="font-foglihten_no06 inline-block mx-1">:</div>
                                                    <div class="relative">
                                                        <div class="flex space-x-1">
                                                            <div class="time w-full flex justify-center items-center text-center digit"
                                                                style="width: .4em;" id="seconds-1">0</div>
                                                            <div class="time w-full flex justify-center items-center text-center digit"
                                                                style="width: .4em;" id="seconds-2">0</div>
                                                        </div>
                                                        <div id="sec-text"
                                                            class="timer-text font-roboto_condensed text-center left-1/2 -translate-x-1/2 absolute bottom-[-5px]">
                                                            секунд</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="swiper-button-next backdrop-blur-sm"></div>
                <div class="swiper-button-prev backdrop-blur-sm"></div>
                <div class="swiper-pagination" style="bottom: 2px;"></div>
            </div>
        </div>
        <script>
            const denum = (num, titles) => {
                const cases = [2, 0, 1, 1, 1, 2];
                const caseIndex = (num % 100 > 4 && num % 100 < 20) ? 2 : cases[Math.min(num % 10, 5)];
                return titles[caseIndex].replace('%d', num);
            }
            if (document.getElementById("timer")) {
                const textEl = document.getElementById('timer');

                function updateTextWidth() {
                    const width = textEl.offsetWidth;
                    if (width === 0) {
                        setTimeout(function() {
                            updateTextWidth()
                        }, 100)
                        return;
                    }
                    const fontSize = width * 0.10;
                    // document.querySelector('.scale-text').style.lineHeight = fontSize*1.5+'px'
                    document.querySelector('.scale-text').style.fontSize = fontSize + 'px'
                    document.querySelectorAll('.timer-text').forEach(item => {
                        item.style.fontSize = fontSize * 0.3 + 'px'
                    })
                    // textEl.style.setProperty('--text-width', width);
                }

                window.addEventListener('resize', updateTextWidth);
                updateTextWidth();
                // var countDownDate = new Date("May 27, 2024 16:00:00").getTime();
                var countDownDate = @json(strtotime('December 1, 2025 21:00:00') * 1000);

                function splitNumber(number) {
                    // Преобразуем число в строку и добавляем ведущий ноль если нужно
                    const paddedNumber = number.toString().padStart(2, '0');
                    // Возвращаем массив из двух цифр
                    return [paddedNumber[0], paddedNumber[1]];
                }
                distance = 0

                function calcTime() {
                    document.getElementById('timer-container').style.opacity = '1';
                    var now = new Date().getTime();
                    distance = countDownDate - now;

                    // Вычисляем значения
                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    // const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const hours = Math.floor((distance / (1000 * 60 * 60)));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    // document.getElementById('days-text').innerText = denum(days, ['день', 'дня', 'дней']);
                    document.getElementById('hours-text').innerText = denum(hours, ['час', 'часа', 'часов']);
                    document.getElementById('min-text').innerText = denum(minutes, ['минута', 'минуты', 'минут']);
                    document.getElementById('sec-text').innerText = denum(seconds, ['секунда', 'секунды', 'секунд']);
                    // Разбиваем каждое значение на отдельные цифры
                    const [days1, days2] = splitNumber(days);
                    const [hours1, hours2] = splitNumber(hours);
                    const [minutes1, minutes2] = splitNumber(minutes);
                    const [seconds1, seconds2] = splitNumber(seconds);

                    // Обновляем значения в DOM
                    // document.getElementById("days-1").innerHTML = days1;
                    // document.getElementById("days-2").innerHTML = days2;
                    document.getElementById("hours-1").innerHTML = hours1;
                    document.getElementById("hours-2").innerHTML = hours2;
                    document.getElementById("minutes-1").innerHTML = minutes1;
                    document.getElementById("minutes-2").innerHTML = minutes2;
                    document.getElementById("seconds-1").innerHTML = seconds1;
                    document.getElementById("seconds-2").innerHTML = seconds2;

                }
                calcTime();
                var x = setInterval(function() {
                    calcTime();

                    if (distance < 0) {
                        clearInterval(x);
                        // redirect();
                        document.getElementById("timer").innerHTML = "Время истекло";
                    }
                }, 1000);
            }
        </script>
        <div class="bg-myGreen flex-1 flex flex-col-reverse md:flex-col justify-between"
            @if (getSettings('winterMode')) style="color: #fff;background-color: #2C4B3A;background: url('{{ asset('img/top-bg.jpg?1') }}');background-size: cover;" @endif>
            <div class="p-0 md:pt-3 pb-6 mb:pb-12 md:pr-4 lg:pt-[98px] lg:pr-0 w-full bg-cover relative flex-1">
                @if (getSettings('springMode') && isset($content->image_data['firstImage']['size']))
                    <input type="hidden" data-id="firstImage" class="json-image"
                        value="{{ e(json_encode($content->image_data['firstImage']['size'], JSON_UNESCAPED_UNICODE)) }}"
                        data-picture-class="block object-cover absolute left-0 right-0 top-0 bottom-0 w-full h-full">
                @endif
                <div
                    class="text-center md:text-left pt-3 md:pt-0 md:pl-4 lg:pl-16 relative z-10 @if (getSettings('springMode')) text-white @endif">
                    <h1 class="m-headline-1 d-headline-1 mb-4 md:mb-6">{{ $content->text_data['headline1'] }}</h1>
                    <p class="d-text-body m-text-body mb-4 md:mb-12 mobile-hidden-br md:px-0 px-2">
                        {!! nl2br($content->text_data['subtitle1']) !!}</p>
                    <a href="{{ route('page.dermatologists') }}"
                        class="h-11 inline-flex items-center justify-center px-3 md:px-4 lg:px-7 border @if (getSettings('springMode')) border-white @else{{ $borderColor }} @endif text-xl leading-none font-medium md:w-full max-w-[357px] md:text-2xl md:h-14">Подробнее</a>
                </div>
            </div>

            {{--      max-w-[546px] lg:max-w-none mx-auto --}}

            <div class="w-full sm:w-auto lg:mx-0 flex justify-end">
                <div class="p-0 w-full bg-cover relative">
                    @if (!getSettings('springMode') && isset($content->image_data['activeComponentsImage']['size']))
                        <input type="hidden" data-id="activeComponentsImage" class="json-image"
                            value="{{ e(json_encode($content->image_data['activeComponentsImage']['size'], JSON_UNESCAPED_UNICODE)) }}"
                            data-picture-class="block object-cover absolute left-0 right-0 top-0 bottom-0 w-full h-full">
                    @endif
                    <div class="backdrop-blur-sm text-center font-medium text-white text-lg xl:text-2xl pt-px sm:pt-6 px-1 sm:px-6 pb-3.5 z-10 relative @if (getSettings('springMode')) bg-springGreen @endif"
                        @if (!getSettings('springMode')) style="background: #375545A3;" @endif>
                        {{--            <div class="font-semibold xl:font-medium uppercase mb-8 leading-1.6 text-17 xl:text-2xl">Более <span class="percent-round relative inline-block align-middle mx-3 xl:mx-4 leading-none -translate-y-1 text-19 xl:text-2xl font-bold">80% --}}
                        {{--                <svg width="58" height="58" class="absolute w-[42px] h-[42px] xl:w-[58px] xl:h-[58px] left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2" viewBox="0 0 58 58" fill="none" xmlns="http://www.w3.org/2000/svg"> --}}
                        {{--                  <circle opacity="0.24" cx="29" cy="29" r="26.5" stroke="white" stroke-width="5"/> --}}
                        {{--                  <mask id="path-2-inside-1_1945_5452" fill="white"> --}}
                        {{--                    <path d="M10.442 10.442C9.36609 9.36609 7.61008 9.35852 6.64108 10.5316C2.95715 14.9915 0.677026 20.4702 0.128703 26.2709C-0.506379 32.9893 1.22149 39.7189 5.01466 45.3004C8.80784 50.8819 14.4288 54.9658 20.9093 56.8485C27.3897 58.7313 34.3239 58.295 40.5173 55.6149C46.7107 52.9348 51.7755 48.1786 54.8392 42.1657C57.9029 36.1528 58.7737 29.2597 57.3016 22.6738C55.8295 16.088 52.1066 10.2217 46.7743 6.08551C42.1705 2.5144 36.6158 0.426069 30.8429 0.0585672C29.3244 -0.0380982 28.1278 1.24716 28.1756 2.76795V2.76795C28.2234 4.28874 29.4988 5.46603 31.0147 5.59651C35.503 5.98285 39.807 7.65445 43.3972 10.4393C47.7163 13.7895 50.7319 18.5412 51.9243 23.8758C53.1167 29.2104 52.4114 34.7938 49.9297 39.6642C47.4481 44.5347 43.3456 48.3872 38.329 50.5581C33.3124 52.729 27.6957 53.0823 22.4465 51.5573C17.1973 50.0323 12.6443 46.7243 9.57188 42.2033C6.49941 37.6823 5.09983 32.2314 5.61425 26.7894C6.04184 22.266 7.76959 17.9842 10.5604 14.4479C11.5031 13.2535 11.5179 11.5179 10.442 10.442V10.442Z"/> --}}
                        {{--                  </mask> --}}
                        {{--                  <path d="M10.442 10.442C9.36609 9.36609 7.61008 9.35852 6.64108 10.5316C2.95715 14.9915 0.677026 20.4702 0.128703 26.2709C-0.506379 32.9893 1.22149 39.7189 5.01466 45.3004C8.80784 50.8819 14.4288 54.9658 20.9093 56.8485C27.3897 58.7313 34.3239 58.295 40.5173 55.6149C46.7107 52.9348 51.7755 48.1786 54.8392 42.1657C57.9029 36.1528 58.7737 29.2597 57.3016 22.6738C55.8295 16.088 52.1066 10.2217 46.7743 6.08551C42.1705 2.5144 36.6158 0.426069 30.8429 0.0585672C29.3244 -0.0380982 28.1278 1.24716 28.1756 2.76795V2.76795C28.2234 4.28874 29.4988 5.46603 31.0147 5.59651C35.503 5.98285 39.807 7.65445 43.3972 10.4393C47.7163 13.7895 50.7319 18.5412 51.9243 23.8758C53.1167 29.2104 52.4114 34.7938 49.9297 39.6642C47.4481 44.5347 43.3456 48.3872 38.329 50.5581C33.3124 52.729 27.6957 53.0823 22.4465 51.5573C17.1973 50.0323 12.6443 46.7243 9.57188 42.2033C6.49941 37.6823 5.09983 32.2314 5.61425 26.7894C6.04184 22.266 7.76959 17.9842 10.5604 14.4479C11.5031 13.2535 11.5179 11.5179 10.442 10.442V10.442Z" stroke="white" stroke-width="6" mask="url(#path-2-inside-1_1945_5452)"/> --}}
                        {{--                </svg> --}}
                        {{--              </span> активных компонентов<br/> --}}
                        {{--          <span class="inline-block mt-2">в составах наших продуктов</span></div> --}}
                        <div
                            class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-[42px] h-[42px] xl:w-[58px] xl:h-[58px] observed inline hidden">
                        </div>

                        <div
                            class="font-semibold xl:font-medium uppercase mb-8 mt-8 leading-1.6 text-17 xl:text-2xl line-margin-content">
                            @php
                                $string = $content->text_data['activeComponentsPercentage'];

                                // Поиск и замена шаблона [80%]

                                // Разделение строки на отдельные строки и заключение их в <span></span>
                                $lines = explode("\n", $string);
                                foreach ($lines as $key => $line) {
                                    if (preg_match('/\[(\d+)%\]/', $line, $matches)) {
                                        $percentage = $matches[1];
                                        $replacement = generateSVG(
                                            $percentage,
                                            58,
                                            5,
                                            'absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-[42px] h-[42px] xl:w-[58px] xl:h-[58px] observed',
                                        );
                                        $replacement =
                                            '<div class="percent-round relative inline-block align-middle mx-3 xl:mx-4 leading-none -translate-y-1 text-19 xl:text-2xl font-bold"><div class="text-19 xl:text-2xl font-bold">' .
                                            $percentage .
                                            '%</div>' .
                                            $replacement .
                                            '</div>';
                                        $line = str_replace($matches[0], $replacement, $line);
                                    }
                                    $lines[$key] = $line;
                                    if ($key == count($lines) - 1) {
                                        $lines[$key] = '<div class="inline">' . $line . '</div>';
                                    }
                                }

                                // Объединение обработанных строк обратно
                                $result = implode("\n", $lines);

                                echo $result;
                            @endphp</div>
                        <div class="italic leading-1.2">{{ $content->text_data['activeComponentsPercentage2'] ?? '' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    {{--  @if (getSettings('happyCoupon')) --}}
    {{--  <div class="px-8 lg:px-14 xl:px-16 py-12 md:py-14 lg:py-16"> --}}
    {{--    <div> --}}
    {{--      <h4 class="m-headline-3 d-headline-4 text-center mb-6 sm:mb-9 md:mb-12">Введите код за покупку в магазине</h4> --}}
    {{--      <form action="{{ route('happy_coupon.store') }}" method="post" class="max-w-[649px] mx-auto"> --}}
    {{--        <div class="border-b border-b-myGreen flex items-center pb-2 md:pb-4"> --}}
    {{--          <input type="text" name="store-coupon" id="store-coupon-field" placeholder="XXXXXXX-XXXX-XXXX" --}}
    {{--                 class="placeholder-myGreen w-full text-xl border-0 px-0 py-0.5 md:py-1.5 placeholder-black bg-transparent focus:ring-0 lh-none"> --}}
    {{--          <button type="submit" data-field="store-coupon-field" class="shrink-0 label-button"> --}}
    {{--            <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg"> --}}
    {{--              <path d="M10.5552 18L16.5 12L10.5552 6L9.5 7.065L14.3896 12L9.5 16.935L10.5552 18Z" fill="#6C715C"/> --}}
    {{--            </svg> --}}
    {{--          </button> --}}
    {{--        </div> --}}
    {{--      </form> --}}
    {{--    </div> --}}
    {{--    <script> --}}
    {{--      document.addEventListener('DOMContentLoaded', () => { --}}
    {{--        const storeCodeInput = document.getElementById('store-coupon-field'); --}}

    {{--        storeCodeInput.addEventListener('input', (event) => { --}}
    {{--          const target = event.target; --}}
    {{--          let value = target.value.toUpperCase(); // Convert to uppercase --}}
    {{--          value = value.replace(/[^A-Z0-9]/g, ''); // Remove non-alphanumeric characters --}}

    {{--          // Apply the mask --}}
    {{--          let parts = []; --}}
    {{--          parts.push(value.substring(0, 7)); --}}
    {{--          if (value.length > 7) parts.push(value.substring(7, 11)); --}}
    {{--          if (value.length > 11) parts.push(value.substring(11, 15)); --}}

    {{--          target.value = parts.join('-').toUpperCase(); // Set the formatted value --}}
    {{--        }); --}}
    {{--      }); --}}
    {{--    </script> --}}
    {{--  </div> --}}
    {{--  @endif --}}
    {{--  @if (auth()->check() && auth()->id() == 1) --}}
    {{--  <div class="py-6 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]"> --}}
    {{--    <div class="flex flex-row gap-2 mb-6 md:mb-10 draggable"> --}}
    {{--      <div class="flex flex-col items-center px-[13px] max-w-[170px] relative"> --}}
    {{--        <a href="{{ config('app.url') }}/catalog?in_stock=on" class="block absolute w-full h-full left-0 top-0 z-10"></a> --}}
    {{--        @if (isset($content->image_data['instock']['size'])) --}}
    {{--          <div class="item-square rounded-full overflow-hidden w-16 md:w-[138px]"> --}}
    {{--            <input type="hidden" data-id="menuImage" class="json-image" --}}
    {{--                   value="{{ e(json_encode($content->image_data['instock']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover" data-alt="В наличии"> --}}
    {{--          </div> --}}
    {{--        @else --}}
    {{--          <div class="item-square rounded-full overflow-hidden w-16 md:w-[138px]"> --}}
    {{--            <img src="{{ asset('img/category.jpg') }}" alt=""> --}}
    {{--          </div> --}}
    {{--        @endif --}}
    {{--        <div class="text-center text-xs md:text-[22px] mt-1.5 sm:mt-3 pb-1">В наличии</div> --}}
    {{--      </div> --}}
    {{--      @foreach ($addData->categories as $category) --}}
    {{--        <div class="flex flex-col items-center px-[13px] max-w-[170px] relative"> --}}
    {{--          <a href="{{ route('catalog.category', $category->slug) }}" class="block absolute w-full h-full left-0 top-0 z-10"></a> --}}
    {{--          @if (isset($category->options['menuImage']['size'])) --}}
    {{--            <div class="item-square rounded-full overflow-hidden w-16 md:w-[138px]"> --}}
    {{--              <input type="hidden" data-id="menuImage" class="json-image" --}}
    {{--                     value="{{ e(json_encode($category->options['menuImage']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover" data-alt="{{ $category->title }}"> --}}
    {{--            </div> --}}
    {{--          @else --}}
    {{--            <div class="item-square rounded-full overflow-hidden w-16 md:w-[138px]"> --}}
    {{--              <img src="{{ asset('img/category.jpg') }}" alt=""> --}}
    {{--            </div> --}}
    {{--          @endif --}}
    {{--          <div class="text-center text-xs md:text-[22px] mt-1.5 sm:mt-3 pb-1">{{ $category->title }}</div> --}}
    {{--        </div> --}}
    {{--      @endforeach --}}
    {{--      <div class="flex flex-col items-center px-[13px] max-w-[170px] relative"> --}}
    {{--        <a href="{{ route('product.vouchers') }}" class="block absolute w-full h-full left-0 top-0 z-10"></a> --}}
    {{--        @if (isset($content->image_data['vouchers']['size'])) --}}
    {{--          <div class="item-square rounded-full overflow-hidden w-16 md:w-[138px]"> --}}
    {{--            <input type="hidden" data-id="menuImage" class="json-image" --}}
    {{--                   value="{{ e(json_encode($content->image_data['vouchers']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover" data-alt="Подарочные сертификаты"> --}}
    {{--          </div> --}}
    {{--        @else --}}
    {{--          <div class="item-square rounded-full overflow-hidden w-16 md:w-[138px]"> --}}
    {{--            <img src="{{ asset('img/category.jpg') }}" alt=""> --}}
    {{--          </div> --}}
    {{--        @endif --}}
    {{--        <div class="text-center text-xs md:text-[22px] mt-1.5 sm:mt-3 pb-1">Подарочные сертификаты</div> --}}
    {{--      </div> --}}
    {{--      <div class="flex flex-col items-center px-[13px] max-w-[170px] relative"> --}}
    {{--        <a href="{{ route('product.presents') }}" class="block absolute w-full h-full left-0 top-0 z-10"></a> --}}
    {{--        @if (isset($content->image_data['presents']['size'])) --}}
    {{--          <div class="item-square rounded-full overflow-hidden w-16 md:w-[138px]"> --}}
    {{--            <input type="hidden" data-id="menuImage" class="json-image" --}}
    {{--                   value="{{ e(json_encode($content->image_data['presents']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover" data-alt="Наши презенты"> --}}
    {{--          </div> --}}
    {{--        @else --}}
    {{--          <div class="item-square rounded-full overflow-hidden w-16 md:w-[138px]"> --}}
    {{--            <img src="{{ asset('img/category.jpg') }}" alt=""> --}}
    {{--          </div> --}}
    {{--        @endif --}}
    {{--        <div class="text-center text-xs md:text-[22px] mt-1.5 sm:mt-3 pb-1">Наши презенты</div> --}}
    {{--      </div> --}}
    {{--    </div> --}}
    {{--  </div> --}}
    {{--  @endif --}}
    <div class="py-6 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]">
        <div id="swiper-block-categories" class="swiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide flex flex-col items-center px-[13px] max-w-[170px] w-auto relative">
                    <a href="{{ config('app.url') }}/catalog?in_stock=on"
                        class="block absolute w-full h-full left-0 top-0 z-10"></a>
                    @if (isset($content->image_data['instock']['size']))
                        <div class="item-square rounded-full overflow-hidden w-16 md:w-[138px]">
                            <input type="hidden" data-id="menuImage" class="json-image"
                                value="{{ e(json_encode($content->image_data['instock']['size'], JSON_UNESCAPED_UNICODE)) }}"
                                data-picture-class="block object-cover" data-alt="В наличии">
                        </div>
                    @else
                        <div class="item-square rounded-full overflow-hidden w-16 md:w-[138px]">
                            <img src="{{ asset('img/category.jpg') }}" alt="">
                        </div>
                    @endif
                    <div class="text-center text-xs md:text-[22px] mt-1.5 sm:mt-3 pb-1 w-16 md:w-[138px]">В наличии
                    </div>
                </div>
                @foreach ($addData->categories as $category)
                    <div class="swiper-slide flex flex-col items-center px-[13px] max-w-[170px] w-auto relative">
                        <a href="{{ route('catalog.category', $category->slug) }}"
                            class="block absolute w-full h-full left-0 top-0 z-10"></a>
                        @if (isset($category->options['menuImage']['size']))
                            <div class="item-square rounded-full overflow-hidden w-16 md:w-[138px]">
                                <input type="hidden" data-id="menuImage" class="json-image"
                                    value="{{ e(json_encode($category->options['menuImage']['size'], JSON_UNESCAPED_UNICODE)) }}"
                                    data-picture-class="block object-cover" data-alt="{{ $category->title }}">
                            </div>
                        @else
                            <div class="item-square rounded-full overflow-hidden w-16 md:w-[138px]">
                                <img src="{{ asset('img/category.jpg') }}" alt="">
                            </div>
                        @endif
                        <div class="text-center text-xs md:text-[22px] mt-1.5 sm:mt-3 pb-1 w-16 md:w-[138px]">
                            {{ $category->title }}</div>
                    </div>
                @endforeach
                <div class="swiper-slide flex flex-col items-center px-[13px] max-w-[170px] w-auto relative">
                    <a href="{{ route('product.vouchers') }}"
                        class="block absolute w-full h-full left-0 top-0 z-10"></a>
                    @if (isset($content->image_data['vouchers']['size']))
                        <div class="item-square rounded-full overflow-hidden w-16 md:w-[138px]">
                            <input type="hidden" data-id="menuImage" class="json-image"
                                value="{{ e(json_encode($content->image_data['vouchers']['size'], JSON_UNESCAPED_UNICODE)) }}"
                                data-picture-class="block object-cover" data-alt="Подарочные сертификаты">
                        </div>
                    @else
                        <div class="item-square rounded-full overflow-hidden w-16 md:w-[138px]">
                            <img src="{{ asset('img/category.jpg') }}" alt="">
                        </div>
                    @endif
                    <div class="text-center text-xs md:text-[22px] mt-1.5 sm:mt-3 pb-1 w-16 md:w-[138px]">Подарочные
                        сертификаты</div>
                </div>
                <div class="swiper-slide flex flex-col items-center px-[13px] max-w-[170px] w-auto relative">
                    <a href="{{ route('product.presents') }}"
                        class="block absolute w-full h-full left-0 top-0 z-10"></a>
                    @if (isset($content->image_data['presents']['size']))
                        <div class="item-square rounded-full overflow-hidden w-16 md:w-[138px]">
                            <input type="hidden" data-id="menuImage" class="json-image"
                                value="{{ e(json_encode($content->image_data['presents']['size'], JSON_UNESCAPED_UNICODE)) }}"
                                data-picture-class="block object-cover" data-alt="Наши презенты">
                        </div>
                    @else
                        <div class="item-square rounded-full overflow-hidden w-16 md:w-[138px]">
                            <img src="{{ asset('img/category.jpg') }}" alt="">
                        </div>
                    @endif
                    <div class="text-center text-xs md:text-[22px] mt-1.5 sm:mt-3 pb-1 w-16 md:w-[138px]">Наши презенты
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        new Swiper('#swiper-block-categories', {
            slidesPerView: 'auto',
            spaceBetween: 8,
            mousewheel: {
                releaseOnEdges: true
            },
            freeMode: true,
            breakpoints: {
                768: {
                    spaceBetween: 8,
                },
                1024: {
                    spaceBetween: 8,
                },
            },
        });
    </script>
    @if (isset($addData->weRecommend2))
        <div data-onload="swiperProductCarousel2"
            class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-12 md:py-14 lg:py-16 xl:py-[86px]">
            <div>
                <div class="mb-6 md:mb-12">
                    <h2 class="text-center m-headline-2 d-headline-2 lh-outline-none">Новинки в категориях</h2>
                </div>
                <div id="swiper-product-carousel2" class="swiper overflow-visible">
                    <div class="swiper-wrapper overflow-hidden">
                        @foreach ($addData->weRecommend2 as $product)
                            <div class="swiper-slide product-item-swiper__slide h-auto pb-px">
                                <x-public.product-item id="{{ $product->id }}"
                                    class="w-full h-full flex flex-col justify-between" :product="$product" />
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-button-next swiper-buttom-outside backdrop-blur-sm top-1/3"></div>
                    <div class="swiper-button-prev swiper-buttom-outside backdrop-blur-sm top-1/3"></div>
                </div>
                {{--        <div class="flex flex-wrap -mx-2 md:-mx-3 -my-2 md:-my-3"> --}}
                {{--          @foreach ($addData->vigodno as $product) --}}
                {{--            <x-public.product-item id="{{ $product->id }}" class="w-1/2 md:w-1/3 xl:w-1/4 px-2 md:px-3 py-2 md:py-3" :product="$product"/> --}}
                {{--          @endforeach --}}
                {{--        </div> --}}
            </div>
        </div>
        <script>
            function swiperProductCarousel2() {
                if (document.getElementById('swiper-product-carousel2')) {
                    new Swiper('#swiper-product-carousel2', {
                        slidesPerView: 2,
                        spaceBetween: 24,
                        mousewheel: {
                            releaseOnEdges: true
                        },
                        preloadImages: false,
                        lazy: true,
                        cssMode: true,
                        navigation: {
                            nextEl: ".swiper-button-next",
                            prevEl: ".swiper-button-prev",
                        },
                        breakpoints: {
                            640: {
                                slidesPerView: 3,
                            },
                            768: {},
                            1024: {
                                slidesPerView: 4,
                            },
                        },
                        on: {
                            init: handleVisibleSlides,
                            slideChange: handleVisibleSlides,
                        }
                    });

                    function handleVisibleSlides() {
                        const {
                            activeIndex,
                            params,
                            slides
                        } = this;
                        const visibleSlides = [];

                        for (let i = 0; i < params.slidesPerView; i++) {
                            const slideIndex = (activeIndex + i) % slides.length;
                            visibleSlides.push(slides[slideIndex]);
                        }

                        visibleSlides.forEach((slide) => {
                            const innerSwiperEl = slide.querySelector('.product-item-swiper');
                            if (innerSwiperEl) {
                                slide.innerSwiperInitialized = new Swiper(innerSwiperEl, window.swiperOptions);
                            }
                        });
                    }
                    // function handleVisibleSlides() {
                    //   const visibleSlides = this.slides.filter((slide) => {
                    //     // Используем встроенные методы библиотеки для определения видимых слайдов
                    //     return slide.classList.contains('swiper-slide-visible');
                    //   });
                    //
                    //   visibleSlides.forEach((slide) => {
                    //     const innerSwiperEl = slide.querySelector('.product-item-swiper');
                    //     if (innerSwiperEl) {
                    //       slide.innerSwiperInitialized = new Swiper(innerSwiperEl, window.swiperOptions);
                    //     }
                    //   });
                    // }
                }
            }
        </script>
    @endif
    {{--  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-6 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]"> --}}
    {{--    <div class="flex md:items-center justify-center"> --}}
    {{--      @if (isset($content->image_data['categoriesBigImage']['size'])) --}}
    {{--      <input type="hidden" data-id="categoriesBigImage" class="json-image" value="{{ e(json_encode($content->image_data['categoriesBigImage']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block w-[41%] sm:w-[36.5591%]" data-img-class="block object-cover h-full"> --}}
    {{--      @endif --}}
    {{--        <div class="space-y-1.5 sm:space-y-3 md:space-y-4 lg:space-y-5 xl:space-y-6 mx-2 md:mx-6"> --}}
    {{--        <h3 class="text-lg md:text-xl lg:text-3xl xl:text-[44px] uppercase lh-outline-none">Категории</h3> --}}
    {{--        <ul class="list-disc ml-4 text-myBrown"> --}}
    {{--                    <a href="{{ config('app.url') }}/catalog?preorder=on" class="hidden  text-myBrown d-headline-3 m-text-body font-medium lh-base whitespace-nowrap">Оформить предзаказ</a> --}}
    {{--          <li><a href="{{ config('app.url') }}/catalog" class="block text-myBrown d-headline-3 m-text-body font-medium lh-base">Смотреть все</a></li> --}}
    {{--                    <a href="{{ config('app.url') }}/catalog/new" class="hidden  text-myBrown d-headline-3 m-text-body font-medium lh-base">New</a> --}}
    {{--          <li><a href="{{ config('app.url') }}/catalog?in_stock=on" class="block text-myBrown d-headline-3 m-text-body font-medium lh-base">В наличии</a></li> --}}
    {{--          @foreach ($addData->categories as $category) --}}
    {{--            <li><a href="{{ route('catalog.category', $category->slug) }}" class="block text-myBrown d-headline-3 m-text-body font-medium lh-base">{{ $category->title }}</a></li> --}}
    {{--          @endforeach --}}
    {{--          <li><a href="{{ route('product.vouchers') }}" class="block text-myBrown d-headline-3 m-text-body font-medium lh-base">Сертификаты</a></li> --}}
    {{--        </ul> --}}
    {{--      </div> --}}
    {{--        <div></div> --}}
    {{--      @if (isset($content->image_data['categoriesSmallImage']['size'])) --}}
    {{--      <input type="hidden" data-id="categoriesSmallImage" class="json-image" value="{{ e(json_encode($content->image_data['categoriesSmallImage']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block w-[19.5121%] hidden md:block"> --}}
    {{--      @endif --}}
    {{--    </div> --}}
    {{--  </div> --}}
    @if (isset($addData->vigodno) && $addData->vigodno->isNotEmpty())
        <div data-onload="swiperProductCarousel"
            class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-12 md:py-14 lg:py-16 xl:py-[86px]">
            <div>
                <div class="mb-6 md:mb-12">
                    <h2 class="text-center m-headline-2 d-headline-2 lh-outline-none">Бестселлеры</h2>
                </div>
                <div id="swiper-product-carousel" class="swiper overflow-visible">
                    <div class="swiper-wrapper overflow-hidden">
                        @foreach ($addData->vigodno as $product)
                            <div class="swiper-slide product-item-swiper__slide h-auto">
                                <x-public.product-item id="{{ $product->id }}"
                                    class="w-full h-full flex flex-col justify-between" :product="$product" />
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-button-next swiper-buttom-outside backdrop-blur-sm"></div>
                    <div class="swiper-button-prev swiper-buttom-outside backdrop-blur-sm"></div>
                </div>
                {{--        <div class="flex flex-wrap -mx-2 md:-mx-3 -my-2 md:-my-3"> --}}
                {{--          @foreach ($addData->vigodno as $product) --}}
                {{--            <x-public.product-item id="{{ $product->id }}" class="w-1/2 md:w-1/3 xl:w-1/4 px-2 md:px-3 py-2 md:py-3" :product="$product"/> --}}
                {{--          @endforeach --}}
                {{--        </div> --}}
            </div>
        </div>
        <script>
            function swiperProductCarousel() {
                if (document.getElementById('swiper-product-carousel')) {
                    new Swiper('#swiper-product-carousel', {
                        slidesPerView: 2,
                        spaceBetween: 24,
                        mousewheel: {
                            releaseOnEdges: true
                        },
                        preloadImages: false,
                        lazy: true,
                        cssMode: true,
                        navigation: {
                            nextEl: ".swiper-button-next",
                            prevEl: ".swiper-button-prev",
                        },
                        breakpoints: {
                            640: {
                                slidesPerView: 3,
                            },
                            768: {},
                            1024: {
                                slidesPerView: 4,
                            },
                        },
                    });
                }
            }
        </script>
    @endif

    @if (isset($addData->weRecommend))
        <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-12 md:py-14 lg:py-16 xl:py-[86px]">
            <div>
                <div class="mb-6 md:mb-12">
                    <h2 class="text-center m-headline-2 d-headline-2 lh-outline-none">Мы рекомендуем</h2>
                </div>
                <div class="flex flex-wrap -mx-2 md:-mx-3 -my-2 md:-my-3">
                    @foreach ($addData->weRecommend as $product)
                        <x-public.product-item id="{{ $product->id }}"
                            class="w-1/2 md:w-1/3 xl:w-1/4 px-2 md:px-3 py-2 md:py-3" :product="$product" />
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 pt-6 sm:pt-10 md:pt-14 lg:pt-16 xl:pt-[86px]">
        <div>
            <div class="border-t border-myBrown mb-6 mx-auto w-[188px]"></div>
            <div class="md:my-12">
                <h2 class="text-center md:text-lg lg:text-xl xl:text-40 uppercase">{!! nl2br($content->text_data['production']) !!}</h2>
            </div>
            <div class="border-t border-myBrown my-6 mx-auto w-[318px] mb-14"></div>
            <div class="relative mx-auto max-w-[935px]">
                @if (isset($content->image_data['uniqueWorkingRecipes']['size']))
                    <div class="item-square uniqueWorkingRecipes">
                        <input type="hidden" data-id="uniqueWorkingRecipes" class="json-image"
                            value="{{ e(json_encode($content->image_data['uniqueWorkingRecipes']['size'], JSON_UNESCAPED_UNICODE)) }}"
                            data-picture-class="block object-cover relative w-full min-h-[255px]"
                            data-img-class="absolute left-0 top-0 sm:relative sm:left-auto sm:top-auto h-full w-full">
                    </div>
                @endif
                <div
                    class="absolute bottom-0 left-0 w-full backdrop-blur-sm bg-myCutomBrown md:px-2.5 py-2 md:py-4 text-white text-center lg:text-xl xl:text-40 uppercase text-lg !leading-relaxed">
                    {!! nl2br($content->text_data['production2']) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 pt-12 md:py-14 lg:py-16 xl:py-[86px]">
        <div class="flex-col flex md:flex-row-reverse justify-between md:-mx-3 lg:-mx-7">
            <div class="w-full lg:w-1/2 md:px-3 lg:px-7 relative md:mb-0 mb-5">
                @isset($content->text_data['dermatologists-img-text'])
                    <div
                        class="absolute z-10 left-5 sm:left-3 md:left-4 lg:left-8 xl:left-12 top-5 md:top-4 lg:top-8 xl:top-12 text-17 md:text-xl lg:text-2xl xl:text-28 italic !leading-1.6">
                        {!! nl2br($content->text_data['dermatologists-img-text']) !!}</div>
                @endisset
                <div class="item-square dermgstImage">
                    @if (isset($content->image_data['dermatologistsImg']['size']))
                        {{--            <input type="hidden" data-id="new_products_line" class="json-image" value="{{ e(json_encode($content->image_data['dermatologistsImg']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover max-h-[255px] sm:max-h-none relative" data-img-class="max-h-[255px] sm:max-h-none h-full w-full"> --}}
                        <input type="hidden" data-id="new_products_line" class="json-image"
                            value="{{ e(json_encode($content->image_data['dermatologistsImg']['size'], JSON_UNESCAPED_UNICODE)) }}"
                            data-picture-class="block object-cover relative" data-img-class="h-full w-full">
                    @endif
                </div>
            </div>
            <div class="w-full lg:w-1/2 md:px-3 lg:px-7">
                <div
                    class="bg-myGreen !leading-1.6 md:text-xl lg:text-2xl xl:text-32 m-text-body text-center mb-11 pt-7 pb-6 px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 -mx-2 sm:-mx-4 md:m-0 flex flex-col justify-center md:justify-between md:py-20 h-full">
                    <div class="lh-outline-none">
                        <a @if (!auth()->check()) href="javascript:;" data-src="#dermatologists-modal" data-fancybox-no-close-btn @else href="https://t.me/dermatolog_lm_bot" @endif
                            class="uppercase underline underline-offset-4 hover:no-underline outline-none">проконсультироваться
                            с <br />врачом-дерматологом</a>
                    </div>
                    @isset($content->text_data['dermatologists-text'])
                        <div class="mt-12 leading-1.6 opacity-64">
                            {!! $content->text_data['dermatologists-text'] !!}
                        </div>
                    @endisset
                </div>
            </div>


        </div>
    </div>


    <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 pb-12 md:py-14 lg:py-16 xl:py-[86px]">
        <div class="flex-col md:flex-row flex items-center justify-between">
            <div class="hidden md:block md-item-square w-full md:max-w-[39.4817%] md:mb-0 mb-5">
                @if (isset($content->image_data['new_products_line']['size']))
                    <input type="hidden" data-id="new_products_line" class="json-image"
                        value="{{ e(json_encode($content->image_data['new_products_line']['size'], JSON_UNESCAPED_UNICODE)) }}"
                        data-picture-class="block object-cover max-h-[255px] sm:max-h-none relative"
                        data-img-class="max-h-[255px] sm:max-h-none h-full w-full">
                @endif
                {{--        <img src="{{ asset('img/home/img-05.jpg?1') }}" alt="" class="block object-cover"> --}}
            </div>
            <div class="xl:ml-[108px] lg:ml-24 md:ml-20 text-center md:text-left">
                <div class="m-headline-3 d-headline-4 lh-outline-none">{!! nl2br($content->text_data['new_products_line']) !!}</div>
                <div class="m-headline-1 d-headline-1 my-6 text-myBrown">LE MOUSSE</div>
                <div class="border-t border-myBrown my-5 w-[135px] mb-6 mx-auto md:mx-0"></div>
                {{--        <div class="flex flex-col items-center space-y-8 md:space-y-0 md:flex-row md:space-x-14 mb-6"> --}}
                {{--          <img src="{{ asset('/img/product-icons/i-1.png') }}" alt=""> --}}
                {{--          <img src="{{ asset('/img/product-icons/i-2.png') }}" alt=""> --}}
                {{--          <img src="{{ asset('/img/product-icons/i-3.png') }}" alt=""> --}}
                {{--        </div> --}}
                <div class="m-text-body d-text-body">{!! nl2br($content->text_data['new_products_line2']) !!}</div>
            </div>
        </div>
    </div>
    <div data-onload="swiper2" class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-12 md:py-14 lg:py-16 xl:py-[86px]">
        <div>
            <div class="mb-6 md:mb-12">
                <h2 class="text-center m-headline-2 d-headline-2 lh-outline-none">Новости</h2>
            </div>
            <div>
                <div id="swiper-2" class="swiper swipre-1/3-2/3 overflow-visible">
                    <div class="swiper-wrapper overflow-hidden">
                        @if (isset($articles))
                            @foreach ($articles as $key => $slide)
                                <div class="swiper-slide product-item-swiper__slide h-auto">
                                    <x-public.blog-item id="{{ $slide->id }}"
                                        class="w-full h-full flex flex-col justify-between" :article="$slide" />
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <div class="swiper-button-next swiper-buttom-outside backdrop-blur-sm !top-1/4"></div>
                    <div class="swiper-button-prev swiper-buttom-outside backdrop-blur-sm !top-1/4"></div>
                </div>
            </div>
        </div>
    </div>
    {{--  <div class="hidden px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-12 md:py-14 lg:py-16 xl:py-[86px]"> --}}
    {{--    <div> --}}
    {{--      <div class="mb-6 md:mb-12"> --}}
    {{--        <h2 class="text-center m-headline-2 d-headline-2 lh-outline-none">Наши новинки</h2> --}}
    {{--      </div> --}}
    {{--      <div> --}}
    {{--        <div id="swiper-2" class="swiper swipre-1/3-2/3 overflow-visible"> --}}
    {{--          <div class="swiper-wrapper overflow-hidden"> --}}
    {{--            @if (isset($content->carousel_data['ourNewProducts'])) --}}
    {{--              @php($counter = 0) --}}
    {{--              @foreach ($content->carousel_data['ourNewProducts'] as $key => $slide) --}}
    {{--                @if (!isset($slide['image']['size'])) --}}
    {{--                  @continue --}}
    {{--                @endif --}}
    {{--                  <div class="swiper-slide @if ($counter % 2 == 0) slide-width-1-3 @else slide-width-2-3 @endif"> --}}
    {{--                  <div class="swiper-slide h-auto"> --}}
    {{--                    <a href="{{ $slide['link'] ?? '#' }}" class="block before-md-item-square w-full h-full"> --}}
    {{--                      @if (isset($slide['image']['size'])) --}}
    {{--                        <input type="hidden" data-id="ourNewProducts-{{ $key }}" class="json-image" value="{{ e(json_encode($slide['image']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block w-full h-full object-cover object-center"> --}}
    {{--                   @endif --}}
    {{--                    </a> --}}
    {{--                    <div class="absolute bg-black bg-opacity-20 left-0 top-0 w-full h-full pointer-events-none"></div> --}}
    {{--                    <div class="absolute bottom-0 left-0 w-full pl-6 pb-6 md:pb-10 text-white"> --}}
    {{--                      @if (isset($slide['headline']) && !empty($slide['headline'])) --}}
    {{--                        <div class="mb-6"><h3 class="d-headline-4 m-headline-3 lh-outline-none lh-none">{{ $slide['headline'] }}</h3></div> --}}
    {{--                      @endif --}}
    {{--                      @if (isset($slide['subtitle']) && !empty($slide['subtitle'])) --}}
    {{--                        <div class="d-subtitle-1 m-subtitle-1">{{ $slide['subtitle'] }}</div> --}}
    {{--                      @endif --}}
    {{--                    </div> --}}
    {{--                  </div> --}}
    {{--                @php($counter++) --}}
    {{--              @endforeach --}}
    {{--            @endif --}}
    {{--          </div> --}}
    {{--          <div class="swiper-button-next swiper-buttom-outside backdrop-blur-sm"></div> --}}
    {{--          <div class="swiper-button-prev swiper-buttom-outside backdrop-blur-sm"></div> --}}
    {{--        </div> --}}
    {{--      </div> --}}
    {{--    </div> --}}
    {{--  </div> --}}

    @include('_parts.public.mailingSubscribe')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('firstSlider')) {
                new Swiper('#firstSlider', {
                    slidesPerView: 1,
                    initialSlide: 0,
                    preloadImages: false,
                    lazy: false,
                    cssMode: true,
                    mousewheel: true,
                    // autoplay: {
                    //   delay: 5000,
                    //   pauseOnMouseEnter: true,
                    //   disableOnInteraction: false,
                    // },
                    navigation: {
                        nextEl: ".swiper-button-next",
                        prevEl: ".swiper-button-prev",
                    },
                    pagination: {
                        el: ".swiper-pagination",
                    },
                });
            }
        });

        function swiper2() {
            if (document.getElementById('swiper-2')) {
                new Swiper('#swiper-2', {
                    slidesPerView: 2, // на больших экранах
                    spaceBetween: 24, // расстояние между слайдами
                    breakpoints: {
                        640: {
                            slidesPerView: 2, // на мобильных устройствах
                        },
                        768: {
                            slidesPerView: 3, // на мобильных устройствах
                        },
                    },
                    preloadImages: false,
                    lazy: false,
                    cssMode: true,
                    mousewheel: true,
                    navigation: {
                        nextEl: ".swiper-button-next",
                        prevEl: ".swiper-button-prev",
                    },
                });
            }
        }
    </script>
    @if (isset($content->text_data['checklist']['file']))
        <x-public.popup id="pdf-alert">
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
                <div class="d-headline-4 m-headline-3 mb-8">Чек - лист по подбору <br />косметики по типам кожи</div>
                <div class="mb-8">
                    <x-public.green-button href="{{ $content->text_data['checklist']['file'] }}" target="_blank"
                        onclick="Fancybox.close()">
                        Смотреть
                    </x-public.green-button>
                </div>
                <a href="#" id="hidden-alert"
                    class="text-myGreen2 underline hover:no-underline underline-offset-4">Больше не показывать</a>
            </div>
        </x-public.popup>
        <script>
            window.checklist = {
                checklist: @json($content->text_data['checklist']['file']),
                checklist_link: document.getElementById('checklist')
            }
        </script>
    @endif
</x-app-layout>
