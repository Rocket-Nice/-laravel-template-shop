import {checkChoicesDropdown, fetchData, formatPrice} from "../utilites.js";
import {getOrderTotal} from "../starter.js";

export class Shipping{
  constructor() {
    this.method = null;
    this.methodElem = null;
    this.price = 0;
    this.priceField = null;
    this.priceElem = null;
    this.priceElemText = null;
    this.priceElemPrice = null;
    this.moduleElem = null;
    this.routes = {};
    this.postcode = null;
    this.postcodeElem = null;
    this.country = null;
    this.countryElem = null;
    this.region = null;
    this.regionName = null;
    this.regionElem = null;
    this.regionChoice = null;
    this.city = null;
    this.cityName = null;
    this.cityElem = null;
    this.cityChoice = null;
    this.pvz = null;
    this.pvzAddress = null;
    this.pvzElem = null;
    this.pvzChoice = null;
    this.street = null;
    this.streetElem = null;
    this.house = null;
    this.houseElem = null;
    this.flat = null;
    this.flatElem = null;
    this.pickupModule = null;
    this.onChangeShippingMethod = this.onChangeShippingMethod.bind(this);
    this.onChangePostcode = this.onChangePostcode.bind(this);
    this.onChangeCountry = this.onChangeCountry.bind(this);
    this.onChangeRegion = this.onChangeRegion.bind(this);
    this.onChangeCity = this.onChangeCity.bind(this);
    this.onChangePvz = this.onChangePvz.bind(this);
    this.onClickOpenMap = this.onClickOpenMap.bind(this);
    this.onClickMap = this.onClickMap.bind(this);
    this.onClickCdekCourierCalculate = this.onClickCdekCourierCalculate.bind(this);
  }

  init(){
    if(!document.getElementById('shipping-code')) return false;
    // сохраняем роуты и другие данные
    const inputJsData = document.querySelectorAll('.js_data.shipping_route');
    for (var i = 0;i<inputJsData.length;i++){
      let element = inputJsData[i];
      this.routes[element.id] = element.value;
      let parent = element.parentNode;
      parent.removeChild(element);
    }
    // инициализируем поле стоимости
    this.priceField = document.getElementById('shipping-price');
    this.priceElem = document.getElementById('shipping-price-field');
    this.priceElemText = document.getElementById('shipping-price-text');
    this.priceElemPrice = document.getElementById('shipping-price-info');

    // инициализируем модуль выбора адреса
    this.moduleElem = document.getElementById('shippingModule');
    // инициализируем поле "страна"
    this.countryElem = document.getElementById('country');
    let defaultCountry = this.countryElem.value;
    if (localStorage.getItem('country') !== null) {
      defaultCountry = localStorage.getItem('country');
    }

    this.setCountry(defaultCountry)
    this.countryElem.addEventListener('change', this.onChangeCountry);
    this.checkInnField()
    const pochtaPostcode = document.getElementById('pochta-postcode');

    if(pochtaPostcode){
      this.postcodeElem = pochtaPostcode;
      pochtaPostcode.addEventListener('change', this.onChangePostcode)
    }

    if (localStorage.getItem('postcode') !== null) {
      this.postcode = localStorage.getItem('postcode');
    }
    if (localStorage.getItem('region') !== null) {
      this.region = localStorage.getItem('region');
    }
    if (localStorage.getItem('city') !== null) {
      this.city = localStorage.getItem('city');
    }
    if (localStorage.getItem('pvz') !== null) {
      this.pvz = localStorage.getItem('pvz');
    }
    if (localStorage.getItem('street') !== null) {
      this.street = localStorage.getItem('street');
    }
    if (localStorage.getItem('house') !== null) {
      this.house = localStorage.getItem('house');
    }
    if (localStorage.getItem('flat') !== null) {
      this.flat = localStorage.getItem('flat');
    }
    // инициализируем поле выбора способа оплаты
    this.methodElem = document.getElementById('shipping-code');
    this.methodElem.addEventListener('change', this.onChangeShippingMethod);
    if (localStorage.getItem('shippingMethod') !== null) {
      this.setShippingMethod(localStorage.getItem('shippingMethod'));
      this.methodElem.dispatchEvent(new Event('change', {
        'bubbles': true,
        'cancelable': true
      }));
    }
    this.pickupModule = document.getElementById('pickupModule');
  }

  setShippingMethod(method){
    this.method = method;
    localStorage.setItem('shippingMethod', method);
    const shippingMethodOption = Array.from(this.methodElem.options).find(option => option.value === method);
    if (shippingMethodOption) {
      shippingMethodOption.selected = true
    }else if(this.methodElem.selectedIndex){
      this.methodElem.options[this.methodElem.selectedIndex].selected = false;
    }
  }
  setShippingPrice(price){
    if(!(!isNaN(parseFloat(price)) && isFinite(price) && /^[-+]?\d*\.?\d+$/.test(price)) && price !== 0) alert('Ошибка расчета стоимости доставки');
    if(price > 0){
      this.priceElem.style.display = '';
    }else{
      this.priceElem.style.display = 'none';
    }
    this.price = parseInt(price);
    this.priceField.value = price;
    this.priceElemPrice.innerHTML = formatPrice(price);
  }

  setCountry(country){
    this.country = country
    localStorage.setItem('country', country);
    const countryOption = Array.from(this.countryElem.options).find(option => option.value === country);
    if (countryOption) {
      countryOption.selected = true
    }else if(this.countryElem.selectedIndex){
      this.countryElem.options[this.countryElem.selectedIndex].selected = false;
    }
  }
  setPostcode(postcode){
    this.postcode = postcode
    localStorage.setItem('postcode', postcode);
    if(!this.postcodeElem) return;
    if(!this.postcodeElem.value) this.postcodeElem.value = postcode;
  }
  setRegion(region){
    this.region = region
    localStorage.setItem('region', region);
    const regionOption = Array.from(this.regionElem.options).find(option => option.value === region);
    if (regionOption) {
      regionOption.selected = true;
      this.regionName = regionOption.textContent;
    }else if(this.regionElem.selectedIndex && this.regionElem.options[this.regionElem.selectedIndex]){
      this.regionElem.options[this.regionElem.selectedIndex].selected = false;
    }
    if(this.regionChoice){
      this.regionChoice.setChoiceByValue(region);
    }else if(!region && this.regionChoice){
      this.regionChoice.passedElement.element.removeEventListener('showDropdown', checkChoicesDropdown);
      this.regionChoice.destroy();
    }
  }
  setCity(city){
    this.city = city
    localStorage.setItem('city', city);
    const cityOption = Array.from(this.cityElem.options).find(option => option.value === city);
    if (cityOption) {
      cityOption.selected = true
      this.cityName = cityOption.textContent;
    }else if(this.cityElem.selectedIndex){
      this.cityElem.options[this.cityElem.selectedIndex].selected = false;
    }
    if(this.cityChoice){
      this.cityChoice.setChoiceByValue(city);
    }else if(!city && this.cityChoice){
      this.cityChoice.passedElement.element.removeEventListener('showDropdown', checkChoicesDropdown);
      this.cityChoice.destroy();
    }
  }
  setPvz(pvz){
    this.pvz = pvz
    localStorage.setItem('pvz', pvz);
    if(!this.pvzElem) return;
    const pvzOption = Array.from(this.pvzElem.options).find(option => option.value === pvz);
    if (pvzOption) {
      pvzOption.selected = true
      this.pvzAddress = pvzOption.textContent;
    }else if(this.pvzElem.selectedIndex){
      this.pvzElem.options[this.pvzElem.selectedIndex].selected = false;
    }
    if(pvz && this.pvzChoice){
      this.pvzChoice.setChoiceByValue(pvz);
    }else if(!pvz && this.pvzChoice){
      this.pvzChoice.passedElement.element.removeEventListener('showDropdown', checkChoicesDropdown);
      this.pvzChoice.destroy();
    }
  }
  setStreet(street){
    this.street = street
    localStorage.setItem('street', street);
    if(!this.streetElem) return;
    if(!this.streetElem.value) this.streetElem.value = street;
  }
  setHouse(house){
    this.house = house
    localStorage.setItem('house', house);
    if(!this.houseElem) return;
    if(!this.houseElem.value) this.houseElem.value = house;
  }
  setFlat(flat){
    this.flat = flat
    localStorage.setItem('flat', flat);
    if(!this.flatElem) return;
    if(!this.flatElem.value) this.flatElem.value = flat;
  }
  onChangeShippingMethod(event){
    event.preventDefault();
    const chosenMethod = this.methodElem.value;
    this.resetShipping();
    this.setShippingMethod(chosenMethod);

    this.initShippingModule();
    // скрываем и показываем нужную кнопку
    // const button_box = elem.closest('.shiping-field').querySelector('.button-box');
    // const buttons = document.querySelectorAll('.shipping-methods .button-box');
    // for (let i = 0; i < buttons.length; i++) {
    //   buttons[i].classList.remove('block');
    //   buttons[i].classList.add('hidden');
    // }
    // if (button_box){
    //   button_box.classList.remove('hidden');
    //   button_box.classList.add('block');
    // }
    //
    //***
    // const pochtaAddress = document.getElementById('pochta-address');
    // if (pochtaAddress){
    //   const pochtaFields = pochtaAddress.querySelectorAll('input[data-required]')
    //   if (shippingCode == 'pochta'){
    //     pochtaAddress.classList.remove('hidden');
    //     pochtaAddress.classList.add('block');
    //     pochtaFields.forEach((field) => {
    //       field.required = true;
    //     })
    //     const pochtaPostcode = document.getElementById('pochta-postcode')
    //     if(pochtaPostcode && pochtaPostcode.value != ''){
    //       pochtaPriceRussia(pochtaPostcode.value)
    //     }
    //   }else{
    //     pochtaAddress.classList.remove('block');
    //     pochtaAddress.classList.add('hidden');
    //     pochtaFields.forEach((field) => {
    //       field.required = false;
    //     })
    //   }
    // }
  }

  resetShipping(){
    this.setShippingPrice(0);
    this.setShippingMethod(null);
    getOrderTotal();
    this.closePochtaAddress();
    this.moduleElem.innerHTML = '';
    if(this.pickupModule) this.pickupModule.innerHTML = '';
    if(localStorage.getItem('region') !== null) localStorage.removeItem('region');
    if(localStorage.getItem('city') !== null) localStorage.removeItem('city');
    if(localStorage.getItem('country') !== null) localStorage.removeItem('country');

  }

  initShippingModule(){
    const actions = {
      nt: () => {
        this.createShippingModuleBlock();
      },
      cdek: () => {
        this.createShippingModuleBlock();
      },
      yandex: () => {
        this.createShippingModuleBlock();
      },
      x5post: () => {
        this.createShippingModuleBlock();
      },
      cdek_courier: () => {
        this.createShippingModuleBlock();
      },
      pochta: () => {
        this.showPochtaAddress();
        Fancybox.show(
          [
            {
              src: '#pochtaDisclaimer'
            },
          ],
          {
            loop: false,
            touch: false,
            contentClick: false,
            dragToClose: false,
          }
        );
      },
      pickup: () => {
        const pickups_data = window.pickupsData;
        const thisPickup = pickups_data.find(item => item.code === this.method);
        if(thisPickup){
          document.getElementById('pickupModule').textContent = thisPickup.address
        }
      }
    };
    (actions[this.method] || (() => {}))();
  }

  initMap(collection){
    const createShippingMapModal = this.createShippingMapModal;
    ymaps.ready(
      function () {
        if (typeof collection != 'undefined' && collection) {
          createShippingMapModal();
          if(!collection.data.features[0]){
            return false
          }
          var map = new ymaps.Map('mapModule', {
              center: collection.data.features[0].geometry.coordinates,
              controls: collection.controls,
              zoom: 10,
            }),
            cluster = new ymaps.ObjectManager({
              clusterize: true,
              gridSize: 32,
              margin: 20,
              preset: 'islands#invertedGrayClusterIcons',
            });
          cluster.add(collection.data);

          map.geoObjects.add(cluster);

          cluster.clusters.events
            .add('mouseenter', function (e) {
              cluster.clusters.setClusterOptions(e.get('objectId'), {
                preset: 'islands#grayClusterIcons'
              });
            })
            .add('mouseleave', function (e) {
              cluster.clusters.setClusterOptions(e.get('objectId'), {
                preset: 'islands#invertedGrayClusterIcons'
              });
            });

          for (var i in collection.delivery) {
            switch (i) {
              case 'PVZ':
                var floatIndex = 2;
                break;
              case 'POSTAMAT':
                var floatIndex = 1;
                break;
              default:
                var floatIndex = 0;
                break;
            }

            var delivery = collection.delivery[i],
              button = new ymaps.control.Button({
                data: {
                  content: delivery.content,
                  title: delivery.title,
                  code: delivery.code,
                },
                options: {
                  selectOnClick: true,
                  size: 'small',
                  float: 'left',
                  floatIndex: floatIndex,
                  maxWidth: 150,
                }
              });

            map.controls.add(button);

            button.events
              .add('press', function (e) {
                let target = e.get('target');
                let code = target.data.get('code');

                map.controls.each(function (e) {
                  if (e.options.getName() === 'button' && e !== target && e.deselect()) {
                    e.deselect();
                  }
                });

                cluster.setFilter("object.params.code == code");
              })
              .add('deselect', function (e) {
                cluster.setFilter(false);
              });
          }
        }
      }
    );
    var modalElement = document.getElementById('map-wrapper');
    if (modalElement) {
      modalElement.style.display = 'block';
    }
  }
  checkInnField(){
    const innAddress = document.getElementById('inn-address');
    if(this.country==14 || this.country==57){
      innAddress.style.display = ''
      innAddress.querySelector('input').required = true;
    }else{
      innAddress.style.display = 'none'
      innAddress.querySelector('input').required = false;
    }
  }
  onChangeCountry(e){
    e.preventDefault();
    this.setCountry(this.countryElem.value);
    this.resetShipping();

    Array.from(this.methodElem.options).forEach((item) => {
      if (!this.checkAccessShipping(item.value)){
        item.selected = false;
        item.disabled = true;
      }else{
        item.disabled = false;
      }
    })
    this.methodElem.dispatchEvent(new Event('change', {
      'bubbles': true,
      'cancelable': true
    }));
    this.checkInnField()
  }
  onChangePostcode(e){
    e.preventDefault();
    if(!!this.postcodeElem.value){
      this.setPostcode(this.postcodeElem.value);
    }else{
      this.setPostcode('');
      this.setShippingPrice(0);
      getOrderTotal();
    }
    if(!!this.postcode){
      this.calculateShippingPrice();
    }
  }
  onChangeRegion(e){
    e.preventDefault();
    this.setRegion(this.regionElem.value);
    const route = this.routes[`route_${this.method}_cities`];

    if(!this.region) { // если регион не выбрае, убираем пвз и города
      if(this.pvz) this.setPvz(null);
      if(this.city) this.setCity(null);
      this.cityElem.style.display = 'none';
      const shippingDetail = document.getElementById(`${this.method}-shipping`);
      if(shippingDetail){
        shippingDetail.innerHTML = '';
      }
      return false;
    }

    fetchData(route, 'GET', {region: this.region}).then(response => {
      if (!response) {
        alert('Нет доступных городов для доставки');
        return false;
      }

      if (this.cityChoice !== null) {
        this.cityChoice.passedElement.element.removeEventListener('showDropdown', checkChoicesDropdown);
        this.cityChoice.destroy();
      }

      this.cityElem.innerHTML = '';
      var option = document.createElement('option');
      option.innerText = 'Выберите город';
      option.setAttribute('disabled', true);
      option.setAttribute('selected', true);
      this.regionElem.append(option);


      for (var i = 0; i < response.length; i++) {
        var option = document.createElement('option');
        if (this.method === 'yandex') {
          option.value = response[i].id;
          option.innerText = response[i].Name;
        } else if (this.method === 'nt' || this.method === 'cdek' || this.method === 'cdek_courier') {
          if (this.method === 'cdek' || this.method === 'nt'){
            option.value = response[i].id;
          }else{
            option.value = response[i].code;
          }
          if (response[i].sub_region) {
            option.innerText = response[i].city + ' (' + response[i].sub_region + ')';
          } else {
            option.innerText = response[i].city;
          }
        }else if(this.method === 'x5post'){
          option.value = response[i].id;
          option.innerText = response[i].name;
        }

        this.cityElem.append(option);
        if((this.city && this.city === option.value) || response.length === 1){
          this.setCity(option.value);
        }
        this.cityElem.style.display = null;
      }
      this.cityChoice = new Choices(this.cityElem, {
        removeItemButton: true,
        shouldSort: false,
        noChoicesText: 'Пусто',
        itemSelectText: ''
      });
      this.cityChoice.passedElement.element.addEventListener('showDropdown', checkChoicesDropdown);
      if(this.cityElem){
        this.cityElem.dispatchEvent(new Event('change', {
          'bubbles': true,
          'cancelable': true
        }));
      }
    });
  }
  onChangeCity(e){
    e.preventDefault();
    this.setCity(this.cityElem.value);
    const route = this.routes[`route_${this.method}_pvz`];
    if(!this.city){
      if(this.pvz) this.setPvz(null);
    }
    this.setShippingPrice(0);
    if (['nt', 'cdek', 'yandex', 'x5post'].includes(this.method)) {
      if(!this.region || !this.city) return false;
      let params = {region: this.region, city: this.city};
      if(this.method == 'nt') params.method = 'nt';
      fetchData(route, 'GET', params).then(response => {
        if (!response) {
          alert('Нет доступных пунктов выдачи');
          return false;
        }
        if(this.pvzChoice){
          this.pvzChoice.passedElement.element.removeEventListener('showDropdown', checkChoicesDropdown);
          this.pvzChoice.destroy();
        }
        const shippingDetail = document.getElementById(`${this.method}-shipping`);
        shippingDetail.innerHTML = response.html;
        this.pvzElem = document.getElementById(`${this.method}-pvz`);
        // если пвз уже задан и есть в текущем списке пвз
        if(this.pvz && Array.from(this.pvzElem.options).find(option => option.value === this.pvz)){
          this.setPvz(this.pvz);
        }else{
          this.setPvz(null);
        }
        this.pvzChoice = new Choices(this.pvzElem, {
          removeItemButton: true,
          shouldSort: false,
          noChoicesText: 'Пусто',
          itemSelectText: ''
        })
        this.pvzChoice.passedElement.element.addEventListener('showDropdown', checkChoicesDropdown);
        this.pvzElem.addEventListener('change', this.onChangePvz);

        const findPvzOnMapField = document.getElementById(`findPvzOnMap_${this.method}`);
        findPvzOnMapField.addEventListener('click', this.onClickOpenMap);
        if(this.pvzElem && this.pvz){
          this.pvzElem.dispatchEvent(new Event('change', {
            'bubbles': true,
            'cancelable': true
          }));
        }
      });
    } else if(this.method === 'cdek_courier') {
      var html = `<div id="${this.method}-address-form"><div class="mb-4">
          <input type="text" name="${this.method}-street" placeholder="Улица" class="block w-full border-0 border-b border-b-myGray bg-transparent placeholder-myGray m-text-body d-text-body py-1.5 px-3 focus:ring-0 focus:border-b-black" value="${this.street ?? ''}" id="${this.method}-street">
      </div>
      <div class="flex mb-4 -mx-2"><div class="w-1/2 px-2"><div class="form-group">
        <input type="text" name="${this.method}-house" placeholder="Дом" class="block w-full border-0 border-b border-b-myGray bg-transparent placeholder-myGray m-text-body d-text-body py-1.5 px-3 focus:ring-0 focus:border-b-black" value="${this.house ?? ''}" id="${this.method}-house">
      </div></div>
      <div class="w-1/2 px-2"><div class="form-group">
        <input type="text" name="${this.method}-flat" placeholder="Квартира" class="block w-full border-0 border-b border-b-myGray bg-transparent placeholder-myGray m-text-body d-text-body py-1.5 px-3 focus:ring-0 focus:border-b-black" value="${this.flat ?? ''}" id="${this.method}-flat">
      </div></div></div>
      <div  id="${this.method}-btn"><button type="button" class="block w-full border-0 border-b border-b-myGray bg-transparent placeholder-myGray m-text-body d-text-body py-1.5 px-3 focus:ring-0 focus:border-b-black" id="${this.method}-calculate">Рассчитать стоимость</button></div></div>`;

      // html += '<div class="form-check">\n' +
      //   '                      <input class="form-check-input" type="checkbox" name="${this.method}-express" id="${this.method}-express" value="1" required>\n' +
      //   '                      <label class="form-check-label" for="${this.method}-express">Экспресс доставка</label>\n' +
      //   '                    </div>';
      if(!document.getElementById(`${this.method}-address-form`)){
        var element = document.getElementById(`${this.method}-shipping`);
        if (element) {
          element.innerHTML = html;
          this.streetElem = document.getElementById(`${this.method}-street`);
          this.houseElem = document.getElementById(`${this.method}-house`);
          this.flatElem = document.getElementById(`${this.method}-flat`);
          let button = document.getElementById(`${this.method}-calculate`);
          if (button){
            button.addEventListener('click', this.onClickCdekCourierCalculate)
          }
          if (this.region && this.city && this.street && this.house) {
            this.calculateShippingPrice();
          }

        }
      }

    }
  }
  onChangePvz(e){
    e.preventDefault();
    this.setPvz(this.pvzElem.value);
    this.calculateShippingPrice();
  }

  onClickOpenMap(e){
    e.preventDefault();
    var map_data = document.getElementById(`data_map_${this.method}`);
    if(!map_data) return false;
    map_data = JSON.parse(map_data.value);
    this.initMap(map_data);
    // //
    document.getElementById('mapContainer').removeEventListener('click', this.onClickMap);
    document.getElementById('mapContainer').addEventListener('click', this.onClickMap);

    Fancybox.show(
      [
        {
          src: '#mapContainer'
        },
      ],
      {
        loop: false,
        touch: false,
        contentClick: false,
        dragToClose: false,
      }
    );
  }

  onClickMap(e){
    if (e.target.classList.contains('ll_set_point')) {
      const pvzCode = e.target.dataset.pvzCode;
      if(pvzCode){
        this.setPvz(pvzCode);
        this.calculateShippingPrice();
      }
    }
  }

  onClickCdekCourierCalculate(e){
    e.preventDefault();
    this.setStreet(this.streetElem.value);
    this.setHouse(this.houseElem.value);
    this.setFlat(this.flatElem.value);
    if (!this.region || !this.city || !this.street || !this.house) {
      if (!this.region) alert('Укажите полный адрес. Не указан регион');
      if (!this.city) alert('Укажите полный адрес. Не указан город');
      if (!this.street) alert('Укажите полный адрес. Не указана улица');
      if (!this.house) alert('Укажите полный адрес. Не указан дом');
      return false;
    }
    this.calculateShippingPrice();
  }
  loadRegions(){
    const route = this.routes[`route_${this.method}_regions`]
    if(!this.regionElem) alert('Ошибка загрузки регионов')

    let params = {country: this.country};
    if(this.method == 'nt') params.method = 'nt';
    fetchData(route, 'GET', params).then(response => {
      if (!response) {
        alert('Нет доступных регионов для доставки');
        return false;
      }
      this.regionElem.querySelector(`.${this.method}-loading`).remove();
      if (this.regionChoice !== null) {
        this.regionChoice.passedElement.element.removeEventListener('showDropdown', window.checkChoicesDropdown);
        this.regionChoice.destroy();
      }
      var option = document.createElement('option');
      option.innerText = 'Выберите регион';
      option.setAttribute('disabled', true);
      option.setAttribute('selected', true);
      this.regionElem.append(option);

      for (var i = 0; i < response.length; i++) {
        var option = document.createElement('option');
        if (this.method === 'yandex') {
          option.value = response[i].id;
          option.innerText = response[i].name;
        } else if (this.method === 'nt' || this.method === 'cdek' || this.method === 'cdek_courier') {
          option.value = response[i].id;
          option.innerText = response[i].region;
        }else if (this.method === 'x5post') {
          option.value = response[i].id;
          option.innerText = response[i].name;
        }
        this.regionElem.append(option);
        if(this.region && this.region === option.value){
          this.setRegion(this.region);
        }
      }
      this.regionChoice = new Choices(this.regionElem, {
        removeItemButton: true,
        shouldSort: false,
        noChoicesText: 'Пусто',
        itemSelectText: ''
      })
      this.regionChoice.passedElement.element.addEventListener('showDropdown', checkChoicesDropdown);
      if(this.region && this.regionElem){
        this.regionElem.dispatchEvent(new Event('change', {
          'bubbles': true,
          'cancelable': true
        }));
      }
    });
  }

  calculateShippingPrice(){
    const route = this.routes[`route_calculate_${this.method}`];
    this.showLoader();
    const startTime = performance.now();
    let calculateParams;
    let fetchMethod = 'GET';
    if (['nt', 'cdek', 'yandex', 'x5post'].includes(this.method)) {
      if(!this.pvz) return false;
      calculateParams = {code: this.pvz};
    } else if(this.method === 'cdek_courier') {
      if(!this.city){
        return false;
      }
      calculateParams = {city: this.city, shipping: 'cdek_courier'};
    }else if(this.method === 'pochta'){
      if(!this.postcode){
        return false;
      }
      calculateParams = {type: 'russia', to: this.postcode};
      fetchMethod = 'POST';
    }
    fetchData(route, fetchMethod, calculateParams).then(response => {
      if (!response || (response.shippingPrice !== 0 && !response.shippingPrice && !response.price)) {
        alert('Ошибка расчета стоимости доставки');
        this.hideLoader();
        return false;
      }
      let price;
      if(this.method === 'pochta'){
        if (response.price > 0) {
          price = Number(response.price.toFixed(0));
        } else {
          price = Number(response.price);
        }
      }else{
        price = response.shippingPrice;
      }

      this.setShippingPrice(price);
      getOrderTotal();

      const pvzIdField = document.getElementById(`${this.method}-pvz-id`);
      if(pvzIdField) pvzIdField.value = this.pvz ?? '';
      const pvzAddressField = document.getElementById(`${this.method}-pvz-address`);
      if(pvzAddressField) pvzAddressField.value = this.pvzAddress ?? '';
      const regionField = document.getElementById(`${this.method}-form-region`);
      if(regionField) regionField.value = this.regionName ?? '';
      const cityField = document.getElementById(`${this.method}-form-city`);
      if(cityField) cityField.value = this.city ?? '';
      const streetField = document.getElementById(`${this.method}-form-street`);
      if(streetField) streetField.value = this.street ?? '';
      const houseField = document.getElementById(`${this.method}-form-house`);
      if(houseField) houseField.value = this.house ?? '';
      const flatField = document.getElementById(`${this.method}-form-flat`);
      if(flatField) flatField.value = this.flat ?? '';
      const addressField = document.getElementById(`${this.method}-form-address`);
      if(addressField) addressField.value = this.getFullAddress();

      const endTime = performance.now();
      const executionTime = endTime - startTime;
      setTimeout(() => {
        this.hideLoader();
        Fancybox.close();
      },500 - executionTime > 0 ? 500 - executionTime : 0);
    }).catch(error => {
      alert('Ошибка расчета стоимости доставки');
      this.hideLoader();
      Fancybox.close();
    });
  }

  createShippingMapModal(){
    var modalElem = document.createElement('div');

    modalElem.id = 'map-wrapper';
    modalElem.style.paddingLeft = '0';
    modalElem.style.paddingRight = '0';
    modalElem.style.width = '100%';
    modalElem.style.minHeight = '320px';
    modalElem.style.overflow = 'hidden'
    modalElem.style.borderRadius = '22px'

    var modalContainer = document.getElementById('mapContainer');
    modalContainer.innerHTML = '';
    modalContainer.appendChild(modalElem);

    var height = document.documentElement.clientHeight * 0.8;
    if(height < 320){
      height = 320
    }
    var modalModule = document.createElement('div');
    modalModule.id = 'mapModule';
    modalModule.style.width = '100%';
    modalModule.style.height = height + 'px';

    var modalWrapper = document.getElementById('map-wrapper');
    modalWrapper.appendChild(modalModule);
  }
  createShippingModuleBlock(){
    const blockId = `blockShipping${this.method}`;
    if (document.getElementById(blockId)) return false;

    var shippingModuleBlock = document.createElement('div');
    shippingModuleBlock.id = blockId;
    shippingModuleBlock.classList.add('shipping-main');
    shippingModuleBlock.classList.add('container');
    shippingModuleBlock.classList.add('w-full');

    const regions = this.createSelectElement('region');
    const cities = this.createSelectElement('city');
    const shippingDetail = document.createElement('div');
    shippingDetail.id = `${this.method}-shipping`;
    shippingModuleBlock.appendChild(regions);
    shippingModuleBlock.appendChild(cities);
    shippingModuleBlock.appendChild(shippingDetail);

    this.moduleElem.appendChild(shippingModuleBlock);

    this.regionElem = regions.querySelector('select');
    this.regionElem.addEventListener('change', this.onChangeRegion);
    this.cityElem = cities.querySelector('select');
    this.cityElem.style.display = 'none';
    this.cityElem.addEventListener('change', this.onChangeCity);
    // загружаем регионы
    this.loadRegions();
  }

  createSelectElement(elementName){
    const elementId = `${this.method}-${elementName}`
    const element = document.createElement('div');
    element.classList.add('mb-4');
    const field = document.createElement('select');
    field.name = elementName;
    field.id = elementId;
    field.className = 'block w-full border-0 border-b border-b-myGray bg-transparent placeholder-myGray m-text-body d-text-body py-1.5 px-3 focus:ring-0 focus:border-b-black';
    field.required = true;
    field.innerHTML = `<option disabled value="" class="${this.method}-loading">Загрузка...</option>`;
    element.appendChild(field);
    return element;
  }

  showPochtaAddress() {
    const pochtaAddress = document.getElementById('pochta-address');
    if (pochtaAddress){
      const pochtaFields = pochtaAddress.querySelectorAll('input[data-required]')
      pochtaAddress.style.display = null;
      pochtaFields.forEach((field) => {
        field.required = true;
      })

      if(!!this.postcode){
        this.calculateShippingPrice();
      }
    }
  }
  closePochtaAddress() {
    const pochtaAddress = document.getElementById('pochta-address');
    if (pochtaAddress){
      pochtaAddress.style.display = 'none';
      const pochtaFields = pochtaAddress.querySelectorAll('input[data-required]')
      pochtaFields.forEach((field) => {
        field.required = false;
      })
    }
  }

  showLoader() {
    let loader = document.getElementById('loader');
    if(!loader){
      loader = document.createElement('div');
      loader.id = 'loader';
      loader.style.display = 'none';
      loader.style.zIndex = 2000;
      loader.className = 'fixed left-0 top-0 right-0 bottom-0 w-full h-full bg-white/90 flex justify-center items-center';
      loader.innerHTML = `
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
          <div class="d-headline-4 m-headline-3">Считаем стоимость доставки</div>
        </div>
      `;
      document.body.appendChild(loader);
    }
    loader.style.display = null;
  }
  hideLoader() {
    let loader = document.getElementById('loader');
    if(loader){
      loader.style.display = 'none';
    }
  }

  getFullAddress(){
    var address = this.regionName + ', ' + this.cityName + ', ' + this.street + ', д. ' + this.house;
    if (flat) {
      address += ', кв. ' + this.flat;
    }
    return address;
  }

  checkAccessShipping(method){
    const allowMethods = this.countryElem.options[this.countryElem.selectedIndex].dataset.shipping.split(',');
    return allowMethods.includes(method);
  }
}
