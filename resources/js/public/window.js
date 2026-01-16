window.observeSlidesInViewport = function(swiperInstance) {
  let { activeIndex } = swiperInstance;

  // Проверьте изображение текущего слайда
  observeLazyPicture(swiperInstance.slides[activeIndex]);

  // Если есть предыдущий слайд, проверьте его изображение
  if (swiperInstance.slides[activeIndex - 1]) {
    observeLazyPicture(swiperInstance.slides[activeIndex - 1]);
  }

  // Если есть следующий слайд, проверьте его изображение
  if (swiperInstance.slides[activeIndex + 1]) {
    observeLazyPicture(swiperInstance.slides[activeIndex + 1]);
  }
}

window.observeLazyPicture = function(element) {
  if (!element) return;  // Убедимся, что элемент предоставлен

  if ("IntersectionObserver" in window) {
    let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          let pictureElement = entry.target;
          let imgElement = pictureElement.querySelector('img');
          let sourceElements = pictureElement.querySelectorAll('source');

          imgElement.src = imgElement.dataset.src;

          sourceElements.forEach(function(sourceElement) {
            sourceElement.srcset = sourceElement.dataset.srcset;
          });

          lazyImageObserver.unobserve(pictureElement);
        }
      });
    }, {
      rootMargin: '500px 0px'
    });

    lazyImageObserver.observe(element);
  } else {
    // Для браузеров без поддержки IntersectionObserver
    let imgElement = element.querySelector('img');
    let sourceElements = element.querySelectorAll('source');

    imgElement.src = imgElement.dataset.src;

    sourceElements.forEach(function(sourceElement) {
      sourceElement.srcset = sourceElement.dataset.srcset;
    });
  }
};

document.addEventListener("DOMContentLoaded", function() {
  let lazyPictures = [].slice.call(document.querySelectorAll("picture.lazy-load"));
  lazyPictures.forEach(window.observeLazyPicture);
});

// Добавьте эту строку каждый раз, когда вы динамически добавляете новый элемент picture.lazy-load
// observeLazyPicture(newPictureElement);

