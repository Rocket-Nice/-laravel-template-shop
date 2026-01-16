import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('surveyHandler', () => ({
  currentQuestion: null,
  button: 'Далее',
  nextButton: false,
  questions: [],
  showError: false,
  showSuccess: false,
  errorMessage: '',
  successMessage: '',
  isSubmitting: false,
  init() {
    const questions = this.$el.querySelectorAll('.questions-item')
    questions.forEach((questionItem, index) => {
      const question = {
        is_answered: false,
        elem: questionItem,
        index
      }
      this.questions.push(question)
    })
    this.currentQuestion = 0
  },
  previousQuestion() {
    this.currentQuestion--;
    if(this.questions[this.currentQuestion].elem.querySelector('.required') && this.questions[this.currentQuestion].elem.dataset.type === 'radio'){
      if (!this.validateCurrentQuestion()){
        return false;
      }
    }
    this.nextButton = true;
  },
  nextQuestion() {
    if(this.currentQuestion < this.questions.length-1){
      if(this.questions[this.currentQuestion].elem.querySelector('.required') && this.questions[this.currentQuestion].elem.dataset.type === 'radio'){
        if (!this.validateCurrentQuestion()){
          return false;
        }
      }
      this.currentQuestion++
      if(this.currentQuestion === this.questions.length-1){
        this.button = 'Отправить анкету'
      }else{
        this.button = 'Далее'
      }
      if (!this.validateCurrentQuestion() && this.questions[this.currentQuestion].elem.querySelector('.required')){
        this.nextButton = false;
      }

    }else{
      this.validateAndSubmit()
    }
  },
  questionChange(){
    if (!this.validateCurrentQuestion()){
      return false;
    }
    this.questions[this.currentQuestion].is_answered = true;
    this.nextButton = true;
    let score = Number(this.$el.value);
    let commentField = this.$el.closest('.questions-item').querySelector('.question-comment');
    if(commentField) {
      if(score <= 8){
        commentField.style.display = null;
      }else{
        commentField.style.display = 'none';
      }

    }


    console.log('this.$el.value', this.$el.value)
  },
  validateCurrentQuestion(){
    const group = this.questions[this.currentQuestion].elem.querySelectorAll('input[type="radio"]')
    if (!this.isRadioGroupValid(group)){
      return false;
    }
    return true;
  },
  validateAndSubmit() {
    const form = this.$refs.form;
    if (this.validateForm(form)) {
      this.submitForm(form);
    }
  },

  validateForm(form) {
    const radioGroups = this.getRadioGroups(form);
    const allGroupsValid = radioGroups.every(group => this.isRadioGroupValid(group));

    if (!allGroupsValid) {
      this.showError = true;
      this.showSuccess = false;
      window.alert('Пожалуйста, ответьте на все вопросы');
      return false;
    }

    this.showError = false;
    this.errorMessage = '';
    return true;
  },

  getRadioGroups(form) {
    const radioInputs = form.querySelectorAll('input.required[type="radio"]');
    const groupNames = new Set(Array.from(radioInputs).map(input => input.name));
    return Array.from(groupNames).map(name => form.querySelectorAll(`input[type="radio"][name="${name}"]`));
  },

  isRadioGroupValid(group) {
    return Array.from(group).some(radio => radio.checked);
  },

  async submitForm(form) {
    this.isSubmitting = true;
    const formData = new FormData(form);

    try {
      const response = await fetch(form.action || window.location.href, {
        method: 'POST',
        body: formData
      });

      if (response.ok) {
        this.showSuccess = true;
        // this.successMessage = 'Форма успешно отправлена!';
        Fancybox.show(
          [
            {
              src: '#survey-success'
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
        form.reset();
      } else {
        throw new Error(response.message);
      }
    } catch (error) {
      window.alert(error.message);
      this.showError = true;
      // this.errorMessage = error.message;
    } finally {
      this.isSubmitting = false;
    }
  }
}));
Alpine.data('headerData', () => ({
  open: false,
  showHeader: false,
  lastScrollTop: 0,
  scrollThreshold: 200,
  init() {
    if (window.innerWidth < 1024) {
      this.showHeader = true;
    }
    window.addEventListener('scroll', () => {
      this.handleScroll();
    });
  },
  handleScroll() {
    let st = window.pageYOffset || document.documentElement.scrollTop;

    if (st < this.scrollThreshold && window.innerWidth > 1024) {
      this.showHeader = false;
      this.lastScrollTop = st <= 0 ? 0 : st;
      return;
    }
    this.showHeader = true;
    // if (st > this.lastScrollTop) {
    //   this.showHeader = false;
    // } else {
    //   this.showHeader = true;
    // }

    this.lastScrollTop = st <= 0 ? 0 : st;
  }
}));
Alpine.data('productsLoader', (initialFilters = {}) => ({
  products: [],
  page: 1,
  loading: false,
  noMoreProducts: false,
  observer: null,
  filters: initialFilters,

  initializeLoader() {
    this.setupIntersectionObserver();
    this.loadMore();
  },

  setupIntersectionObserver() {
    this.observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting && !this.loading && !this.noMoreProducts) {
          this.loadMore();
        }
      });
    }, { rootMargin: '100px' });

    this.$watch('products', () => {
      this.observeLastProduct();
    });
  },

  observeLastProduct() {
    if (this.products.length > 0) {
      this.$nextTick(() => {
        const productsContainer = this.$el.querySelector('.products-container');
        const lastProduct = productsContainer.lastElementChild;
        if (lastProduct) {
          this.observer.observe(lastProduct);
        }
      });
    }
  },

  loadMore() {
    if (this.loading || this.noMoreProducts) return;

    this.loading = true;
    // const queryParams = new URLSearchParams({
    //   page: this.page,
    //   ...this.serializeFilters(this.filters)
    // });
    var url = '/api/products';
    var query = [];
    query.push(encodeURIComponent('page') + '=' + encodeURIComponent(this.page));
    for (var key in this.filters) {
      if (Array.isArray(this.filters[key])) {
        this.filters[key].forEach((item, index) => {
          query.push(encodeURIComponent(`${key}[${index}]`) + '=' + encodeURIComponent(item));
        });
      } else {
        query.push(encodeURIComponent(key) + '=' + encodeURIComponent(this.filters[key]));
      }
    }
    // Проверяем, содержит ли URL уже параметры
    var separator = url.includes('?') ? '&' : '?';
    url += (query.length ? separator + query.join('&') : '')

    fetch(url)
      .then(response => response.json())
      .then(data => {
        this.products = [...this.products, ...data.data];
        this.page++;
        this.loading = false;
        if (data.data.length < 10) {
          this.noMoreProducts = true;
        }
        setTimeout(()=>{

          this.initSwiper();
          let tooltipListener = new CustomEvent('tooltipListener');
          window.dispatchEvent(tooltipListener);
        },100)
      })
      .catch(error => {
        console.error('Error loading products:', error);
        this.loading = false;
      });
  },

  serializeFilters(filters) {
    const serialized = {};
    for (const [key, value] of Object.entries(filters)) {
      if (Array.isArray(value)) {
        serialized[key] = value.join(',');
      } else {
        serialized[key] = value;
      }
    }
    return serialized;
  },

  updateFilters(newFilters) {
    this.filters = { ...this.filters, ...newFilters };
    this.resetAndReload();
  },

  resetAndReload() {
    this.products = [];
    this.page = 1;
    this.noMoreProducts = false;
    this.loadMore();
  },

  initSwiper() {
    const swiperContainers = document.querySelectorAll('.product-item-swiper');
    swiperContainers.forEach((container) => {
      // Проверяем, есть ли уже инициализированный Swiper в контейнере
      if (!container.swiper) {
        // Если Swiper не инициализирован, создаём новый экземпляр
        new Swiper(container, window.swiperOptions);
      }
    });
  },

  btnTooltipListener() {
      const btnTooltips = document.querySelectorAll('.btn-tooltip');
      btnTooltips.forEach((btn) => {
        btn.removeEventListener('click', btnTooltipListenerHandler);
        btn.addEventListener('click', btnTooltipListenerHandler);
      });
    }
}))

Alpine.start();


function autoResize(textarea) {
  textarea.style.height = 'auto';
  textarea.style.height = textarea.scrollHeight + 2 + 'px';
}

// Get the textarea element
const textareaFields = document.querySelectorAll('textarea.auto-height');

textareaFields.forEach((textarea) => {
  textarea.addEventListener('input', function() {
    autoResize(this);
  });
})

import './helper';
import './public/common';
import './public/header';
import './public/product';
// import './public/cart';
// import './public/order';
import './public/starter';
