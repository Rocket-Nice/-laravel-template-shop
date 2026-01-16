<x-light-layout>
  <style>
    body, html {
      background: #fff!important;
{{--      background: url({{ asset('img/happy_coupon/hc_bg.jpg') }});--}}
/*      color: #fff;*/
    }
    #show-coupone-btn {
      color: #000;
    }
  </style>
  <x-slot name="custom_vite">
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/happy_coupon/script.js'])
  </x-slot>
  <div id="loader" style="background: #F6F6F6;" class="z-10 fixed left-0 top-0 right-0 bottom-0 w-full h-full bg-white/90 flex justify-center items-center" style="z-index: 2000">
    <div class="text-center">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-loader block mx-auto" width="56" height="56" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
        <path d="M12 6l0 -3" />
        <path d="M16.25 7.75l2.15 -2.15" />
        <path d="M18 12l3 0" />
        <path d="M16.25 16.25l2.15 2.15" />
        <path d="M12 18l0 3" />
        <path d="M7.75 16.25l-2.15 2.15" />
        <path d="M6 12l-3 0" />
        <path d="M7.75 7.75l-2.15 -2.15" />
      </svg>
      <div class="d-headline-4 m-headline-3">Загрузка</div>
    </div>
  </div>
{{--  style="background: #F6F6F6;"--}}
  <div class="overflow-hidden w-full max-w-[500px] mx-auto min-h-screen py-12" >
    <div class="relative">
      <div class="text-center uppercase text-xl title-img">
{{--        ВЫБЕРИ СВОЙ СЧАСТЛИВЫЙ КОНВЕРТ<br class="hidden sm:inline"/>--}}
        ВЫБЕРИ СВОЙ Свиток LE&nbsp;MOUSSE
      </div>
{{--      <div class="text-center title-img">--}}
{{--        <img src="{{ asset('img/happy_coupon/hc_title.png') }}" alt="">--}}
{{--      </div>--}}
      <div class="coupones-grid" style="padding-bottom: 200px;">
        @foreach($prizes_grid as $rows)
          <div class="flex coupones-row justify-center">
            @foreach($rows as $item)
              <div class="coupone-item img">
                <img src="{!! $item !!}" alt="" style="transform: scale(1.4)">
              </div>
            @endforeach
          </div>
        @endforeach
      </div>
{{--      style="margin-top: 3em;"--}}
      <div class="text-center mb-4">
        <a class="h-11 lg:h-[58px] text-center inline-flex items-center justify-center px-3 md:px-5 px-4 bg-white border border-customBrown text-customBrown rounded-full text-sm sm:text-base md:text-lg lg:text-xl !leading-none font-medium uppercase no-underline" id="show-coupone-btn" @if(!$attempts_left){!! 'href="'.route('cabinet.home.index').'"' !!}@else{!! 'style="display: none;"' !!}@endif><span class="text">@if(!$attempts_left){!! 'Личный кабинет' !!}@else{!! 'Далее' !!}@endif</span></a>
{{--        <div class="message-coupones-left">У вас осталось <span id="attempts">{{ $attempts_left }}</span> из {{ $order->giftCoupons()->count() }} попыток</div>--}}
        <div class="message-coupones-left">У вас осталось <span id="attempts">{{ $attempts_left }}</span> попытка</div>
      </div>
      <div class="flex flex-wrap" id="result-coupones">
        <div class="w-1/2 coupon-item">
          <div class="img" data-item="1"></div>
        </div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="2"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="3"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="4"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="5"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="6"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="7"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="8"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="9"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="10"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="11"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="12"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="13"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="14"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="15"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="16"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="17"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="18"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="19"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="20"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="20"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="21"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="22"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="23"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="24"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="25"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="26"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="27"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="28"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="29"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="30"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="31"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="32"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="33"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="34"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="35"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="36"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="37"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="38"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="39"></div></div>
      </div>
    </div>
  </div>
  <div style="display: none;">
    <img src="{{ asset('img/happy_coupon/animation_may/1-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/2-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/3-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/4-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/5-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/6-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/7-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/8-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/9-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/10-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/11-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/12-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/13-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/14-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/15-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/16-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/17-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/18-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/19-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/20-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/21-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/22-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/23-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/24-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/25-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/26-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/27-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/28-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/29-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/30-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/31-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/32-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/33-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/34-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/35-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/36-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/37-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/38-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/39-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/40-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/41-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/42-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/43-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/44-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/45-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/46-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/47-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/48-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/49-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/50-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/51-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/52-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/53-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/54-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/55-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/56-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/57-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/58-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/59-img.jpg?3') }}" class="preload-item__img">
    <img src="{{ asset('img/happy_coupon/animation_may/60-img.jpg?3') }}" class="preload-item__img">
  </div>

  <script>
    function isset(obj) {
      return typeof obj !== 'undefined' && obj !== null;
    }
    function myDataObject(){
      let _data = {}

      return {
        getObject() {
          return _data
        },
        getVar(key) {
          return isset(_data[key]) ? _data[key] : null
        },
        setVar(key, val){
          _data[key] = val
        }
      }
    }
    const myData = myDataObject();

    const rows = document.querySelectorAll('.coupones-grid .coupones-row');
    var is_processing = false;


    const openCoupone = (event) => {
      if (is_processing){return false;}
      is_processing = true;
      let elem = event.target;
      if (document.querySelector('.coupone.preload')){ //
        is_processing = false;
        return false;
      }
      checkPrize(myData.getVar('prizes').length + 1, elem);
    }
    const allCouponeItems = document.querySelectorAll('.coupones-grid .coupone-item img');
    for(var i = 0;i < allCouponeItems.length;i++){
      allCouponeItems[i].addEventListener('click', openCoupone);
    }

    const getOpenedPrizes = () => {
      var loader = document.getElementById('loader');
      loader.style.display = 'flex';
      window.ajax.post('{{ route('happy_coupon.opened', $order->slug) }}', {}, (response) => {
        let prizes = response.prizes;
        myData.setVar('prizes', prizes);
        myData.setVar('limit', response.limit);
        document.getElementById('attempts').textContent = response.limit - prizes.length;
        loader.style.display = 'none';
        for(var i = 0;i < prizes.length;i++){
          let count = prizes[i].position.count,
            item = document.querySelector('#result-coupones .img[data-item="'+count+'"]'),
            img = document.createElement('img'),
            coupone = document.createElement('div');
          coupone.innerText = prizes[i].coupone;
          coupone.style.color = '#A68773';
          coupone.style.textAlign = 'center'
          img.src = prizes[i].image;
          if (prizes[i].code == '0'){
            item.closest('.coupon-item').remove()
          }else{
            item.append(coupone);
            item.append(img);
          }
        }
      });

    }
    document.addEventListener('DOMContentLoaded', function() {
      getOpenedPrizes();
    });
    const checkPrize = (count, elem) => {
      let prizes = myData.getVar('prizes'),
        limit = myData.getVar('limit');
      if (prizes.length >= limit) {
        is_processing = false;
        return false;
      }
      window.ajax.post('{{ route('happy_coupon.open', $order->slug) }}', {count: count}, (response) => {
        if (response.error){
          is_processing = false;
          return false;
        }
        showPrize(elem);
        loadResult(response.prize.image, response.prize.coupone, response.prize.name)
        if (prizes === null) {
          prizes = []
        }
        prizes.push(response.prize);
        myData.setVar('attempts_left', response.attempts_left);
        document.getElementById('attempts').textContent = response.attempts_left;
        let btn = document.getElementById('show-coupone-btn');
        btn.setAttribute('onclick', 'nextCoupone('+count+')');
      });
    }

    function getElementPosition(element) {
      return {
        x: element.offsetLeft,
        y: element.offsetTop
      };
    }
    function getElementSize(element) {
      return {
        width: element.offsetWidth,
        height: element.offsetHeight
      };
    }

    const showPrize = (elem) => {
      let clone = elem.cloneNode(true),
        pos = getElementPosition(elem),
        size = getElementSize(elem),
        gridSize = getElementSize(document.querySelector('.coupones-grid'));

      const gridPrizes = document.querySelectorAll('.coupones-grid .coupones-row');
      gridPrizes.forEach(gridPrize => {
        gridPrize.style.opacity = 0
      })
      // document.style.background = '#F6F6F6'
      document.querySelector('.title-img').style.opacity = 0;
      document.body.style.background = '#F6F6F6'
      document.body.style.color = '#000'

      clone.classList.add('tempElement');

      let couponeBox = document.createElement('div');
      couponeBox.classList.add('coupone');
      couponeBox.classList.add('preload');
      couponeBox.style.position = 'absolute';
      couponeBox.style.left = pos.x+'px';
      couponeBox.style.top = pos.y+'px';
      couponeBox.style.width = size.width+'px';
      couponeBox.style.height = size.height+'px';
      couponeBox.style.transition = 'all 0.2s ease-out';

      let preloadItem = document.createElement('div');
      preloadItem.classList.add('preload-item');

      preloadItem.append(clone);
      couponeBox.append(preloadItem);
      document.querySelector('.coupones-grid').appendChild(couponeBox);
      setTimeout(() => {
        let couponWidth = size.width*2.3;
        let couponHeight = size.height*2.3;
        couponeBox.style.left = ((gridSize.width/2)-couponWidth/2)+'px';
        couponeBox.style.top = ((gridSize.height/2)-couponHeight/2)+'px';
        couponeBox.style.width = couponWidth+'px';
        couponeBox.style.height = couponHeight+'px';
      }, 100)
      setTimeout(() => {
        document.getElementById('show-coupone-btn').style.display = 'none';
      }, 400)
    }

    const nextCoupone = (count) => {
      const gridPrizes = document.querySelectorAll('.coupones-grid .coupones-row');
      gridPrizes.forEach(gridPrize => {
        gridPrize.style.opacity = null
      })
      document.querySelector('.title-img').style.opacity = null;
      document.body.style.background = null
      document.body.style.color = null
      let resultBox = document.querySelector('#result-coupones .img[data-item="'+count+'"]'),
        activeCoupone = document.querySelector('.coupone.preload'),
        image = activeCoupone.querySelectorAll('.preload-item img');
      image = image[image.length-1];
      image_src = image.src,
        image_clone = image.cloneNode(true);
      image_clone.style.opacity = 0;

      let coupone = document.createElement('div');
      coupone.innerText = image_clone.getAttribute('data-coupone');
      coupone.style.textAlign = 'center';
      coupone.style.color = '#A68773';
      let btn = document.getElementById('show-coupone-btn'),
        btnSize = getElementSize(btn)
      if (typeof image.dataset.remove != 'undefined' && image.dataset.remove == '1'){
        resultBox.closest('.w-1/2').remove()
      }else{
        resultBox.append(coupone);
        resultBox.append(image_clone);
        let size = getElementSize(resultBox),
          pos =  getElementPosition(resultBox);
        if (myData.getVar('attempts_left')!==0){
          btn.style.display = 'none';
          pos.y -= btnSize.height;
        }
        activeCoupone.style.position = 'absolute';
        activeCoupone.style.left = pos.x+'px';
        activeCoupone.style.top = pos.y+'px';
        activeCoupone.style.width = size.width+'px';
        activeCoupone.style.height = size.height+'px';
        activeCoupone.style.transition = 'all 0.2s ease-out';
      }

      setTimeout(() => {
        image_clone.style.opacity = 1;
        activeCoupone.remove();
        if (myData.getVar('attempts_left')===0){
          btn.removeAttribute('onclick');
          btn.setAttribute('href', '{{ route('cabinet.order.index') }}');
          btn.innerHTML = '<span class="text">Личный кабинет</span>';
        }
      }, 200);
    }
    {{--var coupones = document.querySelectorAll('.coupone');--}}
    {{--var is_processing = false;--}}

    function loadResult(prize_image, coupone_id, prize_name){
      let link = document.getElementById('show-coupone-btn');
      link.querySelector('.text').textContent = 'Загрузка';
      let boxPreload = document.querySelector('.coupones-grid .coupone');
      let allImagesElems = document.querySelectorAll('.preload-item__img');
      let allImages = [];
      allImagesElems.forEach((image) => {
        allImages.push(image.cloneNode(true))
      });

      new Promise((resolve, reject) => {
        const img = new Image();
        img.src = prize_image;
        if(prize_image=='0'){
          img.dataset.remove = 1;
        }
        img.onload = resolve;
      }).then((image) => {
        if (image.target != null && image.target.currentSrc !== null) {
          allImages.push(image.target);
        }else if(typeof image.path != 'undefined'){
          allImages.push(image.path[0]);
        }
        allImages.forEach(function(item, i, arr) {
          item.setAttribute('data-coupone', coupone_id);
          var preloadItem = document.createElement('div');
          var coupone = document.createElement('div');
          coupone.style.display = 'none';
          coupone.style.textAlign = 'center';
          coupone.style.background = '#F6F6F6';
          coupone.style.marginTop = '-10px';
          coupone.style.padding = '.5em';
          coupone.innerText = prize_name;
          preloadItem.classList.add('preload-item');
          preloadItem.style.display = 'none';
          preloadItem.append(item);
          preloadItem.append(coupone);
          boxPreload.append(preloadItem);
        });
        var elems = boxPreload.querySelectorAll('.preload-item');
        var start = 25;

        for(var i = 0;i<elems.length;i++){
          start += 25;
          if (i+1 == elems.length){
            elems[i].style.position = 'absolute'
            elems[i].style.left = '0'
            elems[i].style.top = '0'
            customFadeIn(elems[i], start)
            // customFadeIn(elems[i].querySelector('div'), start)
            // changeDisplay(elems[i].querySelector('div'), start);
          }else{
            if(i>0){
              changeDisplay(elems[i-1], start, 'none');
            }
            changeDisplay(elems[i], start);
          }

        }
        setTimeout(()=>{
          is_processing = false;
          document.getElementById('show-coupone-btn').style.display = 'inline-flex';
          link.querySelector('.text').textContent = 'Далее';
        }, start)
      });

    }

    {{--function miniaturesChecked(e){--}}

    {{--  var elem = e.target;--}}
    {{--  var miniatures = elem.closest('.miniatures');--}}
    {{--  var productName = elem.dataset.name;--}}

    {{--  document.querySelector('.miniatures-information').innerHTML = 'Вы выбрали <b>'+productName+'</b>!<br/>';--}}
    {{--  miniatures.classList.add('checked');--}}
    {{--  document.getElementById('saveMiniatures').style.display = 'block';--}}
    {{--}--}}

    {{--var boxPreload = document.getElementById('preload');--}}
    function loadImageAsync(url) {
      return new Promise((resolve, reject) => {
        const img = new Image();
        img.src = url;
        img.onload = resolve;
      });
    }

    function removeClass(elem, className, time){
      setTimeout(function(){
        elem.classList.remove(className);
      }, time);
    }
    function addClass(elem, className, time){
      setTimeout(function(){
        elem.classList.add(className);
      }, time);
    }
    function changeDisplay(item, time, display = 'block'){
      if (item) {
        setTimeout(function(){
          item.style.display = display;
        }, time);
      }
    }
    function customFadeIn(item, time){
      if (item) {
        setTimeout(function(){
          window.fadeIn(item, 2000)
        }, time);
      }
    }
  </script>
</x-light-layout>
