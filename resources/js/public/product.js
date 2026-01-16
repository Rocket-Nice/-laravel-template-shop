// product info tooltip
document.addEventListener('DOMContentLoaded', function() {
  btnTooltipListener()
  btnCloseTooltipListener()
});
window.addEventListener('tooltipListener', function() {
  btnTooltipListener()
  btnCloseTooltipListener()
});
function btnTooltipListener() {
  const btnTooltips = document.querySelectorAll('.btn-tooltip');
  btnTooltips.forEach((btn) => {
    btn.removeEventListener('click', btnTooltipListenerHandler);
    btn.addEventListener('click', btnTooltipListenerHandler);
  });
}
function btnTooltipListenerHandler(e){
  const btn = e.currentTarget
  const tooltipId = btn.getAttribute('data-tooltip');
  const tooltip = document.getElementById(tooltipId);
  const arrow = tooltip.querySelector('.tooltip-arrow');

  if (tooltip.classList.contains('hidden')) {
    document.querySelectorAll('.tooltip').forEach((t) => {
      t.classList.add('hidden')
      var sl = t.closest('.swiper-slide')
      if(sl){
        sl.style.zIndex = null
      }
    });
    tooltip.classList.remove('hidden');
    const slide = tooltip.closest('.swiper-slide')
    if(slide){
      slide.style.zIndex = 100
    }
    // Позиционирование tooltip и стрелки относительно кнопки
    adjustTooltipPosition(btn, tooltip, arrow);
  } else {
    tooltip.classList.add('hidden');
    tooltip.style.left = null;
    const slide = tooltip.closest('.swiper-slide')
    if(slide){
      slide.style.zIndex = null
    }
  }
}
function btnCloseTooltipListener(){
  const closeTooltips = document.querySelectorAll('.close-tooltip');

  closeTooltips.forEach((closeBtn) => {
    closeBtn.removeEventListener('click', btnCloseTooltipListenerHandler)
    closeBtn.addEventListener('click', btnCloseTooltipListenerHandler);
  });
}
function btnCloseTooltipListenerHandler(e){
  const closeBtn = e.currentTarget
  const tooltip = closeBtn.closest('.tooltip');
  tooltip.classList.add('hidden');
  const slide = tooltip.closest('.swiper-slide')
  if(slide){
    slide.style.zIndex = null
  }
}
function adjustTooltipPosition(button, tooltip, arrow) {
  const btnRect = button.getBoundingClientRect();
  const tooltipWidth = tooltip.offsetWidth;
  const parentRect = button.parentElement.getBoundingClientRect();

  const centeredRightPosition = (parentRect.right - btnRect.right) + (btnRect.width / 2) - (tooltipWidth / 2);
  // Расчетное положение, чтобы подсказка была по центру относительно кнопки
  const centeredRightPositionFromScreen = btnRect.right + (btnRect.width / 2) + (tooltipWidth / 2);
  const centeredLeftPositionFromScreen = btnRect.left - (btnRect.width / 2) - (tooltipWidth / 2);

  // Определение, куда позиционировать
  let finalRightPosition;
  if (centeredLeftPositionFromScreen < 0) {
    finalRightPosition = tooltipWidth;
  } else if (centeredRightPositionFromScreen > window.innerWidth) {
    finalRightPosition = 0;
  } else {
    finalRightPosition = centeredRightPosition;
  }

  // Применяем позиционирование к подсказке
  tooltip.style.right = `${finalRightPosition}px`;

  // Позиционирование стрелки относительно свойства right
  const arrowRightPixel = (tooltip.getBoundingClientRect().right - (btnRect.left + btnRect.width / 2));
  arrow.style.right = `${arrowRightPixel}px`;
}
// product info tooltip end
// Добавление обработчика события resize с дебаунсингом
window.addEventListener('resize', () => window.debouncedResize(window.updateTextSize, 200));
window.updateTextSize();


function replaceSwiperImageClasses() {
  const elements = this.el.querySelectorAll('input.swiper-json-image');
  elements.forEach(el => {
    el.classList.replace('swiper-json-image', 'json-image');
  });
  window.generatePictureElements()
  // Отписываемся от события после первого вызова, чтобы изменения произошли только один раз
  this.off('slideChange', replaceSwiperImageClasses);
}

const swiperOptions = {
  // // Optional parameters
  // direction: 'vertical',
  // loop: true,
  wrapperClass: 'product-item-swiper__wrapper',
  slideClass: 'product-item-swiper__slide',
  slidesPerView: "auto",
  spaceBetween: 2,
  preloadImages: false,
  lazy: true,
  cssMode: true,
  mousewheel: true,
  // And if we need scrollbar
  // scrollbar: {
  //   el: '#products-01 .swiper-scrollbar',
  // },
  pagination: {
    clickable: true,
    el: ".product-item-swiper__pagination",
  },
  on: {
    slideChange: replaceSwiperImageClasses
  }
}
window.swiperOptions = swiperOptions;
// if(document.querySelectorAll('.product-item-swiper').length>0){
//   new Swiper('.product-item-swiper', swiperOptions);
// }
document.addEventListener("DOMContentLoaded", function() {
  const swipers = document.querySelectorAll('.product-item-swiper');

  swipers.forEach(function(swiper, index) {
    if (!swiper.closest('.product-item-swiper__slide')) {
      new Swiper(swiper, swiperOptions);
    } else {
      // // Если слайдер внутри другого слайда, не инициализируем пока
      // const parentSlide = swiper.closest('.product-item-swiper__slide');
      // // Находим родительский Swiper
      // const parentSwiperEl = parentSlide.closest('.swiper');
      // if (parentSwiperEl) {
      //   const parentSwiperInstance = parentSwiperEl.swiper; // получить инстанс родительского Swiper
      //   if (parentSwiperInstance) {
      //     parentSwiperInstance.on('slideChange', function () {
      //       if (parentSwiperInstance.slides[parentSwiperInstance.activeIndex] === parentSlide && !swiper.swiper) {
      //         // Инициализируем только, если слайд стал активным и слайдер ещё не был инициализирован
      //         new Swiper(swiper, swiperOptions);
      //       }
      //     });
      //   }
      // }
    }
  });
});
const subQty = document.getElementById('subQty')
if(subQty){
  subQty.addEventListener('click', (event) => {
    event.preventDefault()
    const field = document.getElementById(subQty.dataset.field)
    if (field.value > 1){
      field.value = Number(field.value) - 1
    }
  })
}
const addQty = document.getElementById('addQty')
if(addQty){
  addQty.addEventListener('click', (event) => {
    event.preventDefault()
    const field = document.getElementById(addQty.dataset.field)
    if(!field.value){
      field.value = 1
    }
    field.value = Number(field.value) + 1
  })
}

const loadMoreButton = document.getElementById('loadMore');
const route = window.loadRoute
const page = document.getElementById('loader-page')
const catalog = document.getElementById('catalog')

if(loadMoreButton&&route&&page&&catalog){
  const sortField = document.getElementById('order_by')
  var loading = false; // флаг для отслеживания состояния загрузки
  var loadMoreObserver;
  document.addEventListener('DOMContentLoaded', function() {

    loadMoreObserver = new IntersectionObserver(function(entries, observer) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting && !loading) {
          loadProducts()
        }
      });
    }, {
      rootMargin: "0px 0px 100px 0px"
    });

    loadMoreObserver.observe(loadMoreButton);
  });

  function pauseObservation() {
    if (loadMoreObserver) {
      loadMoreObserver.disconnect();
    }
  }
// Функция для возобновления наблюдения
  function resumeObservation() {
    if (loadMoreObserver && loadMoreButton) {
      loadMoreObserver.observe(loadMoreButton);
    }
  }

  var filtersDiv = document.getElementById('filter');
  var showResultsButton = document.getElementById('filterButton');
  var filtersInputs = filtersDiv.querySelectorAll('input');

  showResultsButton.addEventListener('click', (e) => {
    window.location.reload();
  })
  function loadProducts(update = false){

    loadMoreButton.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-loader block mx-auto" width="30" height="30" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M12 6l0 -3" />
              <path d="M16.25 7.75l2.15 -2.15" />
              <path d="M18 12l3 0" />
              <path d="M16.25 16.25l2.15 2.15" />
              <path d="M12 18l0 3" />
              <path d="M7.75 16.25l-2.15 2.15" />
              <path d="M6 12l-3 0" />
              <path d="M7.75 7.75l-2.15 -2.15" />
            </svg>`
    loading = true; // Установите флаг загрузки перед отправкой запроса
    // observer.unobserve(entry.target); // Отключите наблюдение временно
    pauseObservation()
    var url = route; // Укажите ваш URL
    var data = {
      page: page.value
    };
    filtersInputs.forEach(input => {
      if (input.name) { // Учитываем только те input элементы, у которых есть атрибут name
        if(input.type == 'text'||input.type == 'hidden'){
          data[input.name] = input.value; // Сохраняем значение input в объекте data
        }else if((input.type == 'checkbox' || input.type == 'radio')&&input.checked){
          data[input.name] = input.value; // Сохраняем значение input в объекте data
        }
      }
    });
    if(sortField.value != 'default') {
      data.orderBy = sortField.value
    }
    if(update){
      catalog.style.opacity = '0.5'
    }
    window.ajax.get(url, data, function(response) {
      // console.log(data);
      // console.log(response);

      if(update){
        catalog.innerHTML = ''
        catalog.style.opacity = ''
      }
      if(!response.data) {
        loadMoreButton.innerHTML = 'Показать еще'
        loadMoreButton.style.opacity = '0'
        return true;
      }
      const products = response.data

      products.forEach((product)=>{
        const productItem = createProductItem(product)
        catalog.appendChild(productItem)
      })
      window.generatePictureElements()
      window.listenCart();
      // btnTooltipListener()
      btnCloseTooltipListener()
      initializeSwiperOnNewContainers()
      loadMoreButton.innerHTML = 'Показать еще'
      page.value = response.current_page + 1
      loading = false; // Снимите флаг загрузки после получения ответа

      // Возобновите наблюдение
      // observer.observe(entry.target);

      if(response.current_page >= response.last_page) {
        loadMoreButton.style.opacity = '0'
        return true;
      }
      resumeObservation()
    });
  }
// сортировка
  sortField.addEventListener('change', function(e){
    page.value = 1
    catalog.innerHTML = ''
    loadProducts(true)
  })
// filters

  if(filtersDiv){
    filtersDiv.addEventListener('change', function(event) {
      if (event.target.tagName.toLowerCase() === 'input') {
        updateUrlWithFilters();
      }
    });
  }
  function checkFiltersChanged() {
    var changed = Array.from(filtersInputs).some(function(input) {
      var defaultValue = input.getAttribute('data-default');
      if (input.type === "checkbox") {
        return input.checked ? 1 : 0 != defaultValue;
      } else {
        return input.value !== defaultValue;
      }
    });
    if(changed){
      updateCountProducts();
    }else{
      showResultsButton.style.display = 'none';
    }
  }
  filtersInputs.forEach(function(input) {
    input.addEventListener('change', checkFiltersChanged);
    // Для текстовых полей может быть также полезно использовать событие 'input'
    if (input.type === "text") {
      input.addEventListener('input', checkFiltersChanged);
    }
  });
}
function updateCountProducts(){
  filtersDiv.querySelector('.flex').style.opacity = '0.5'
  filtersDiv.querySelector('.flex').style.pointerEvents = 'none'
  var url = route; // Укажите ваш URL
  var data = {
    getTotal: true
  };
  filtersInputs.forEach(input => {
    if (input.name) { // Учитываем только те input элементы, у которых есть атрибут name
      if(input.type == 'text'||input.type === "hidden"){
        data[input.name] = input.value; // Сохраняем значение input в объекте data
      }else if((input.type == 'checkbox' || input.type == 'radio')&&input.checked){
        data[input.name] = input.value; // Сохраняем значение input в объекте data
      }
    }
  });

  window.ajax.get(url, data, function(response) {
    if(response.total && response.total > 0){
      showResultsButton.innerText = `Показать (${window.denum(response.total, ['%d товар', '%d товара', '%d товаров'])})`
      showResultsButton.style.display = ''
      showResultsButton.style.pointerEvents = ''
    }else if(response.total == 0){
      showResultsButton.innerText = `Нет товаров`
      showResultsButton.style.display = ''
      showResultsButton.style.pointerEvents = 'none'
    }else{
      showResultsButton.style.display = 'none'
    }
    filtersDiv.querySelector('.flex').style.opacity = ''
    filtersDiv.querySelector('.flex').style.pointerEvents = ''
  });
}
function updateUrlWithFilters() {
  var inputs = document.querySelectorAll('#filter input');
  var params = new URLSearchParams(window.location.search);

  inputs.forEach(function(input) {
    if (input.type === "text") {
      if (input.value) {
        params.set(input.name, input.value);
      } else {
        params.delete(input.name);
      }
    }else{
      if (input.value&&input.checked) {
        params.set(input.name, input.value);
      } else {
        params.delete(input.name);
      }
    }
  });

  var newUrl = window.location.pathname + '?' + params.toString();
  window.history.pushState({path:newUrl}, '', newUrl);
}



function createProductItem(product){
  const productItem = document.createElement('div')
  productItem.className = 'w-1/2 md:w-1/3 lg:w-1/2 xl:w-1/3 px-2 md:px-3 py-2 md:py-3 flex flex-col justify-between'
  productItem.id = product.id


  // создаем карточки


  const productCards = document.createElement('div')
  productCards.innerHTML = `<div class="swiper product-item-swiper"><div class="swiper-wrapper product-item-swiper__wrapper"></div><div class="product-item-swiper__pagination swiper-pagination"></div></div>`
  const productCardsWrapper = productCards.querySelector('.swiper-wrapper')

  let firstSlide = document.createElement('div');
  firstSlide.className = 'swiper-slide product-item-swiper__slide';
  const firstSlideHtml = document.createElement('div')
  firstSlideHtml.className = 'product-card_item relative'
  let soldOut = '';
  if((!product.quantity || !product.status) && !!product.soon === false){
    soldOut = '<div class="text-sm uppercase bg-white absolute left-0 top-2 py-1 px-2 z-10">sold out</div>';
    firstSlideHtml.innerHTML = soldOut;
  }
  if(typeof window.puzzles != "undefined" && window.puzzles && product.puzzles && product.puzzles_count){
    firstSlideHtml.innerHTML = `<div class="flex items-center absolute z-10 text-myGreen2 text-lg" style="left: 9.625668449%; top:8.771929825%; color: ${typeof product.puzzle_color != "undefined" && product.puzzle_color ? product.puzzle_color : '#6C715C'}">` +
      '<svg style="width: 6.417112299%;" viewBox="0 0 24 32" fill="none" xmlns="http://www.w3.org/2000/svg">\n' +
      '                            <path opacity="0.64" fill-rule="evenodd" clip-rule="evenodd" d="M0.00221464 21.1217C-0.000738212 21.2221 -0.000738212 21.2989 0.00221464 21.3993C0.0619892 22.749 0.160797 24.0623 0.307682 25.3299C0.367037 25.8422 0.434241 26.347 0.509893 26.8438C0.533726 27.0003 0.558397 27.156 0.583926 27.3109H0.586878C2.45013 27.0038 4.42853 26.8 6.49553 26.7085H6.77309C8.22589 26.7351 9.39227 27.3906 9.3775 28.1731C9.37455 28.368 9.29778 28.5422 9.16195 28.7105C8.90505 29.0265 8.76627 29.3926 8.76627 29.7854C8.76627 31.0108 10.16 32 11.8815 32C13.603 32 14.9968 31.0078 14.9968 29.7854C14.9968 29.3897 14.858 29.0265 14.6011 28.7105C14.4653 28.5452 14.3885 28.368 14.3855 28.1731C14.3708 27.3906 15.5371 26.7351 16.9929 26.7085C17.0962 26.7055 17.1966 26.7085 17.297 26.7115C19.3522 26.8 21.3218 27.0185 23.1821 27.3109C23.2137 27.1187 23.2441 26.9253 23.2733 26.7306C23.3421 26.2704 23.4039 25.8033 23.459 25.3299C23.6065 24.0622 23.7059 22.749 23.7638 21.3993V21.1217C23.7342 19.666 23.0787 18.4996 22.2992 18.5144C22.1043 18.5173 21.9271 18.5941 21.7617 18.7299C21.4428 18.9868 21.0796 19.1256 20.6869 19.1256C19.4674 19.1256 18.4752 17.7318 18.4752 16.0103C18.4752 14.2888 19.4644 12.8951 20.6869 12.8951C21.0826 12.8951 21.4458 13.0309 21.7617 13.2908C21.9271 13.4266 22.1043 13.5004 22.2992 13.5063C23.0817 13.5211 23.7372 12.3547 23.7638 10.899C23.7667 10.7956 23.7638 10.6952 23.7608 10.5948C23.7031 9.25524 23.5989 7.95204 23.4524 6.69177C23.3801 6.06971 23.2975 5.45811 23.2051 4.85775C23.1975 4.80835 23.1898 4.75903 23.1821 4.70979C21.3218 4.99917 19.3522 5.19996 17.297 5.28855C17.1967 5.2915 17.0963 5.2915 16.9929 5.2915C15.5372 5.26493 14.3708 4.60939 14.3855 3.82689C14.3885 3.632 14.4653 3.45778 14.6011 3.28947C14.858 2.97352 14.9968 2.60736 14.9968 2.21464C14.9968 0.992157 13.603 0 11.8815 0C10.16 0 8.76627 0.992157 8.76627 2.21464C8.76627 2.61032 8.90505 2.97352 9.16195 3.28947C9.29778 3.45483 9.37455 3.632 9.3775 3.82689C9.39522 4.60939 8.22885 5.26493 6.77309 5.2915C6.6727 5.29445 6.59592 5.29445 6.49553 5.2915C4.64892 5.20972 2.87065 5.05253 1.18389 4.80097C0.982588 4.77095 0.782585 4.73958 0.583926 4.70684C0.482223 5.35507 0.391957 6.01616 0.313749 6.68925C0.167159 7.95087 0.0629338 9.25464 0.00516748 10.5948C0.00221464 10.6952 0.00221464 10.7956 0.00221464 10.899C0.0287903 12.3547 0.717496 13.5148 1.5 13.5C1.69489 13.497 1.83889 13.4266 2.00425 13.2908C2.3202 13.0339 2.6834 12.8951 3.07613 12.8951C4.29565 12.8951 5.28781 14.2888 5.28781 16.0103C5.28781 17.7318 4.29861 19.1256 3.07613 19.1256C2.6834 19.1256 2.3202 18.9868 2.00425 18.7299C1.83889 18.5941 1.66171 18.5173 1.46683 18.5144C0.684322 18.4996 0.0287903 19.666 0.00221464 21.1217ZM2.00222 10.8784C2.00369 10.9399 2.00713 10.9987 2.01216 11.0547C2.34727 10.9514 2.70358 10.8951 3.07613 10.8951C4.50493 10.8951 5.60571 11.7143 6.26964 12.6487C6.93714 13.5881 7.28781 14.7828 7.28781 16.0103C7.28781 17.2366 6.93858 18.4312 6.27125 19.3713C5.60681 20.3072 4.50556 21.1256 3.07613 21.1256C2.70338 21.1256 2.34688 21.0692 2.01161 20.9658C2.0065 21.0268 2.00311 21.091 2.00188 21.1582L2.00168 21.1694L2.00135 21.1805C1.99969 21.237 1.99956 21.2734 2.00097 21.3268C2.05829 22.6139 2.15179 23.8565 2.28843 25.048C3.61977 24.8862 4.99529 24.773 6.40704 24.7105L6.45126 24.7085H6.79138L6.80967 24.7088C7.80063 24.727 8.79322 24.9571 9.60583 25.414C10.3372 25.8252 11.4033 26.7292 11.3772 28.2071C11.3668 28.8375 11.147 29.3544 10.8788 29.75C11.0753 29.8744 11.4211 30 11.8815 30C12.3406 30 12.6862 29.8744 12.8833 29.7496C12.6088 29.3455 12.3962 28.827 12.3858 28.2077C12.3859 28.2087 12.3859 28.2098 12.3859 28.2108M13.0272 29.6341C13.031 29.6271 13.0338 29.6238 13.0342 29.6239C13.0346 29.6239 13.0327 29.6274 13.0272 29.6341ZM10.7291 29.6255C10.7295 29.6254 10.7322 29.6287 10.736 29.6355C10.7306 29.629 10.7287 29.6256 10.7291 29.6255ZM2.00222 10.8784C2.00224 10.7877 2.00246 10.7242 2.00397 10.6658C2.05911 9.39469 2.15751 8.15963 2.29507 6.96598C3.62973 7.12262 5.00412 7.22741 6.40704 7.28954L6.42188 7.2902L6.43673 7.29064C6.57265 7.29463 6.6862 7.29474 6.82095 7.29095C7.80887 7.27131 8.79796 7.04148 9.60805 6.586C10.3393 6.17486 11.4073 5.26898 11.3771 3.78855C11.366 3.17089 11.1536 2.65366 10.8798 2.25037C11.0768 2.1256 11.4224 2 11.8815 2C12.341 2 12.6868 2.1258 12.8838 2.25068C12.6157 2.64618 12.3962 3.16291 12.3858 3.79288C12.3597 5.27105 13.4262 6.1751 14.1582 6.58633C14.9713 7.04316 15.9645 7.27306 16.9564 7.29117L16.9746 7.2915H17.003C17.1 7.29151 17.2257 7.29151 17.3558 7.28768L17.3695 7.28728L17.3832 7.28669C18.7853 7.22626 20.1499 7.11539 21.4702 6.9609C21.6082 8.15589 21.7069 9.39334 21.7621 10.6671C21.7649 10.7661 21.7656 10.8072 21.7646 10.8418L21.7643 10.8521L21.7641 10.8625C21.7629 10.9295 21.7595 10.9934 21.7544 11.0542C21.4164 10.9504 21.0587 10.8951 20.6869 10.8951C19.2575 10.8951 18.1562 11.7134 17.4918 12.6494C16.8245 13.5895 16.4752 14.7841 16.4752 16.0103C16.4752 17.2379 16.8259 18.4325 17.4934 19.372C18.1573 20.3064 19.2581 21.1256 20.6869 21.1256C21.0612 21.1256 21.4183 21.0688 21.7537 20.9652C21.7587 21.0222 21.7621 21.082 21.7638 21.1445V21.3555C21.7081 22.6329 21.6143 23.8683 21.4776 25.0537C20.1594 24.8932 18.7921 24.774 17.3832 24.7133L17.3695 24.7127L17.3423 24.7119C17.251 24.7092 17.105 24.7049 16.9467 24.709C15.958 24.7284 14.9686 24.9583 14.1582 25.4137C13.4261 25.825 12.3595 26.7292 12.3858 28.2077M13.0342 2.37614C13.0338 2.37622 13.031 2.37289 13.0271 2.36591C13.0327 2.37257 13.0346 2.37606 13.0342 2.37614ZM10.7359 2.36591C10.732 2.37289 10.7293 2.37622 10.7289 2.37614C10.7284 2.37606 10.7304 2.37257 10.7359 2.36591Z" fill="currentColor"/>\n' +
      '                          </svg>' +
      `                          <span class="cormorantInfant ml-1.5">${product.puzzles_count}</span>` +
      '                        </div>';
  }else{
    let refillSticker = '';
    let newStructure = '';
    if(product.refill){
      let refill = product.refill;
      let refillImage;
      if(refill.style_page.mainImage.image){
        refillImage = JSON.stringify(refill.style_page.mainImage.image)
      }
      refillSticker = `<div>
                      <div x-show="!open" @click="open = !open" class="flex-1 flex items-center text-myDark bg-white text-sm py-1 px-1 leading-none border border-myDark bg-opacity-60">
                        есть refill <svg width="20" height="20" class="ml-1.5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M19.5 10C19.5 15.2239 15.2239 19.5 10 19.5C4.77614 19.5 0.5 15.2239 0.5 10C0.5 4.77614 4.77614 0.5 10 0.5C15.2239 0.5 19.5 4.77614 19.5 10Z" stroke="#2C2E35" stroke-linecap="round" stroke-linejoin="round"/>
                          <path d="M10 15V10" stroke="#2C2E35" stroke-linecap="round" stroke-linejoin="round"/>
                          <path d="M9.99609 7H10.0051" stroke="#2C2E35" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                      </div>
                      <div class="absolute z-10 p-3 w-full">
                        <div x-show="open" @click.outside="if(open) open = false" class="flex items-center text-myDark bg-white text-md leading-none border border-myDark">
                          <div class="item-square hidden sm:block" style="width: 40%;max-width: 111px;">
                            ${refillImage ? `<input type="hidden" class="json-image" value='${refillImage}' data-picture-class="lg:absolute lg:left-0 lg:top-0 w-full h-full block object-cover" data-img-class="block w-full">` : ''}
                          </div>
                          <div class="py-2 px-3">
                            <div class="mb-3">Дополнительно к продукту вы можете приобрести</div>
                            <div class="font-medium">${ refill.name }</div>
                          </div>
                        </div>
                      </div>
                    </div>`;

    }
    // console.log('product', product)
    if(!!product.soon && (!product.quantity || !product.status)){
      newStructure += `<div class="flex-1 flex items-center text-myDark bg-white text-sm py-1 px-1 leading-none border border-myDark">Скоро в продаже</div>`;
      // newStructure = ``;
      // firstSlideHtml.innerHTML = refillSticker;
    }
    if(product.new_structure === "1"){
      // console.log('product.new_structure', !!product.new_structure)
      newStructure += `<div class="flex-1 flex items-center text-myDark bg-white text-sm py-1 px-1 leading-none border border-myDark">новый состав</div>`;
      // newStructure = ``;
      // firstSlideHtml.innerHTML = refillSticker;
    }

    if(product.is_new === "1"){
      // console.log('product.new_structure', !!product.new_structure)
      newStructure += `<div class="flex-1 flex items-center text-myDark bg-white text-sm py-1 px-1 leading-none border border-myDark">Новинка</div>`;
      // newStructure = ``;
      // firstSlideHtml.innerHTML = refillSticker;
    }
    if(product.sale === "1"){
      // console.log('product.new_structure', !!product.new_structure)
      newStructure += `<div class="flex-1 flex items-center text-myDark bg-white text-sm py-1 px-1 leading-none border border-myDark">SALE</div>`;
      // newStructure = ``;
      // firstSlideHtml.innerHTML = refillSticker;
    }

    if(product.tag20 === "1"){
      newStructure += `<div class="flex-1 flex justify-end"> <img src="https://lemousse.shop/img/bf-1125/tag-20.jpg" alt="" style="width: 50px; " class="block"></div>`;
    }else if(product.tag30 === "1"){
      newStructure += `<div class="flex-1 flex justify-end"> <img src="https://lemousse.shop/img/bf-1125/tag-30.jpg" alt="" style="width: 50px; " class="block"></div>`;
    }else if(product.tag50 === "1"){
      newStructure += `<div class="flex-1 flex justify-end"> <img src="https://lemousse.shop/img/bf-1125/tag-50.jpg" alt="" style="width: 50px; " class="block"></div>`;
    }
    firstSlideHtml.innerHTML = `${soldOut}<div x-data="{ open: false }" :class="open ? '' : 'absolute'" class="z-10 top-2 right-1 flex flex-col space-y-0.5">${refillSticker}${newStructure}</div>`;
  }
  let firstSlideImage;
  if(product.category_id !== 32 && product.type_id === 1){
    firstSlideImage = document.createElement('a')
    firstSlideImage.className = 'img product_card_preview item-square block'
    firstSlideImage.href = `/product/${product.slug}`
  }else{
    firstSlideImage = document.createElement('div')
    firstSlideImage.className = 'img product_card_preview item-square block'
  }

  let imageInput = document.createElement('input');
  imageInput.type = 'hidden';
  imageInput.dataset.id = `cardImage-${product.id}`;
  imageInput.className = 'json-image';
  if(product.cardImage && product.cardImage.image){
    imageInput.value = JSON.stringify(product.cardImage.image);
  }else{
    imageInput.value = '[]'
  }
  imageInput.dataset.pictureClass = 'block object-cover';
  imageInput.dataset.imgClass = 'block w-full object-cover';
  firstSlideImage.appendChild(imageInput)
  firstSlideHtml.appendChild(firstSlideImage)
  firstSlide.appendChild(firstSlideHtml)
  productCardsWrapper.appendChild(firstSlide);
  const style_cards_keys = Object.keys(product.style_cards);
  for (const key of style_cards_keys) {
    const card = product.style_cards[key]
    let slide = document.createElement('div');
    slide.className = 'swiper-slide product-item-swiper__slide';

    let cardItem = document.createElement('div');
    cardItem.className = 'product-card_item relative';

    let productDiv = document.createElement('div');
    productDiv.className = `product_${card.card_style} relative bg-slate-100 overflow-hidden max-w-sm`;

    let cardFieldsDiv = document.createElement('div');
    cardFieldsDiv.className = `card_fields z-10 absolute top-0 left-0 w-full ${card.card_style == 'card-style-5' ? card['vertical-align'] : 'h-full'}`;
    cardFieldsDiv.style = card.style || '';

    if (card.fields) {
      const fields_keys = Object.keys(card.fields);
      for (const k of fields_keys) {
        const field = card.fields[k]
        let fieldDiv = document.createElement('div');
        fieldDiv.className = `cormorantGaramond lh-base product-description-item ${field.align || ''} ${field['vertical-align'] || ''}`;
        fieldDiv.style = field.style || '';
        fieldDiv.innerHTML = field.text.replace(/\n/g, '<br>'); // Convert newline characters to <br> tags
        cardFieldsDiv.appendChild(fieldDiv);
      }
    }

    if (card['big-text']) {
      let bigTextDiv = document.createElement('div');
      bigTextDiv.className = `cormorantGaramond lh-base product-description-bigText ${card['big-text-vertical-align'] || ''} ${card['big-text-align'] || ''}`;
      bigTextDiv.style = card['big-text-style'] || '';
      bigTextDiv.innerHTML = card['big-text'].replace(/\n/g, '<br>');
      cardFieldsDiv.appendChild(bigTextDiv);
    }

    if (card.text) {
      let textDiv = document.createElement('div');
      textDiv.className = `cormorantGaramond lh-base product-description-item ${card.align || ''} ${card['vertical-align'] || ''}`;
      textDiv.style = card['text-style'] || '';
      textDiv.innerHTML = card.text.replace(/\n/g, '<br>');
      cardFieldsDiv.appendChild(textDiv);
    }

    if (card['small-text']) {
      let smallTextDiv = document.createElement('div');
      smallTextDiv.className = `cormorantGaramond lh-base product-description-smallText ${card['small-text-vertical-align'] || ''} ${card['small-text-align'] || ''}`;
      smallTextDiv.style = card['small-text-style'] || '';
      smallTextDiv.innerHTML = card['small-text'].replace(/\n/g, '<br>');
      cardFieldsDiv.appendChild(smallTextDiv);
    }

    productDiv.appendChild(cardFieldsDiv);

    if (card.image) {
      let imageDiv = document.createElement('div');
      imageDiv.className = 'img product_card_preview item-square block';

      let imageInput = document.createElement('input');
      imageInput.type = 'hidden';
      imageInput.dataset.id = `productImage-${product.id}-${key}`;
      imageInput.className = 'swiper-json-image';
      imageInput.value = JSON.stringify(card.image);
      imageInput.dataset.pictureClass = 'block object-cover';
      imageInput.dataset.imgClass = 'block w-full';

      imageDiv.appendChild(imageInput);
      productDiv.appendChild(imageDiv);
    }

    cardItem.appendChild(productDiv);
    slide.appendChild(cardItem);
    productCardsWrapper.appendChild(slide);
  }
  // создаем карточки end
  const productTitle = document.createElement('div')
  productTitle.className = `mb-4 mt-4 md:mt-6 flex-1`
  if(product.category_id !== 32 && product.type_id === 1){
    productTitle.innerHTML = `<h3 class="break-words font-light text-lg sm:text-2xl md:text-3xl lg:text-32 min-h-[52px] pr-2 lh-outline-none"><a href="/product/${product.slug}">${product.name}</a></h3>`
  }else{
    productTitle.innerHTML = `<h3 class="break-words font-light text-lg sm:text-2xl md:text-3xl lg:text-32 min-h-[52px] pr-2 lh-outline-none">${product.name}</h3>`
  }

  const productStockStatus = document.createElement('div');
  // if(product.quantity > 0 && product.status === 1 && product.stockStatus){
  //   productStockStatus.className = `text-base sm:mt-2 mb-4`;
  //   productStockStatus.style.color = product.stockStatus.color;
  //   productStockStatus.innerHTML = `<div class="cormorantInfant text-myDark">${product.stockStatus.text ?? "&nbsp;" }</div>
  //     <div>
  //       <svg style="width: 100%;" height="4" viewBox="0 0 100% 4" fill="none" xmlns="http://www.w3.org/2000/svg">
  //         <rect width="100%" height="4" fill="#B2B2B2" fill-opacity="0.25"/>
  //         <rect width="${product.stockStatus.percent }%" height="4" fill="currentColor"/>
  //       </svg>
  //     </div>`;
  // }
  // создаем цену и описание
  let relativeDiv = document.createElement('div');
  relativeDiv.className = 'relative flex items-center justify-between mb-4 md:mb-6';

  let priceParent = document.createElement('div');


  let pirceContainer = document.createElement('div')
  pirceContainer.className = 'flex space-x-3 md:space-x-4 items-center w-full justify-between pr-1'
  let oldPriceDiv = document.createElement('div');
  oldPriceDiv.className = 'text-base sm:text-md md:text-lg cormorantInfant italic font-semibold text-myGray line-through';
  if(product.old_price){
    oldPriceDiv.innerHTML = window.formatPrice(product.old_price);
    priceParent.appendChild(oldPriceDiv)
  }
  let priceDiv = document.createElement('div');
  priceDiv.className = 'subtitle-1 text-myBrown';
  priceDiv.innerHTML = window.formatPrice(product.price);
  priceParent.appendChild(priceDiv)
  pirceContainer.appendChild(priceParent)
  let volume = document.createElement('div')
  volume.className = 'text-sm md:text-base cormorantInfant font-medium italic opacity-50'
  volume.innerText = product.volume ? product.volume : ''
  pirceContainer.appendChild(volume);
  relativeDiv.appendChild(pirceContainer);

  let tooltipLink;
  let tooltipId = product.id;

  if (product.cardsDescription) {
    tooltipLink = document.createElement('a');
    tooltipLink.className = 'btn-tooltip';
    tooltipLink.dataset.tooltip = `tooltip-${tooltipId}`;
    tooltipLink.innerHTML = `<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 19C14.95 19 19 14.95 19 10C19 5.05 14.95 1 10 1C5.05 1 1 5.05 1 10C1 14.95 5.05 19 10 19Z" stroke="#B1908E" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M10 14.5V10" stroke="#B1908E" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M9.99512 7.30078H10.0032" stroke="#B1908E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>`
  } else if(product.category_id === 32) {
    tooltipLink = document.createElement('a');
    tooltipLink.href = `/product/${product.slug}`; // Use the actual routing function or URL
    tooltipLink.className = 'btn-tooltip';
    tooltipLink.innerHTML = `<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 19C14.95 19 19 14.95 19 10C19 5.05 14.95 1 10 1C5.05 1 1 5.05 1 10C1 14.95 5.05 19 10 19Z" stroke="#B1908E" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M10 14.5V10" stroke="#B1908E" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M9.99512 7.30078H10.0032" stroke="#B1908E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>`
  }

  relativeDiv.appendChild(tooltipLink);

  if (product.cardsDescription) {
    let tooltipDiv = document.createElement('div');
    tooltipDiv.id = `tooltip-${tooltipId}`;
    tooltipDiv.className = 'tooltip hidden absolute bottom-full mb-4 shadow-md p-6 bg-white w-screen max-w-[260px] md:max-w-[386px] z-10';
    tooltipDiv.innerHTML = `<div class="flex justify-between items-center mb-3 md:mb-6">
                <h4 class="headline-4">о продукте</h4>
                <button class="close-tooltip">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.0003 18.3327C14.5837 18.3327 18.3337 14.5827 18.3337 9.99935C18.3337 5.41602 14.5837 1.66602 10.0003 1.66602C5.41699 1.66602 1.66699 5.41602 1.66699 9.99935C1.66699 14.5827 5.41699 18.3327 10.0003 18.3327Z" stroke="#2C2E35" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M7.6416 12.3592L12.3583 7.64258" stroke="#2C2E35" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12.3583 12.3592L7.6416 7.64258" stroke="#2C2E35" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>`
    // ... add children to tooltipDiv here, including the header, close button, description, icons, and button

    // Example for adding description
    let descriptionDiv = document.createElement('div');
    descriptionDiv.className = 'mb-3 md:mb-6 subtitle-2';
    descriptionDiv.innerHTML = product.cardsDescription; // Use innerHTML carefully
    tooltipDiv.appendChild(descriptionDiv);

    // Example for adding icons
    let iconsDiv = document.createElement('div');
    iconsDiv.className = 'flex flex-col justify-between space-y-3 mb-3 md:mb-6 subtitle-2';
    if(product.cardsDescriptionIcons){
      const icons_keys = Object.keys(product.cardsDescriptionIcons);
      for (const k of icons_keys) {
        const icon = product.cardsDescriptionIcons[k]
        let iconDiv = document.createElement('div');
        iconDiv.className = 'flex items-center';

        let iconImage = document.createElement('img');
        iconImage.src = icon.icon;
        iconImage.alt = icon.text;
        iconImage.className = 'w-[18px] h-[18px]';

        let iconTextDiv = document.createElement('div');
        iconTextDiv.textContent = icon.text;

        iconDiv.appendChild(iconImage);
        iconDiv.appendChild(iconTextDiv);
        iconsDiv.appendChild(iconDiv);
      }
    }
    tooltipDiv.appendChild(iconsDiv);

    // Example for adding 'Read more' button, replace 'x-public.primary-button' with actual button creation
    if(product.category_id !== 32 && product.type_id === 1){
      let readMoreButton = document.createElement('a');
      readMoreButton.href = `/product/${product.slug}`; // Use the actual routing function or URL
      readMoreButton.className = 'h-11 inline-flex items-center justify-center px-3 md:px-4 px-7 border border-black text-xl leading-none font-medium w-full h-8';
      readMoreButton.textContent = 'Читать подробнее';
      tooltipDiv.appendChild(readMoreButton);
    }

    let tooltipArrow = document.createElement('div');
    tooltipArrow.className = 'absolute tooltip-arrow w-3 h-3 -bottom-1 right-1/2 transform translate-x-1/2 rotate-45 bg-white shadow-sm';
    tooltipDiv.appendChild(tooltipArrow)
    relativeDiv.appendChild(tooltipDiv);
  }
  const buttonItem = document.createElement('div')
  var buttonAddToCart;
  if(checkProductAvailability(product)){
    if(product.product_options && product.product_options.productSize) {
      buttonAddToCart = `<a href="/product/${product.slug}" class="h-11 inline-flex items-center justify-center px-3 md:px-4 px-7 border border-black text-xl leading-none font-medium w-full"><span class="text-base sm:text-xl whitespace-nowrap">Выбрать размер</span></a>`
      buttonItem.innerHTML = buttonAddToCart
    }else{
      if(product.preorder == true){
        buttonAddToCart = `<a href="javascript:;" data-fancybox="preorder-${product.id}" data-src="#preorder-${product.id}" class="h-11 inline-flex items-center justify-center px-3 md:px-4 px-7 border border-black text-xl leading-none font-medium w-full"><span class="text-sm sm:text-xl whitespace-nowrap">Оформить предзаказ</span></a>
            <div id="preorder-${product.id}" class="d-text-body m-text-body max-w-3xl" style="display: none;">
                <div class="p-2 sm:p-4">
                    <div class="mb-4">
                        <h3 class="d-headline-4 m-headline-3">Внимание</h3>
                        <p>Ожидаемая отправка с 5 по 17 декабря<br/>После отгрузки товара, статус вашего заказа изменится в личном кабинете</p>
                    </div>
                    <a href="#" class="h-11 inline-flex items-center justify-center px-3 md:px-4 px-7 border border-black text-xl leading-none font-medium w-full toCart" data-id="${product.id}">В корзину</a>
                </div>
            </div>
        `
      }else{
        buttonAddToCart = `<a href="#" class="h-11 inline-flex items-center justify-center px-3 md:px-4 px-7 border border-black text-xl leading-none font-medium w-full toCart" data-id="${product.id}">В корзину</a>`
      }

      buttonItem.innerHTML = buttonAddToCart
      // buttonItem.querySelector('.toCart').addEventListener('click', (event) => {
      //   window.toCartHandler(event)
      // })
    }
  }else{
    if(window.auth.check){
      if(product.notification){
        buttonAddToCart = `<div class="text-center text-xl bg-gray-200 text-gray-500 h-11 flex justify-center items-center"><button onclick="window.productNotification(this, '${product.slug}', 'remove')">Сообщим о поступлении</button></div>`
      }else{
        buttonAddToCart = `<div class="text-center text-xl bg-gray-200 text-gray-500 h-11 flex justify-center items-center"><button onclick="window.productNotification(this, '${product.slug}', 'set')">Узнать о поступлении</button></div>`
      }
    }else{
      buttonAddToCart = `<div class="text-center text-xl bg-gray-200 text-gray-500 h-11 flex justify-center items-center"><a href="javascript:;" class="outline-none" data-src="#authForm" onclick="window.productNotificationBeforeAuth('${product.slug}')" data-fancybox-no-close-btn>Узнать о поступлении</a></div>`
    }

    buttonItem.innerHTML = buttonAddToCart
  }

  productItem.appendChild(productCards)
  productItem.appendChild(productTitle)
  productItem.appendChild(productStockStatus)
  productItem.appendChild(relativeDiv)
  productItem.appendChild(buttonItem)
  return productItem
}
function initializeSwiperOnNewContainers() {
  // Находим все контейнеры Swiper
  const swiperContainers = document.querySelectorAll('.product-item-swiper');

  swiperContainers.forEach((container) => {
    // Проверяем, есть ли уже инициализированный Swiper в контейнере
    if (!container.swiper) {
      // Если Swiper не инициализирован, создаём новый экземпляр
      new Swiper(container, swiperOptions);
    }
  });
}

function checkProductAvailability(product) {
  // Проверяем статус и количество товара в целом
  if (product.status > 0 && product.quantity > 0) {
    return true;
  }

  // Перебираем ключи объектов data_status и data_quantity и проверяем их значения
  for (let statusKey in product.data_status) {
    if (product.data_status.hasOwnProperty(statusKey)) {
      const suffix = statusKey.replace('status', '');
      const quantityKey = `quantity${suffix}`;
      if (product.data_quantity.hasOwnProperty(quantityKey)) {
        const isStatusPositive = product.data_status[statusKey] > 0;
        const isQuantityPositive = product.data_quantity[quantityKey] > 0;

        if (isStatusPositive && isQuantityPositive) {
          return true;
        }
      }
    }
  }

  // Если не найдено ни одного положительного статуса и количества, возвращаем false
  return false;
}

document.addEventListener('DOMContentLoaded', function() {
  function handleFileSelect(evt) {
    var files = evt.target.files; // FileList object

    // Loop through the FileList and render image files as thumbnails.
    for (var i = 0, f; f = files[i]; i++) {

      // Only process image files.
      if (!f.type.match('image.*')) {
        continue;
      }

      var reader = new FileReader();

      // Closure to capture the file information.
      reader.onload = (function(theFile) {
        return function(e) {
          // Render thumbnail.
          var span = document.createElement('span');
          span.innerHTML = ['<img src="', e.target.result,
            '" title="', theFile.name, '" class="w-12 h-12 rounded-full object-cover"/>'].join('');
          const thumb = evt.target.closest('.field_file').querySelector('.file_thumb')
          thumb.innerHTML = ''
          evt.target.closest('.field_file').querySelector('.file_thumb').insertBefore(span, null);
        };
      })(f);

      // Read in the image file as a data URL.
      reader.readAsDataURL(f);
    }
  }
  [...document.querySelectorAll('.inputfile')].forEach(function(item) {
    item.addEventListener('change', handleFileSelect, false);
  });
});


window.productNotificationBeforeAuth = (product) => {
  const form = document.getElementById('authForm').querySelector('form')
  const input = document.createElement('input')
  input.type = 'hidden';
  input.name = 'productNotification';
  input.value = product
  form.appendChild(input)
}
var productNotificationSending = false;

function swapSetRemove(str) {
  return str.replace(/('set'|'remove')\)/, (match) => {
    return match === "'set')" ? "'remove')" : "'set')";
  });
}


window.productNotification = (elem, product, action) => {
  if(productNotificationSending){
    return false;
  }
  productNotificationSending = true;
  var params = {
    product: product,
    action: action,
    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
  };
  window.ajax.post(window.products.notification, params, (response) => {
    if (response) {
      if (response.success) {
        let toast_params = {
          message: response.message,
          type: 'success',
        };
        new Toast(toast_params);
      }else{
        new Toast({
          message: response.message,
          type: 'danger'
        });
      }
    }else{
      alert('Что-то сломалось, попробуйте снова позже или с другого устройства')
    }
    if(action == 'set'){
      elem.textContent = 'Сообщим о поступлении'
    }else if(action == 'remove'){
      elem.textContent = 'Узнать о поступлении'
    }
    var onlickAttr = swapSetRemove(elem.getAttribute('onclick'))
    elem.setAttribute('onclick', onlickAttr)
    productNotificationSending = false;
  });
}
