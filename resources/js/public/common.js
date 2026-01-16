console.log('connect');

// header
document.addEventListener('DOMContentLoaded', function () {
    if (window.Fancybox) {
        if(document.querySelectorAll('[data-fancybox-no-close-btn]').length>0){
            Fancybox.bind('[data-fancybox-no-close-btn]',{
                closeButton: false,
                Toolbar: {
                display: {
                    left: [],
                    middle: [],
                    right: [],
                },
                },
            });
            Fancybox.bind('[data-fancybox]',{
                Thumbs: false,
                Carousel: {
                infinite: false,
                },
                Toolbar: {
                display: {
                    left: [],
                    middle: [],
                    right: [],
                },
                },
            });
        }
    }

  window.generatePictureElements();

  // filter catalog
  const filterToggle = document.getElementById('filter-toggle');
  const filterClose = document.getElementById('filter-close');
  const filter = document.getElementById('filter');
  if (filterToggle && filter) {
    filterToggle.addEventListener('click', () => {
      filter.style.transform = 'translateX(0)';
    });

    filterClose.addEventListener('click', () => {
      filter.style.transform = 'translateX(100%)';
    });
  }
  // filter catalog end
  const contentBlock = document.getElementById('activeComponents-block')
  if(contentBlock){
    contentBlock.addEventListener('click', function(event){
      var block = document.getElementById('activeComponents-text');

      if (!block.contains(event.target)) {
        contentBlock.classList.add('translate-x-full');
        contentBlock.classList.remove('translate-x-0');
        document.body.style.overflow = "";
        document.body.style.overflow = "";
      }
    })

  }
  const activeComponentsButton = document.getElementById('activeComponents')
  if(activeComponentsButton){
    activeComponentsButton.addEventListener('click', function(event){


      //contentBlock.classList.remove('hidden');
      contentBlock.classList.add('translate-x-0');
      contentBlock.classList.remove('translate-x-full');
      document.body.style.overflow = "hidden";
      document.body.style.overflow = "hidden";
      // const popupBox = document.getElementById('activeComponents-text')
      //
      // // Устанавливаем прозрачность для .popup-bg
      // contentBlock.style.transition = "background-opacity 0.15s, right 0.15px";
      //
      // contentBlock.classList.remove('bg-opacity-0');
      // contentBlock.classList.add('bg-opacity-60');
      //
      // // Задаем задержку, чтобы .popup-bg успел изменить свою прозрачность, перед тем как .popup начнет двигаться
      // setTimeout(() => {
      //   contentBlock.classList.remove('-right-full');
      //   contentBlock.classList.add('right-0');
      //   // popupBox.style.transition = "transform 0.15s";
      //   // popupBox.classList.remove('translate-x-1/2');
      //   // popupBox.classList.add('translate-x-0');
      // }, 150);  // Задержка равна времени анимации popupBg
    })

    const closeBtn = document.getElementById('activeComponents-close')
    closeBtn.addEventListener('click', () => {
      contentBlock.classList.add('translate-x-full');
      contentBlock.classList.remove('translate-x-0');
      document.body.style.overflow = "";
      document.body.style.overflow = "";
      // setTimeout(() => {
      //   //contentBlock.classList.add('hidden');
      // }, 300);  // Задержка равна времени анимации popupBg
      // const popupBox = document.getElementById('activeComponents-text')
      //
      // // Устанавливаем прозрачность для .popup-bg
      // contentBlock.style.transition = "background-opacity 0.15s, right 0.15px";
      // contentBlock.classList.add('-right-full');
      // contentBlock.classList.remove('right-0');
      //
      // // Задаем задержку, чтобы .popup-bg успел изменить свою прозрачность, перед тем как .popup начнет двигаться
      // setTimeout(() => {
      //   contentBlock.classList.add('bg-opacity-0');
      //   contentBlock.classList.remove('bg-opacity-60');
      //   // popupBox.style.transition = "transform 0.15s";
      //   // popupBox.classList.remove('translate-x-1/2');
      //   // popupBox.classList.add('translate-x-0');
      // }, 150);  // Задержка равна времени анимации popupBg

    })
  }

});

// header end

// product ring animate
let options = {
  root: null,
  rootMargin: '0px',
  threshold: [1.0, 0]
};

let callback = (entries, observer) => {

  entries.forEach(entry => {
    const circle = entry.target.querySelector('.progressCircle');
    if (entry.isIntersecting) {
      if (circle && !circle.classList.contains('hide')) {
        animateSVG(circle);
        circle.classList.add('hide');
        // circle.interval = setInterval(() => {
        //   animateSVG(circle);
        // }, 3000); // Повторять каждые 3 секунды
      }
    } else {
      if (circle) {
        circle.classList.remove('hide');
        if (circle.interval) {
          clearInterval(circle.interval); // Очистите интервал при выходе из области видимости
        }
      }
    }
  });
};

let observer = new IntersectionObserver(callback, options);

document.querySelectorAll('.observed').forEach(elem => {
  observer.observe(elem);
});

function animateSVG(circle) {
  const percentageToFill = circle.dataset.percentage/100; // 80%
  const circleLength = circle.dataset.circleLength;
  const offset = circleLength * (1 - percentageToFill);
  const animation = circle.querySelector(".progressAnimation");

  if (animation) {
    animation.setAttribute("to", offset);
    animation.beginElement(); // Запустите анимацию
  }
}

// product ring animate end
// product description
const addEllipsisBySymbols = (block, ellipsis = '...') => {
  let longText = false;
  const symbolCount = block.dataset.symbols;

  // Устанавливаем текст блока на его оригинальное значение
  const originalText = block.dataset.originalText;
  block.innerHTML = originalText;

  // Обрезаем текст по заданному количеству символов
  if (originalText.length > symbolCount) {
    block.innerHTML = originalText.substring(0, symbolCount) + ellipsis;
    longText = true;
    block.dataset.shortText = block.innerHTML;
  }
  return longText;
};

function countCharsInLines(lines_count, text) {
  // Разделяем текст на массив строк
  let lines = text.split('\n');

  // Обрезаем массив до первых пяти строк, если строк больше пяти
  if (lines.length > lines_count) {
    lines = lines.slice(0, lines_count);
  }

  // Считаем количество символов в каждой строке и суммируем
  let charCount = lines.reduce((acc, line) => acc + line.length, 0);

  return charCount + 1;
}
function getHeightForSymbols(block, symbolsCount) {
  const tempBlock = document.createElement("div");
  tempBlock.style.visibility = "hidden";
  tempBlock.style.width = getComputedStyle(block).width;
  if(symbolsCount > 0) {
    tempBlock.innerHTML = block.dataset.originalText.substring(0, symbolsCount) + "...";
  } else {
    tempBlock.innerHTML = '';
  }
  tempBlock.classList.add(...block.classList);
  block.parentNode.appendChild(tempBlock);
  const height = tempBlock.clientHeight;
  tempBlock.parentNode.removeChild(tempBlock);
  return height;
}
function updateCollapsibleHeight(blocks){
  blocks.forEach(block => {
    let height = getHeightForSymbols(block, block.dataset.symbols)
    block.style.maxHeight = height+'px';
  });
}

const blocks = document.querySelectorAll('.collapsibleBlock');

blocks.forEach(block => {
  let isExpanding = false;
  const buttonId = block.dataset.buttonId;
  const button = document.getElementById(buttonId);
  block.dataset.originalText = block.innerHTML;
  let symbols;
  if(block.dataset.symbols){
    symbols = block.dataset.symbols
  }else if(block.dataset.lines){
    symbols = countCharsInLines(Number(block.dataset.lines), block.innerHTML);
    block.dataset.symbols = symbols
  }
  let height = getHeightForSymbols(block, symbols)
  block.style.maxHeight = height+'px';
  if (!button) return;
  let ellipsisSymb = '...'
  if(block.dataset.ellipsis){
    ellipsisSymb = block.dataset.ellipsis
  }
  const ellipsis = addEllipsisBySymbols(block, ellipsisSymb);
  if(ellipsis === false) {
    button.style.display = 'none';
    return false;
  }

  block.addEventListener('transitionend', () => {
    if (isExpanding) {
      button.querySelector('.text').innerText = button.dataset.closeText;
      button.querySelector('svg').style.transform = 'rotate(180deg)';
    } else {
      button.querySelector('.text').innerText = button.dataset.openText;
      button.querySelector('svg').style.transform = 'rotate(0deg)';
    }
  });

  button.addEventListener('click', () => {
    if (!isExpanding) {
      block.innerHTML = block.dataset.originalText;
      block.style.maxHeight = `${block.scrollHeight}px`;
      isExpanding = true;
    } else {
      block.style.maxHeight = height+'px';
      isExpanding = false;
      setTimeout(()=>{
        addEllipsisBySymbols(block, ellipsisSymb);
      }, 500)
    }
  });

});

// let lastWindowWidth = window.innerWidth;
// window.addEventListener('resize', function() {
//   if (window.innerWidth !== lastWindowWidth) {
//     updateCollapsibleHeight(blocks)
//   }
// });


// product description end
let previousWidth = window.innerWidth;
// product slider
const productSlider = document.getElementById('productSlider')
if(productSlider){
  if(window.innerWidth >= 1024){
    productSlider.style.height = productSlider.offsetHeight+'px'
    productSlider.style.maxHeight = productSlider.offsetHeight+'px'
  }else{
    // productSlider.style.height = (productSlider.offsetWidth * 1.3647058824)+'px'
    // productSlider.style.maxHeight = (productSlider.offsetWidth * 1.3647058824)+'px'
  }
  // resize
  // window.addEventListener('resize', () => {
  //   const currentWidth = window.innerWidth;
  //   if (currentWidth !== previousWidth) {
  //     handleHorizontalResize();
  //     previousWidth = currentWidth;
  //   }
  // });

}
// product slider end
// auto height
const autoHeight = document.querySelectorAll('.swiper[data-autoheight]');
if(autoHeight.length){
  updateSwiperHeight(autoHeight)
  window.addEventListener('resize', () => {
    updateSwiperHeight(autoHeight)
  });
}
function updateSwiperHeight(items){
  items.forEach((item)=>{
    item.querySelectorAll('.swiper[data-autoheight]>.swiper-wrapper>.swiper-slide').forEach(slide => {
      slide.style.minHeight = '';
    });
    let maxHeight = 0;
    item.querySelectorAll('.swiper[data-autoheight]>.swiper-wrapper>.swiper-slide').forEach(slide => {
      if (slide.offsetHeight > maxHeight) {
        maxHeight = slide.offsetHeight;
      }
    });
    item.querySelectorAll('.swiper[data-autoheight]>.swiper-wrapper>.swiper-slide').forEach(slide => {
      slide.style.minHeight = `${maxHeight}px`;
    });
  })
}

// auto height end
// vertical tabs
document.addEventListener("DOMContentLoaded", function() {
  const toggleWrappers = document.querySelectorAll('.toggle-wrapper');

  toggleWrappers.forEach(wrapper => {
    const content = wrapper.querySelector('.toggle-content');
    const toggleButton = wrapper.querySelector('.toggle-button');
    const arrow = wrapper.querySelector('svg');

    let contentHeight = content.scrollHeight + "px";

    content.style.height = '0px';
    content.style.overflow = 'hidden';

    toggleButton.addEventListener('click', () => {
      if(content.style.height === '0px') {
        content.style.height = contentHeight;
        arrow.style.transform = 'rotate(180deg)';
      } else {
        content.style.height = '0px';
        arrow.style.transform = 'rotate(0deg)';
      }
    });
  });
});
// vertical tabs end
// только цифры длф полей .numeric-field
window.numericFieldsListener()
// end


function getMap (latitude,longitude,address) {
  var box_map = document.getElementById('PopupMap');
  // if (!box_map.classList.contains('loaded')) {
  //   let map = document.createElement('script');
  //
  //   map.src = 'https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=25e4d3cc-8437-4a0d-995b-65e4604bdc98';
  //   map.type = 'text/javascript'
  //   // map.async = true;
  //
  //   box_map.append(map);
  //   box_map.classList.add('loaded');
  //   map.onload = function () {
  //     ymaps.ready(init);
  //   }
  // }else{
  //   ymaps.ready(init);
  // }
  ymaps.ready(init(latitude,longitude,address));
}

function init (latitude,longitude,address) {
  var myMap, myGeoObject;

  myMap = new ymaps.Map('ymap', {
    center: [latitude, longitude],
    zoom: 16,
    controls: ['zoomControl', 'fullscreenControl']
  }, {
    searchControlProvider: 'yandex#search'
  });

  myGeoObject = new ymaps.GeoObject({
    // Описание геометрии.
    geometry: {
      type: "Point",
      coordinates: [latitude, longitude]
    },
    // Свойства
    properties: {
      // Контент метки.
      // hintContent: address
    }
  });

  myMap.geoObjects.add(myGeoObject);
}
const openMaps = document.querySelectorAll('[data-map]');
function mapsInit(){
  openMaps.forEach(function (item) {
    item.addEventListener('click', (event) => {
      event.preventDefault()
      const elem = event.target
      let popup = document.getElementById('PopupMap')
      if (!popup){
        popup = document.createElement('div')
        popup.id = 'PopupMap'
        document.body.appendChild(popup)

      }
      popup.innerHTML = '<div id="ymap"></div>'
      Fancybox.show(
        [
          {
            src: '#PopupMap',
            width: "900px",
            height: "700px",
          },
        ],
        {
          loop: false,
          touch: false,
          contentClick: false,
          dragToClose: false,
          on: {
            done: () => {
              setTimeout(() => {
                getMap(elem.dataset.latitude,elem.dataset.longitude);
              },100)
            }
          }
        }
      );
    })
  })
}
mapsInit()
// document.addEventListener('DOMContentLoaded', function() {
//   // Выберите все блоки с классом .findNumbers
//   let blocks = document.querySelectorAll('.findNumbers');
//
//   if(blocks.length>0){
//     blocks.forEach(block => {
//       // Разделить HTML на части, используя регулярное выражение для выделения текста между тегами
//       let parts = block.innerHTML.split(/(<[^>]+>)/g);
//
//       for (let i = 0; i < parts.length; i++) {
//         // Если часть не является HTML-тегом (не начинается с <), обработать её
//         if (!parts[i].startsWith('<')) {
//           parts[i] = parts[i].replace(/(\d+(:\d+)?)/g, '<span class="cormorantInfant">$1</span>');
//         }
//       }
//
//       block.innerHTML = parts.join('');
//     });
//     mapsInit()
//   }
//
// });


document.addEventListener("DOMContentLoaded", function() {
  let lazyPictures = [].slice.call(document.querySelectorAll("picture.lazy-load"));
  lazyPictures.forEach(window.observeLazyPicture);
});

// Добавьте эту строку каждый раз, когда вы динамически добавляете новый элемент picture.lazy-load
// observeLazyPicture(newPictureElement);
document.addEventListener('DOMContentLoaded', function() {
  var selector = document.getElementById('date-of-birth');
  var currentYear = new Date().getFullYear();
  var im = new Inputmask("datetime", {
    inputFormat: "dd.mm.yyyy",
    placeholder: "дд.мм.гггг",
    min: "01/01/" + (currentYear - 100),
    max: "31/12/" + currentYear,
    insertMode: false,
    showMaskOnHover: false,
    yearrange: { minyear: currentYear - 100, maxyear: currentYear }
  });
  im.mask(document.querySelectorAll("input.birthday"));
});



if(document.getElementById('puzzleForm')){
  document.getElementById('puzzleForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Предотвращаем стандартное поведение формы

    var form = event.target;
    var formData = new FormData(form);
    var data = window.formDataToObject(formData);

    var loadingMessage = document.getElementById('loadingMessage');
    var popupMessage = document.getElementById('popupMessage');

    // Показать сообщение "ждите"
    loadingMessage.style.display = '';
    window.setFormDisabled(form, true)
    window.ajax.post(form.action, data, function(response) {
      console.log('response', response)
      // Скрыть сообщение "ждите"
      loadingMessage.style.display = 'none';
      window.setFormDisabled(form, false)
      // Показать всплывающее окно с результатом
      popupMessage.querySelector('.textContent').innerHTML = `<div>${response.message ? response.message.replace(/\n/g, "<br/>") : 'Не получили ответ от сервера'}</div>`;
      Fancybox.show(
        [
          {
            src: '#popupMessage',
            width: "900px",
            height: "700px",
          },
        ],
        {
          loop: false,
          touch: false,
          contentClick: false,
          dragToClose: false
        }
      );
    })
    // Скрыть всплывающее окно через 3 секунды
    // setTimeout(function() {
    //   popupMessage.style.display = 'none';
    // }, 3000);
  });
}


function ready(f) {
  /in/.test(document.readyState) ? setTimeout(function() { ready(f); }, 9) : f();
}

ready(function () {
  window.onYouTubeIframeAPIReady = function() {}
  if(document.querySelectorAll('.youtube').length > 0){
    if (!document.getElementsByClassName) {
      // Поддержка IE8
      var getElementsByClassName = function (node, classname) {
        var a = [];
        var re = new RegExp('(^| )' + classname + '( |$)');
        var els = node.getElementsByTagName("*");
        for (var i = 0, j = els.length; i < j; i++)
          if (re.test(els[i].className)) a.push(els[i]);
        return a;
      }
      var videos = getElementsByClassName(document.body, "youtube");
    } else {
      var videos = document.getElementsByClassName("youtube");
    }

    var nb_videos = videos.length;
    for (var i = 0; i < nb_videos; i++) {
      var iframe = document.createElement("iframe");
      var iframe_url = "https://www.youtube-nocookie.com/embed/" + videos[i].id + "?rel=0&modestbranding=1&autohide=1&showtitle=0&enablejsapi=1&version=3&wmode=transparent";
      if (videos[i].getAttribute("data-params")) iframe_url += '&' + videos[i].getAttribute("data-params");
      iframe.setAttribute("src", iframe_url);
      iframe.setAttribute("frameborder", '0');
      iframe.setAttribute("allowfullscreen", 'allowfullscreen');

      // Высота и ширина iFrame будет как у элемента-родителя
      iframe.style.width = videos[i].style.width;
      iframe.style.height = videos[i].style.height;
      iframe.id = videos[i].id
      // Заменяем начальное изображение (постер) на iFrame
      // var play = videos[i].parentNode.querySelector('.play')
      videos[i].parentNode.replaceChild(iframe, videos[i]);
      // if(play){
      //   play.addEventListener('click', (e)=>{
      //     e.currentTarget.style.display = 'none'
      //     new YT.Player(iframe.id, {
      //       events: {
      //         'onAutoplayBlocked': function(event) {
      //           console.log('onAutoplayBlocked', event.target)
      //         },
      //         'onReady': function(event) {
      //           event.target.playVideo();
      //         }
      //       }
      //     });
      //   })
      // }
    }
  }

});
// gold ticket
//
// window.setCookie = (name, value, days) => {
//   const expires = new Date(Date.now() + days * 24 * 60 * 60 * 1000).toUTCString();
//   document.cookie = `${name}=${value}; expires=${expires}; path=/`;
// }
//
// function getCookie(name) {
//   const value = `; ${document.cookie}`;
//   const parts = value.split(`; ${name}=`);
//   if (parts.length === 2) return parts.pop().split(';').shift();
//   return null;
// }
//
// function goldTicketCheck() {
//   const goldTicket = getCookie('goldticket');
//   const goldTicketShown = getCookie('goldticketShown');
//
//   if (window.location.pathname == '/order' || window.location.pathname == '/catalog/seti_mini_versiy') {
//
//     console.log('window.location.pathname', window.location.pathname)
//     return;
//   }
//   if (goldTicketShown) {
//     return; // Если куки goldticketShown существует, функцию больше не запускаем
//   }
//
//   if (!goldTicket) {
//     // Если куки goldticket не существует, создаем его с текущей датой и временем
//     const now = new Date();
//     window.setCookie('goldticket', now.toUTCString(), 1);
//   } else {
//     // Если куки goldticket существует, проверяем дату и время создания
//     const createdTime = new Date(goldTicket);
//     const currentTime = new Date();
//     const diffMinutes = (currentTime - createdTime) / 1000;
//
//     if (diffMinutes >= 20) {
//       // Если прошло больше 20 минут, функция должна сработать без таймера
//       showGoldTicket();
//     } else {
//       // Если прошло меньше 20 минут, запускаем таймер
//       const remainingTime = (20 - diffMinutes) * 1000;
//       setTimeout(showGoldTicket, remainingTime);
//     }
//   }
// }
//
// function showGoldTicket() {
//   Fancybox.show(
//     [
//       {
//         src: '#goldticket-alert',
//         width: "900px",
//         height: "700px",
//       },
//     ],
//     {
//       closeButton: false,
//       Toolbar: {
//         display: {
//           left: [],
//           middle: [],
//           right: [],
//         },
//       },
//       loop: false,
//       touch: false,
//       contentClick: false,
//       dragToClose: false,
//     }
//   );
//   // Создаем куки goldticketShown после срабатывания функции
//   window.setCookie('goldticketShown', 'true', 1);
// }
//
// // Проверяем наличие куки goldticket при загрузке страницы
// document.addEventListener('DOMContentLoaded', goldTicketCheck);
