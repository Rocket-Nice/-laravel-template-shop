// ajax
window.ajax = {};
window.ajax.x = function () {
  if (typeof XMLHttpRequest !== 'undefined') {
    return new XMLHttpRequest();
  }
  var versions = [
    "MSXML2.XmlHttp.6.0",
    "MSXML2.XmlHttp.5.0",
    "MSXML2.XmlHttp.4.0",
    "MSXML2.XmlHttp.3.0",
    "MSXML2.XmlHttp.2.0",
    "Microsoft.XmlHttp"
  ];

  var xhr;
  for (var i = 0; i < versions.length; i++) {
    try {
      xhr = new ActiveXObject(versions[i]);
      break;
    } catch (e) {
    }
  }
  return xhr;
};
window.generateRandomString = () => {
  const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  let result = '';
  const charactersLength = characters.length;
  for (let i = 0; i < 16; i++) {
    result += characters.charAt(Math.floor(Math.random() * charactersLength));
  }
  return result;
}
window.ajax.send = function (url, callback, method, data, async) {
  if (async === undefined) {
    async = true;
  }
  var x = window.ajax.x();
  x.responseType = 'json';
  x.open(method, url, async);
  x.onreadystatechange = function () {
    if (x.readyState == 4) {
      callback(x.response)
    }
  };
  if (method == 'POST') {
    x.setRequestHeader('Content-type', 'application/json');
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    x.setRequestHeader('X-CSRF-TOKEN', csrf);
  }
  x.send(JSON.stringify(data))
};

window.ajax.get = function (url, data, callback, async) {
  var query = [];
  for (var key in data) {
    query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
  }
  // Проверяем, содержит ли URL уже параметры
  var separator = url.includes('?') ? '&' : '?';
  window.ajax.send(url + (query.length ? separator + query.join('&') : ''), callback, 'GET', null, async);
};

window.ajax.post = function (url, data, callback, async) {
  ajax.send(url, callback, 'POST', data, async)
};
if(document.querySelectorAll('[data-fancybox]').length>0){
  Fancybox.bind('[data-fancybox]',{
    Toolbar: {
      display: {
        left: ["infobar"],
        middle: [],
        right: ["close"],
      },
    },
  });
}
// end
window.isset = function(obj){
  return typeof obj !== 'undefined' && obj !== null;
}
window.htmlDecode = function(input){
  var doc = new DOMParser().parseFromString(input, "text/html");
  return doc.documentElement.textContent;
}
window.formatPrice = function(number) {
  return '<span class="cormorantInfant">'+number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")+'</span> руб.';
}
window.removeSuffix = function removeSuffix(str, suffix) {
  if (str.endsWith(suffix)) {
    return str.slice(0, -suffix.length);
  }
  return str;
}
window.fadeIn = function(element, duration = 500) {
  element.style.opacity = 0;
  element.style.display = null;
  element.style.transition = `opacity ${duration}ms`;

  // Эта часть нужна, чтобы браузер успел применить стили выше
  // перед началом анимации
  setTimeout(() => {
    element.style.opacity = 1;
  }, 50);
}
window.fadeOut = function(element) {
  let opacity = 1;
  const timer = setInterval(function () {
    if (opacity <= 0.1) {
      clearInterval(timer);
      element.style.display = 'none';
    }
    element.style.opacity = opacity;
    opacity -= opacity * 0.1;
  }, 50);
}
window.toUpperCase = function(editor) {
  var text = editor.selection.getContent({ format: 'text' });
  editor.selection.setContent(text.toUpperCase());
}
window.toLowerCase = function(editor) {
  var text = editor.selection.getContent({ format: 'text' });
  editor.selection.setContent(text.toLowerCase());
}
window.numericFieldsListener = function(){
  const numberInputs = document.querySelectorAll('.numeric-field');

  numberInputs.forEach(input => {
    input.addEventListener('input', window.restrictNonNumericInput);
  });
}
window.observeLazyPicture = function(element) {
  if (!element || element.classList.contains('is-observing')) return;  // Убедимся, что элемент предоставлен


  if ("IntersectionObserver" in window) {
    let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          let pictureElement = entry.target;
          let imgElement = pictureElement.querySelector('img');
          let sourceElements = pictureElement.querySelectorAll('source');

          if(imgElement.src != imgElement.dataset.src){
            imgElement.src = imgElement.dataset.src;
          }

          sourceElements.forEach(function(sourceElement) {
            if(sourceElement.srcset != sourceElement.dataset.srcset) {
              sourceElement.srcset = sourceElement.dataset.srcset;
            }
          });

          lazyImageObserver.unobserve(pictureElement);
        }
      });
    }, {
      rootMargin: '500px 0px'
    });

    lazyImageObserver.observe(element);
    element.classList.add('is-observing')
  } else {
    // Для браузеров без поддержки IntersectionObserver
    let imgElement = element.querySelector('img');
    let sourceElements = element.querySelectorAll('source');

    if(imgElement.src != imgElement.dataset.src) {
      imgElement.src = imgElement.dataset.src;
    }

    sourceElements.forEach(function(sourceElement) {
      if(sourceElement.srcset != sourceElement.dataset.srcset) {
        sourceElement.srcset = sourceElement.dataset.srcset;
      }
    });
  }
};
window.updateTextSize = function() {
  // Запрос на следующий анимационный кадр для улучшения производительности
  requestAnimationFrame(() => {
    // Получение всех блоков
    const cards_1 = document.querySelectorAll('.product_card-style-1, .product_card-style-2, .product_card-style-4')

    cards_1.forEach(block => {
      const screenWidth = window.innerWidth;
      const blockWidth = block.clientWidth;
      let newFontSize;
      if(screenWidth >= 546){
        newFontSize = blockWidth * 0.04516129032;
      }else{
        // newFontSize = blockWidth * 0.05586592179;
        newFontSize = blockWidth * 0.04516129032;
      }

      block.style.fontSize = `${newFontSize}px`;
    });
    // Получение всех блоков
    const cards_3 = document.querySelectorAll('.product_card-style-3')

    cards_3.forEach(block => {
      const screenWidth = window.innerWidth;
      const blockWidth = block.clientWidth;
      let newFontSize;
      if(screenWidth >= 546){
        newFontSize = blockWidth * 0.04516129032;
      }else{
        newFontSize = blockWidth * 0.05574912892;
      }

      block.style.fontSize = `${newFontSize}px`;
    });
    const cards_5 = document.querySelectorAll('.product_card-style-5')

    cards_5.forEach(block => {
      const screenWidth = window.innerWidth;
      const blockWidth = block.clientWidth;
      let newFontSize;
      if(screenWidth >= 546){
        newFontSize = blockWidth * 0.04516129032;
      }else{
        newFontSize = blockWidth * 0.05574912892;
      }

      block.style.fontSize = `${newFontSize}px`;
    });
  });
}
window.restrictNonNumericInput = function(event) {
  let inputValue = event.target.value;
  let maxValue = event.target.dataset.maxValue ?? null;
  let numericValue = inputValue.replace(/[^0-9.]/g, '');

  // Удаление всех точек кроме первой
  const firstDotIndex = numericValue.indexOf('.');
  if (firstDotIndex !== -1) {
    numericValue = numericValue.substring(0, firstDotIndex + 1) +
      numericValue.substring(firstDotIndex + 1).replace(/\./g, '');
  }

  // Проверка максимального значения, если задано
  if (maxValue !== null && parseFloat(numericValue) > maxValue) {
    numericValue = maxValue.toString();
  }

  event.target.value = numericValue;
}
function supportsWebP() {
  return new Promise((resolve) => {
    const img = new Image();
    img.onload = () => {
      const result = (img.width > 0) && (img.height > 0);
      resolve(result);
    };
    img.onerror = () => {
      resolve(false);
    };
    img.src = 'data:image/webp;base64,UklGRhoAAABXRUJQVlA4TA0AAAAvAAAAEAcQERGIiP4HAA==';
  });
}
let webp = true
supportsWebP().then(supported => {
  if (!supported) {
    webp = false
  }
})
const elementsWithLoadListener = new WeakSet();
window.generatePictureElements = function() {
  // Карта соответствия между ключами JSON и атрибутами media
  const mediaMap = {
    "480": "(max-width: 479px)",
    "768": "(min-width: 480px) and (max-width: 767px)",
    "1200": "(min-width: 767px) and (max-width: 1199px)",
  };

  // Находим все элементы input с классом json-image
  const inputs = document.querySelectorAll('input.json-image');

  inputs.forEach(input => {
    const imageJson = window.htmlDecode(input.value)
    const imageData = JSON.parse(imageJson);
    const pictureElem = document.createElement('picture');
    pictureElem.id = input.dataset.id
    pictureElem.className = input.dataset.pictureClass
    if(input.dataset.imgClass){
      pictureElem.dataset.imgClass = input.dataset.imgClass
    }
    const swiperImg = input.classList.contains('swiper-slide-img')
    // const pictureElem = document.getElementById(input.getAttribute('data-id'));

    if (pictureElem) {
      // Очистка содержимого picture (если есть старые элементы source или img)
      pictureElem.innerHTML = '';

      for (const size in mediaMap) {
        if (imageData[size]) {
          const source = document.createElement('source');
          let srcsetVal = imageData[size];

          if(!webp && imageData[`${size}_webp`]) {
            // Если существует версия @2x для этого размера, добавляем в srcset
            if (imageData[`${size}@2x`]) {
              srcsetVal += `, ${imageData[`${size}@2x`]} 2x`;
            }

            // source.setAttribute('media', mediaMap[size]);
            if (imageData["minimal_quality"] && false) {
              source.setAttribute('data-srcset', srcsetVal);
            }else{
              source.setAttribute('srcset', srcsetVal);
            }
            // source.setAttribute('srcset', srcsetVal);
            pictureElem.appendChild(source);
          }else{
            const sourceWebp = document.createElement('source');
            let srcsetValWebp = imageData[`${size}_webp`];

            if (imageData[`${size}@2x_webp`]) {
              srcsetValWebp += `, ${imageData[`${size}@2x_webp`]} 2x`;
            }
            sourceWebp.setAttribute('media', mediaMap[size]);
            if (imageData["minimal_quality_webp"] && false) {
              sourceWebp.setAttribute('data-srcset', srcsetValWebp);
            } else {
              sourceWebp.setAttribute('srcset', srcsetValWebp);
            }
            pictureElem.appendChild(sourceWebp);
            // pictureElem.insertBefore(sourceWebp, source);
          }
        }
      }

      // Добавляем тег img для обратной совместимости и в качестве fallback
      if(imageData["1920"]){
        imageData["full"] = imageData["1920"]
      }
      if(imageData["1920_webp"]){
        imageData["full_webp"] = imageData["1920_webp"]
      }
      if (imageData["full"]) {
        const img = document.createElement('img');
        if(pictureElem.dataset.imgClass){
          img.className = pictureElem.dataset.imgClass;
        }
        if (pictureElem.classList.contains('block')) {
          img.classList.add('block');
        }
        if (pictureElem.classList.contains('object-center')) {
          img.classList.add('object-center');
        }
        if (pictureElem.classList.contains('object-cover')) {
          let style = window.getComputedStyle(pictureElem);
          let position = style.getPropertyValue('position');
          if (position == 'static'){
            pictureElem.style.position = 'relative'
          }
          img.classList.add('object-cover');
        }

        if(!webp && imageData["full_webp"]) {
          if (imageData["minimal_quality"] && false) {
            img.setAttribute('src', imageData["minimal_quality"]);
            img.setAttribute('data-src', imageData["full"]);
          } else {
            img.setAttribute('src', imageData["full"]);
          }
          img.setAttribute('alt', '');
        }else{
          img.setAttribute('src', imageData["full_webp"]);

        }

        pictureElem.appendChild(img);
      }
      // pictureElem.classList.add('lazy-load')
      // if (!elementsWithLoadListener.has(pictureElem)) {
      //   window.observeLazyPicture(pictureElem)
      // }
    }
    const parent = input.parentElement;
    if (input.nextSibling) {
      parent.insertBefore(pictureElem, input.nextSibling);
    } else {
      parent.appendChild(pictureElem);
    }
    input.remove()
  });
}
window.observeSlidesInViewport = function(swiperInstance) {
  let { activeIndex } = swiperInstance;

  // Проверьте изображение текущего слайда
  window.observeLazyPicture(swiperInstance.slides[activeIndex]);

  // Если есть предыдущий слайд, проверьте его изображение
  if (swiperInstance.slides[activeIndex - 1]) {
    window.observeLazyPicture(swiperInstance.slides[activeIndex - 1]);
  }

  // Если есть следующий слайд, проверьте его изображение
  if (swiperInstance.slides[activeIndex + 1]) {
    window.observeLazyPicture(swiperInstance.slides[activeIndex + 1]);
  }
}
let resizeTimer;
window.debouncedResize = function(callback, delay) {
  clearTimeout(resizeTimer);
  resizeTimer = setTimeout(callback, delay);
}
window.myDataObject = function(){
  let _data = {}

  return {
    getObject() {
      return _data
    },
    getVar(key) {
      return isset(_data[key]) ? _data[key] : console.error(key, ' was not found in data')
    },
    setVar(key, val){
      _data[key] = val
    }
  }
}
window.debug = function(obj){
  const objStr = JSON.stringify(obj, null, 2); // "2" для форматирования с отступом в 2 пробела
  document.body.innerHTML = `<pre>${objStr}</pre>`;
}
window.checkChoisesDropdown = function(event){
  let dropdown = event.currentTarget.closest('.choices').querySelector('.choices__list.choices__list--dropdown');

  dropdown.style.height = '';
  let rect = dropdown.getBoundingClientRect();

  if (rect.bottom > window.innerHeight) {
    let overflow = rect.bottom - window.innerHeight;
    dropdown.style.height = `${rect.height - overflow}px`;
  }
  if (rect.top < 0) {
    dropdown.style.height = `${rect.height + rect.top}px`;
  }
}
function toastsListener() {
  const toasts = document.querySelectorAll('.toast-item');
  toasts.forEach((toast)=>{
    toast.querySelector('button[data-event="close"]').addEventListener('click', (event) => {
      event.preventDefault();
      const toast = event.target.closest('.toast-item');
      toast.parentNode.removeChild(toast)
    })
  })
}
toastsListener()

if(document.getElementById('stars')){
  const stars = document.querySelectorAll('#stars .star');
  const ratingValueInput = document.getElementById('ratingValue');

  stars.forEach((star, index) => {
    star.addEventListener('mouseover', () => {
      highlightStars(index);
    });

    star.addEventListener('click', () => {
      ratingValueInput.value = index + 1;
      highlightStars(index, true);
    });
  });

  function highlightStars(index, fix = false) {
    stars.forEach((star, starIndex) => {
      if (starIndex <= index) {
        star.querySelector('path').setAttribute('opacity', '1');
        star.querySelector('path').setAttribute('fill', '#2C2E35');
      } else {
        star.querySelector('path').setAttribute('opacity', '0.32');
        star.querySelector('path').setAttribute('fill', '#2C2E35');
      }
    });

    if (!fix) {
      document.addEventListener('mouseout', () => {
        if (ratingValueInput.value) {
          highlightStars(ratingValueInput.value - 1);
        } else {
          stars.forEach(star => {
            star.querySelector('path').setAttribute('opacity', '0.32');
          });
        }
      }, { once: true });
    }
  }
}

window.denum = (num, titles) => {
  const cases = [2, 0, 1, 1, 1, 2];
  const caseIndex = (num % 100 > 4 && num % 100 < 20) ? 2 : cases[Math.min(num % 10, 5)];
  return titles[caseIndex].replace('%d', num);
}

window.setFormDisabled = (form, disabled) => {
  const elements = form.elements;
  for (let i = 0; i < elements.length; i++) {
    elements[i].disabled = disabled;
  }
}
window.formDataToObject = (formData) => {
  const data = {};

  formData.forEach((value, key) => {
    if (key.endsWith('[]')) {
      // Если ключ заканчивается на '[]', это массив
      const actualKey = key.slice(0, -2);
      if (!Array.isArray(data[actualKey])) {
        data[actualKey] = [];
      }
      data[actualKey].push(value);
    } else {
      data[key] = value;
    }
  });

  return data;
}
document.addEventListener('DOMContentLoaded', function() {
  const draggable = document.querySelector('.draggable');
  if(draggable){
    let isDown = false;
    let startX;
    let scrollLeft;
    let hasMoved = false;

    draggable.addEventListener('mousedown', (e) => {
      isDown = true;
      startX = e.pageX - draggable.offsetLeft;
      scrollLeft = draggable.scrollLeft;
    });

    draggable.addEventListener('mouseleave', () => {
      isDown = false;
    });

    draggable.addEventListener('mouseup', () => {
      isDown = false;
      setTimeout(() => { hasMoved = false; }, 0);
    });

    draggable.addEventListener('mousemove', (e) => {
      if (!isDown) return;
      e.preventDefault();
      const x = e.pageX - draggable.offsetLeft;
      const walk = (x - startX) * 3;
      if (Math.abs(x - startX) > 3) {
        hasMoved = true;
      }
      draggable.scrollLeft = scrollLeft - walk;
    });

    draggable.addEventListener('click', (e) => {
      if (hasMoved) {
        e.preventDefault();
        e.stopPropagation();
      }
      hasMoved = false;
    }, true);

    draggable.addEventListener('touchstart', (e) => {
      isDown = true;
      startX = e.touches[0].pageX - draggable.offsetLeft;
      scrollLeft = draggable.scrollLeft;
    }, {passive: true});

    draggable.addEventListener('touchend', () => {
      isDown = false;
      setTimeout(() => { hasMoved = false; }, 0);
    });

    draggable.addEventListener('touchmove', (e) => {
      if (!isDown) return;
      e.preventDefault();
      const x = e.touches[0].pageX - draggable.offsetLeft;
      const walk = (x - startX) * 1;
      if (Math.abs(x - startX) > 2) {
        hasMoved = true;
      }
      draggable.scrollLeft = scrollLeft - walk;
    }, {passive: false});
  }
});
function transferImageClasses(picture) {
  const img = picture.querySelector('img');
  const classes = picture.dataset.imgClass;
  if (img && classes) {
    img.className = classes;
    picture.removeAttribute('data-img-class');
  }
}

// Функция для обработки всех подходящих элементов picture
function processPictureElements() {
  const pictures = document.querySelectorAll('picture[data-img-class]');
  pictures.forEach(transferImageClasses);
}

// Запускаем обработку при загрузке DOM
document.addEventListener('DOMContentLoaded', processPictureElements);

// Опционально: функция для обработки динамически добавленного контента
window.processNewContent = (container = document) => {
  const pictures = container.querySelectorAll('picture[data-img-class]');
  pictures.forEach(transferImageClasses);
  window.listenCart();
}

window.alert = (message) => {
  const alert = document.getElementById('custom-alert__title');
  if(!alert) return false;
  alert.innerHTML = message;
  Fancybox.show(
    [
      {
        src: '#custom-alert'
      },
    ],
    {
      closeButton: false,
      loop: false,
      touch: false,
      contentClick: false,
      dragToClose: false,
    }
  );
}
