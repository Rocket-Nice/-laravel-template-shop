export class HappyCoupon {
  constructor() {
    this.prizes = [];
    this.limit = 0;
    this.isProcessing = false;
    this.attemptsLeft = 0;
    this.isPrizesUpdated = false;
    this.isImagesUploaded = false;
    this.init();
  }

  init() {
    this.getOpenedPrizes();
    this.addEventListeners();
    this.prepareAnimation();
    // this.hideLoaderTime();
  }

  addEventListeners() {
    const allCouponeItems = document.querySelectorAll('.coupones-grid .coupone-item img');
    allCouponeItems.forEach(item => item.addEventListener('click', this.openCoupone.bind(this)));
  }

  async getOpenedPrizes() {

    try {
      const response = await this.fetchData(window.hpRoutes.opened, {});
      this.prizes = response.prizes;
      this.limit = response.limit;
      this.updateAttempts(response.limit - this.prizes.length);

      this.prizes.forEach(this.displayPrize.bind(this));
    } catch (error) {
      console.error('Error fetching opened prizes:', error);
    } finally {
      this.isPrizesUpdated = true;
      if(this.isImagesUploaded){
        this.hideLoader();
      }
    }
  }

  async openCoupone(event) {
    if (this.isProcessing || document.querySelector('.coupone.preload')) return;

    this.isProcessing = true;
    const elem = event.target;
    await this.checkPrize(this.prizes.length + 1, elem);
  }

  async checkPrize(count, elem) {
    if (this.prizes.length >= this.limit) {
      this.isProcessing = false;
      return;
    }
    try {
      const response = await this.fetchData(window.hpRoutes.open, { count });
      if (response.error) {
        this.isProcessing = false;
        return;
      }

      this.showPrize(elem);
      this.loadResult(response.prize.image, response.prize.coupone, response.prize.name);
      this.prizes.push(response.prize);
      this.updateAttempts(response.attempts_left);

      const btn = document.getElementById('show-coupone-btn');
      btn.setAttribute('onclick', `happyCoupon.nextCoupone(${count})`);
    } catch (error) {
      console.error('Error checking prize:', error);
      this.isProcessing = false;
    }
  }

  prepareAnimation(){
    let animation = false;
    let loadedImages = 0;
    const totalImages = window.animation.length;
    const loaderBar = document.getElementById('loading-bar');
    const preloadItems = document.getElementById('preload-items');
    window.animation.forEach((path, index) => {
      const img = new Image();
      img.src = path;
      img.classList.add('preload-item__img');
      preloadItems.appendChild(img);
      img.onload = () => {
        loadedImages++;
        if (loadedImages === totalImages) {
          animation = true;
          this.isImagesUploaded = true;
          if(this.isPrizesUpdated){
            this.hideLoader();
          }
        }
        loaderBar.style.width = `${loadedImages/totalImages*100}%`;
      };
      img.onerror = () => {
        console.error('Ошибка загрузки изображения:', path);
      };
    });
  }
  hideLoaderTime(){
    setTimeout(()=>{
      if(document.getElementById('loader').style.display !== 'none'){
        this.hideLoader();
      }
    },15000)
  }
  hideLoader(){
    document.getElementById('loader').style.display = 'none'
  }
  showPrize(elem) {
    const clone = elem.cloneNode(true);
    const pos = this.getElementPosition(elem);
    const size = this.getElementSize(elem);
    const gridSize = this.getElementSize(document.querySelector('.coupones-grid'));

    document.querySelectorAll('.coupones-grid .coupones-row').forEach(row => row.style.opacity = 0);

    const couponeBox = document.createElement('div');
    couponeBox.className = 'coupone preload';
    Object.assign(couponeBox.style, {
      position: 'absolute',
      left: `${pos.x}px`,
      top: `${pos.y}px`,
      width: `${size.width}px`,
      height: `${size.height}px`
    });

    const preloadItem = document.createElement('div');
    preloadItem.className = 'preload-item';
    preloadItem.appendChild(clone);
    couponeBox.appendChild(preloadItem);
    document.querySelector('.coupones-grid').appendChild(couponeBox);

    // Новая функция для анимации с использованием requestAnimationFrame
    const animate = (startTime) => {
      const currentTime = Date.now();
      const progress = Math.min((currentTime - startTime) / 200, 1); // 200ms для анимации

      const newLeft = pos.x + (((gridSize.width/2) - size.width - pos.x) * progress);
      const newTop = pos.y + (((gridSize.height/2) - size.height - pos.y) * progress);
      const newWidth = size.width + ((size.width * 2 - size.width) * progress);
      const newHeight = size.height + ((size.height * 2 - size.height) * progress);

      Object.assign(couponeBox.style, {
        left: `${newLeft}px`,
        top: `${newTop}px`,
        width: `${newWidth}px`,
        height: `${newHeight}px`
      });

      if (progress < 1) {
        requestAnimationFrame(() => animate(startTime));
      } else {
        // Анимация завершена
        setTimeout(() => {
          document.getElementById('show-coupone-btn').style.display = 'none';
        }, 200);
      }
    };

    // Запуск анимации
    requestAnimationFrame(() => animate(Date.now()));
  }

  nextCoupone(count) {
    document.querySelectorAll('.coupones-grid .coupones-row').forEach(row => row.style.opacity = null);

    const resultBox = document.querySelector(`#result-coupones .img[data-item="${count}"]`);
    const activeCoupone = document.querySelector('.coupone.preload');
    const images = activeCoupone.querySelectorAll('.preload-item img');
    const image = images[images.length-1];
    const imageClone = image.cloneNode(true);
    imageClone.style.opacity = 0;

    const coupone = document.createElement('div');
    Object.assign(coupone.style, {
      textAlign: 'center',
      color: '#A68773'
    });
    coupone.innerText = imageClone.getAttribute('data-coupone');

    const btn = document.getElementById('show-coupone-btn');
    const btnSize = this.getElementSize(btn);

    if (image.dataset.remove === '1') {
      resultBox.closest('.w-1/2').remove();
    } else {
      resultBox.append(coupone, imageClone);
      const size = this.getElementSize(resultBox);
      const pos = this.getElementPosition(resultBox);

      if (this.attemptsLeft !== 0) {
        btn.style.display = 'none';
        pos.y -= btnSize.height;
      }

      Object.assign(activeCoupone.style, {
        position: 'absolute',
        left: `${pos.x}px`,
        top: `${pos.y}px`,
        width: `${size.width}px`,
        height: `${size.height}px`,
        transition: 'all 0.2s ease-out'
      });
    }

    setTimeout(() => {
      imageClone.style.opacity = 1;
      activeCoupone.remove();
      if (this.attemptsLeft === 0) {
        btn.removeAttribute('onclick');
        btn.setAttribute('href', window.hpRoutes.cabinet);
        btn.innerHTML = '<span class="text">Личный кабинет</span>';
        if(document.getElementById('instaPromo-success') && this.attemptsLeft === 0){
          Fancybox.show(
            [
              {
                src: '#instaPromo-success'
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
      }
    }, 200);
  }

  async loadResult(prizeImage, couponeId, prizeName) {
    const link = document.getElementById('show-coupone-btn');
    link.querySelector('.text').textContent = 'Загрузка';
    const boxPreload = document.querySelector('.coupones-grid .coupone');

    const allImages = [...document.querySelectorAll('.preload-item__img')].map(img => img.cloneNode(true));

    try {
      const image = await this.loadImageAsync(prizeImage);
      if (prizeImage === '0') {
        image.dataset.remove = '1';
      }
      allImages.push(image);

      allImages.forEach((item, i) => {
        item.setAttribute('data-coupone', couponeId);
        const preloadItem = document.createElement('div');
        const coupone = document.createElement('div');
        Object.assign(coupone.style, {
          display: 'none',
          textAlign: 'center',
          background: '#F6F6F6',
          marginTop: '-10px',
          padding: '.5em'
        });
        // coupone.innerText = prizeName;
        preloadItem.className = 'preload-item';
        preloadItem.style.display = 'none';
        preloadItem.append(item, coupone);
        boxPreload.append(preloadItem);
      });

      const elems = boxPreload.querySelectorAll('.preload-item');
      let start = 33;

      elems.forEach((elem, i) => {
        start += 33;
        if (i === elems.length - 1) {
          Object.assign(elem.style, {
            position: 'absolute',
            left: '0',
            top: '0'
          });
          this.customFadeIn(elem, start);
          this.customFadeIn(elem.querySelector('div'), start);
        } else {
          if (i > 0) {
            this.changeDisplay(elems[i-1], start, 'none');
          }
          this.changeDisplay(elem, start);
        }
      });

      setTimeout(() => {
        this.isProcessing = false;
        link.style.display = 'inline-flex';
        link.querySelector('.text').textContent = 'Далее';
        // if (this.attemptsLeft === 0) {
        //   link.removeAttribute('onclick');
        //   link.setAttribute('href', window.hpRoutes.cabinet);
        //   link.innerHTML = '<span class="text">Личный кабинет</span>';
        // }else{
        //   link.querySelector('.text').textContent = 'Далее';
        // }
      }, start);

    } catch (error) {
      console.error('Error loading result:', error);
    }
  }

  updateAttempts(attempts) {
    this.attemptsLeft = attempts;
    document.getElementById('attempts').textContent = attempts;
  }

  YНgetElementPosition(element) {
    return {
      x: element.offsetLeft,
      y: element.offsetTop
    };
  }

  getElementSize(element) {
    return {
      width: element.offsetWidth,
      height: element.offsetHeight
    };
  }

  async fetchData(url, data) {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify(data)
    });
    return response.json();
  }

  loadImageAsync(url) {
    return new Promise((resolve, reject) => {
      const img = new Image();
      img.onload = () => resolve(img);
      img.onerror = reject;
      img.src = url;
    });
  }

  changeDisplay(item, time, display = 'block') {
    if (item) {
      setTimeout(() => {
        item.style.display = display;
      }, time);
    }
  }

  customFadeIn(item, time) {
    if (item) {
      setTimeout(() => {
        window.fadeIn(item, 2000);
      }, time);
    }
  }

  displayPrize(prize) {
    const item = document.querySelector(`#result-coupones .img[data-item="${prize.position.count}"]`);
    const img = document.createElement('img');
    const coupone = document.createElement('div');
    coupone.innerText = prize.coupone;
    Object.assign(coupone.style, {
      color: '#A68773',
      textAlign: 'center'
    });
    img.src = prize.image;
    if (prize.code === '0') {
      item.closest('.coupon-item').remove();
    } else {
      item.append(coupone, img);
    }
  }
}
