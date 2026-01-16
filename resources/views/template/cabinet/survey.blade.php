<x-light-layout>
{{--  <x-slot name="custom_vite">--}}
{{--    @vite(['resources/css/app.css', 'resources/js/helper.js', 'resources/js/app.js'])--}}
{{--  </x-slot>--}}
  <x-slot name="style">
    <link rel="stylesheet" href="{{ asset('libraries/@fancyapps/ui/dist/fancybox/fancybox.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  </x-slot>
  <x-slot name="script">
    <script src="{{ asset('libraries/@fancyapps/ui/dist/fancybox/fancybox.umd.js') }}"></script>
  </x-slot>
  <div class="pt-8 md:pt-16 lg:pt-20 xl:pt-24 pl-5 md:pl-16 lg:pl-24 xl:pl-28 bg-surveyGray h-[400px] sm:h-[410px]">
    <div id="main_image" class="absolute left-0 w-full h-[400px] sm:h-[410px] top-0">
      @if(isset($content->image_data['mainImage']['size']))
        <input type="hidden" data-id="mainImage" class="json-image" value="{{ e(json_encode($content->image_data['mainImage']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="hidden md:block w-full absolute left-0 top-0 h-[400px] sm:h-[410px] md:h-full " data-img-class="block w-full h-[400px] sm:h-[410px]  md:h-full object-cover object-right">
        <input type="hidden" data-id="mainImageMob" class="json-image" value="{{ e(json_encode($content->image_data['mainImageMob']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block md:hidden w-full absolute left-0 top-0 h-[400px] sm:h-[410px] md:h-full " data-img-class="block w-full h-[400px] sm:h-[410px]  md:h-full object-cover object-right">
      @endif
    </div>
    <div class="!leading-normal text-myDark relative z-10">
      <h1 class="uppercase font-semibold text-xl md:text-[35px] lg:text-[45px] xl:text-[65px] mb-2">Убедительная просьба</h1>
      <p class="!leading-normal font-montserrat text-[15px] md:text-[25px] lg:text-[30px] mb-20 md:mb-16">отвечать на вопросы честно <br class="sm:hidden"/>и объективно!</p>
      <p class="!leading-normal font-montserrat text-[15px] md:text-[20px]">Нам <span class="font-bold">очень важна</span> <br class="sm:hidden"/>реальная оценка нашего качества.</p>
    </div>
  </div>
  <div class="bg-myCustomGreen flex justify-center items-center text-white py-4 px-2 font-montserrat">
    <p class="!leading-normal text-[15px] md:text-[20px] lg:text-[30px] text-center">Оценка <span class="font-bold ">0</span> – очень плохо, <span class="font-bold ">10</span> – очень хорошо</p>
  </div>
  <div class="py-4 md:py-6 px-4 font-montserrat" style="padding-bottom: 320px;" x-data="surveyHandler">
    <div x-show="currentQuestion < questions.length-1" class="font-normal text-[15px] md:text-[20px] lg:text-[30px] font-semibold text-center text-myCustomGreen mb-2.5">
      <span x-text="currentQuestion+1"></span>/<span x-text="questions.length-1"></span>
    </div>
    <form action="{{ route('cabinet.survey.save', $survey->slug) }}" method="post" @submit.prevent="validateAndSubmit" x-ref="form">
      @csrf
      @foreach($questions as $index => $question)
        <div x-show="currentQuestion === {{ $index }}" data-type="radio" class="questions-item rounded-lg border border-myCustomGreen mb-12 max-w-7xl mx-auto text-myDark p-3 md:py-5 md:px-12">
          <div class="font-normal mb-8 text-[15px] md:text-[20px] lg:text-[30px]">{{ $question->text }}</div>
          <div class="flex items-center justify-between max-w-[334px] md:max-w-[710px] text-[15px] md:text-[20px] lg:text-[30px]">
            @for($i = 0;$i <= 10;$i++)
              <div>
                <input type="radio" @change="questionChange()" id="rating{{ $question->id.'-'.$i }}" name="questions[{{ $question->id }}]" value="{{ $i }}" class="hidden peer required" @if(isset(old('questions')[$question->id]) && old('questions')[$question->id] == $i){{ 'checked' }}@endif>
                <label for="rating{{ $question->id.'-'.$i }}" class="w-6 h-6 md:w-12 md:h-12 flex items-center justify-center border border-myCustomGreen rounded-md cursor-pointer peer-checked:bg-myCustomGreen peer-checked:text-white">{{ $i }}</label>
              </div>
            @endfor
          </div>
          <div class="question-comment mt-6" style="display: none;">
            <div class="font-normal mb-2.5 text-[15px] md:text-[20px] lg:text-[30px]">{{ $question->comment_text }}</div>
            <x-public.textarea name="comments[{{ $question->id }}]" class="auto-height border border-myCustomGreen rounded resize-none" style="height: auto" />
          </div>
        </div>
      @endforeach
      <div x-show="currentQuestion === {{ $index+1 }}" data-type="textarea" class="questions-item rounded-lg border border-myCustomGreen mb-12 max-w-7xl mx-auto text-myCustomGreen p-3 md:py-5 md:px-12">
        <div class="font-normal mb-2.5 text-[15px] md:text-[20px] lg:text-[30px]">Напишите, как в целом, мы можем стать лучше лично для вас?</div>
        <x-public.textarea name="survey-comment" class="auto-height border border-myCustomGreen rounded resize-none" style="height: auto" />
      </div>
      <div class="flex justify-center items-center text-center md:space-x-6">
        <button @click="previousQuestion" x-show="currentQuestion > 0" type="button" class="h-9 md:h-14 my-2 mx-2 bg-transparent border border-myCustomGreen text-center inline-flex items-center justify-center md:px-5 px-4 text-myCustomGreen rounded-full text-sm sm:text-base md:text-lg lg:text-xl !leading-none font-medium uppercase min-w-40" style="min-width: 140px">Назад</button>
        <button x-show="nextButton" type="button" x-ref="submitButton" @click="nextQuestion" x-bind:disabled="isSubmitting" x-text="button" class="h-9 md:h-14 my-2 mx-2 bg-myCustomGreen border-none text-center inline-flex items-center justify-center md:px-5 px-4 text-white rounded-full text-sm sm:text-base md:text-lg lg:text-xl !leading-none font-medium uppercase min-w-40" style="min-width: 140px"></button>
      </div>
      <p x-show="showError" x-text="errorMessage" style="color: red;"></p>
      <p x-show="showSuccess" x-text="successMessage" style="color: green;"></p>
    </form>
  </div>
  <x-public.popup id="survey-success" class="relative">
    <svg style="pointer-events: none; position: absolute; right: 0; bottom: 5%;" width="170" height="275" viewBox="0 0 170 275" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M167.738 0.884277C168.802 0.884277 169.2 1.5968 168.934 3.02185L168.536 5.15943C164.549 13.7097 158.967 24.3976 151.79 37.2231C137.17 63.4441 126.538 82.2548 119.893 93.6552C101.552 133.557 88.1287 163.625 79.6228 183.861C65.535 217.492 58.4911 241.576 58.4911 256.111C58.4911 264.091 60.6175 269.079 64.8705 271.074C65.9337 271.644 67.5286 271.929 69.655 271.929C78.9583 271.929 91.85 263.664 108.33 247.133C118.962 236.303 129.861 223.335 141.025 208.229C141.556 207.659 142.088 207.659 142.619 208.229C143.151 208.799 143.151 209.369 142.619 209.939C141.556 211.079 140.094 212.932 138.234 215.497C136.373 218.062 133.981 221.197 131.057 224.902C122.817 234.878 115.773 242.858 109.925 248.843C93.1791 266.229 79.7557 274.922 69.655 274.922C67.5286 274.922 65.6679 274.494 64.0731 273.639C58.4911 270.789 55.7001 264.946 55.7001 256.111C55.7001 241.291 62.8769 216.922 77.2306 183.006C88.3945 156.785 98.6281 133.699 107.931 113.748L107.134 115.031L60.4846 191.129C61.0163 191.984 61.2821 193.266 61.2821 194.976L60.8834 198.824C60.6175 199.679 60.6175 200.534 60.8834 201.389C61.1492 202.244 60.8834 202.814 60.0859 203.099C59.2885 203.384 58.7569 203.099 58.4911 202.244C58.2253 200.819 58.2253 199.537 58.4911 198.396L58.8898 194.976V193.694C40.8148 223.62 27.6573 243.856 19.4173 254.401C12.772 262.951 7.85459 267.654 4.66489 268.509H3.86746C2.00681 268.509 0.943573 267.654 0.677766 265.944C0.411957 265.374 0.279053 264.519 0.279053 263.379C0.279053 259.389 2.13971 253.119 5.86103 244.568C9.58234 235.733 13.8353 227.895 18.6198 221.055C36.6948 195.119 49.3207 184.003 56.4975 187.709L59.2885 189.846L105.14 113.748L118.697 91.0901C130.658 65.4391 141.157 44.0634 150.195 26.9627C159.498 9.57709 165.346 0.884277 167.738 0.884277ZM162.954 10.7171C164.549 7.86702 165.479 6.01445 165.745 5.15943C161.226 10.5746 150.992 29.5278 135.044 62.019L149.796 35.9405C154.315 27.9603 158.701 19.5525 162.954 10.7171ZM17.8224 252.691C24.2018 245.281 37.6251 225.045 58.0924 191.984C57.8266 191.414 57.0291 190.844 55.7001 190.274C52.5104 188.849 47.8587 190.844 41.7452 196.259C35.6316 201.674 28.4547 210.509 20.2147 222.765C16.2276 228.465 12.6391 235.02 9.44944 242.431C5.99393 249.841 3.86746 255.826 3.07004 260.386C2.80423 262.666 2.80423 264.234 3.07004 265.089C3.07004 265.659 3.33585 265.944 3.86746 265.944C7.05716 264.804 11.7088 260.386 17.8224 252.691Z" fill="#FAF4EE"/>
      <path d="M242.379 209.084C242.91 208.514 243.442 208.514 243.973 209.084C244.505 209.654 244.505 210.224 243.973 210.794C239.72 215.355 232.942 219.202 223.639 222.337C214.602 225.473 205.83 227.04 197.324 227.04C183.502 227.04 175.794 220.627 174.199 207.802L173.8 201.817C173.8 199.822 174.066 197.399 174.597 194.549C173.8 195.119 171.806 197.399 168.617 201.389V202.244C168.617 203.099 168.085 203.527 167.022 203.527C161.44 210.367 153.466 219.772 143.099 231.743C136.454 238.868 132.068 243.001 129.942 244.141C128.878 244.711 128.081 244.996 127.549 244.996C125.423 244.996 124.36 243.571 124.36 240.721C124.625 237.87 126.22 233.595 129.144 227.895C131.802 221.91 135.391 215.925 139.909 209.939C148.947 197.399 155.991 191.129 161.041 191.129C162.902 191.129 164.364 191.841 165.427 193.266C166.756 194.691 167.553 196.401 167.819 198.396L175.395 188.991C175.926 188.136 176.325 187.994 176.591 188.564C177.123 189.134 177.388 189.561 177.388 189.846V191.129L176.591 194.976C176.325 196.116 176.192 198.112 176.192 200.962C176.192 203.812 176.325 205.949 176.591 207.374C177.654 212.505 179.648 216.637 182.572 219.772C185.496 222.907 190.413 224.475 197.324 224.475C205.564 224.475 214.07 222.907 222.842 219.772C231.879 216.637 238.391 213.075 242.379 209.084ZM141.504 230.033C149.213 221.197 157.453 211.364 166.224 200.534C165.959 198.824 165.427 197.399 164.63 196.259C163.832 194.834 162.769 194.121 161.44 194.121C156.655 194.121 150.01 199.964 141.504 211.649C131.669 225.9 126.752 235.59 126.752 240.721C126.752 241.576 127.018 242.003 127.549 242.003C129.676 242.003 134.327 238.013 141.504 230.033Z" fill="#FAF4EE"/>
    </svg>

    <div class="relative z-10">
      <h3 class="uppercase text-lg md:text-2xl font-semibold text-center mb-10">СПАСИБО<br/>ЗА УДЕЛЕННОЕ ВРЕМЯ!</h3>
      <div class="text-sm md:text-lg text-center mb-10">
        Баллы будут начислены в Вашем<br/>
        личном кабинете<br/>
        в течение 3-х рабочих дней
      </div>
      <div class="text-center">
        <x-public.primary-button href="{{ route('cabinet.order.index') }}" class="sm:whitespace-nowrap w-40">Ок</x-public.primary-button>
      </div>
    </div>
  </x-public.popup>
  <x-public.popup id="custom-alert">
    <div class="m-text-body d-text-body text-center">
      <div class="mb-8" id="custom-alert__title"></div>
    </div>
  </x-public.popup>
</x-light-layout>
