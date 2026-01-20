
export function ymGoal(targetName){
  if (typeof ym !== 'undefined') {
    ym(98576494, 'reachGoal', targetName);
  } else {
    console.error('Yandex Metrika не инициализирована');
  }
}
export function formatPrice(number) {
  if(!(!isNaN(parseFloat(number)) && isFinite(number) && /^[-+]?\d*\.?\d+$/.test(number))) return '';
  return '<span>'+number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")+'</span> руб.';
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
export function generatePictureElements() {
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
        // img.setAttribute('loading', 'lazy');
        pictureElem.appendChild(img);
      }
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
export function checkChoicesDropdown(event) {
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
export function draggable(){
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
      const walk = (x - startX) * 3;
      if (Math.abs(x - startX) > 3) {
        hasMoved = true;
      }
      draggable.scrollLeft = scrollLeft - walk;
    }, {passive: false});
  }
}

export function formDataToObject(formData) {
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


export async function fetchData(url, method = 'GET', data = null, handler = null){
  try {
    let response_params = {
      method,
      headers: {
        'Content-Type': 'application/json',
      },
    };
    if (data) {
      if(method === 'GET'){
        var query = [];
        for (var key in data) {
          query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
        }
        // Проверяем, содержит ли URL уже параметры
        var separator = url.includes('?') ? '&' : '?';
        url += (query.length ? separator + query.join('&') : '')
      }else{
        response_params.body = JSON.stringify(data);
      }
    }

    const response = await fetch(url, response_params);

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const result = await response.json();
    if(handler){
      handler(result)
    }
    return result;
  } catch (error) {
    throw error;
  }
}

export function setCookie(name, value, days, minutes = 0){
  let expires;
  if (days === false && minutes > 0){
    expires = new Date(Date.now() + minutes * 60 * 1000).toUTCString();
  }else if(days > 0){
    expires = new Date(Date.now() + days * 24 * 60 * 60 * 1000).toUTCString();
  }
  document.cookie = `${name}=${value}; expires=${expires}; path=/`;
}

export function getCookie(name) {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(';').shift();
  return null;
}
