// map module
function map_init(collection) {
  ymaps.ready(
    function () {
      if (typeof collection != 'undefined' && collection != '') {
        map_create_modal();
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
              target = e.get('target');
              code = target.data.get('code');

              map.controls.each(function (e) {
                if (e.options.getName() == 'button' && e != target && e.deselect()) {
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
}
function map_create_modal() {
  // html = '<div id="map-wrapper" class="ll_map popup" style="padding-left:0;padding-right:0;padding-bottom:40px;width: 100%;min-height: 320px"></div>';
  //
  // $('#ozonMap').append(html);
  // Создать новый элемент div
  var newDiv = document.createElement('div');

  newDiv.id = 'map-wrapper';
  newDiv.style.paddingLeft = '0';
  newDiv.style.paddingRight = '0';
  newDiv.style.paddingBottom = '40px';
  newDiv.style.width = '100%';
  newDiv.style.minHeight = '320px';

  var parentDiv = document.getElementById('mapContainer');
  parentDiv.appendChild(newDiv);
  // var width = $('#map-wrapper .ll_modal').width(),
  //   height = document.documentElement.clientHeight * .5;
  //
  // $('#map-wrapper').append('<div id="mapModule" style="width: ' + width + 'px; height: ' + height + 'px;"></div>');
  var modalElement = document.querySelector('#map-wrapper');
  var width = modalElement.offsetWidth;

  var height = document.documentElement.clientHeight * 0.5;
  if(height < 320){
    height = 320
  }
  var newDiv = document.createElement('div');
  newDiv.id = 'mapModule';
  newDiv.style.width = width + 'px';
  newDiv.style.height = height + 'px';

  var parentDiv = document.getElementById('map-wrapper');
  parentDiv.appendChild(newDiv);
}
var map_code = null;
function map_show_modal(filter, code, type) {
  if (typeof code != 'undefined' && code != '') {
    map_code = code;
  }

  // if (typeof type != 'undefined' && type != '') {
  //   $('#map_filter_' + type).click();
  // }
  // $('#map-wrapper').show();
  if (typeof type !== 'undefined' && type !== '') {
    var filterElement = document.getElementById('map_filter_' + type);
    if (filterElement) {
      filterElement.click();
    }
  }

  var modalElement = document.getElementById('map-wrapper');
  if (modalElement) {
    modalElement.style.display = 'block';
  }
}
// map module end

const shipping_fields = document.querySelectorAll('.shipping-item')

for (let i = 0; i < shipping_fields.length; i++) {
  shipping_fields[i].addEventListener('change', changeMethodShipping);
}

function changeMethodShipping(event){
  const this_method = event.target;
  const checkedInput = document.querySelector('.shipping-item:checked');
  const shippingInput = document.querySelector('input[name="shipping"]');

  shippingInput.value = this_method.value;
  resetShipping();
  // скрываем и показываем нужную кнопку
  const button_box = this_method.closest('.shiping-field').querySelector('.button-box');
  const buttons = document.querySelectorAll('.shipping-methods .button-box');
  const pochtaAddress = document.getElementById('pochta-address');
  for (let i = 0; i < buttons.length; i++) {
    buttons[i].classList.remove('block');
    buttons[i].classList.add('hidden');
  }
  if (button_box){
    button_box.classList.remove('hidden');
    button_box.classList.add('block');
  }
  if (pochtaAddress){
    const pochtaFields = pochtaAddress.querySelectorAll('input[data-required]')
    if (checkedInput && checkedInput.value == 'pochta'){
      pochtaAddress.classList.remove('hidden');
      pochtaAddress.classList.add('block');
      pochtaFields.forEach((field) => {
        field.required = true;
      })
      const pochtaPostcode = document.getElementById('pochta-postcode')
      if(pochtaPostcode && pochtaPostcode.value != ''){
        pochtaPriceRussia(pochtaPostcode.value)
      }
    }else{
      pochtaAddress.classList.remove('block');
      pochtaAddress.classList.add('hidden');
      pochtaFields.forEach((field) => {
        field.required = false;
      })
    }
  }
  if(this_method.classList.contains('is--pickup')){
    var shipping_address_info = document.getElementById('shipping-address-info')
    var shipping_name = document.querySelector(`label[for="${this_method.id}"]`).textContent
    var pickupAddress = this_method.dataset.address
    shipping_address_info.innerText = shipping_name+': '+pickupAddress
    shipping_address_info.classList.remove('hidden')
  }
}

const myData = window.myDataObject();
var inputJsData = document.querySelectorAll('.js_data');

for (var i = 0;i<inputJsData.length;i++){
  let element = inputJsData[i];
  let inputJsDataID = element.id;
  let inputJsDataVal = element.value;
  myData.setVar(inputJsDataID, inputJsDataVal);
  let parent = element.parentNode;
  parent.removeChild(element);
}

function checkAccessShipping(method){
  const shippingMethods = document.getElementById('country');
  const allowMethods = shippingMethods.options[shippingMethods.selectedIndex].dataset.shipping.split(',');
  if (!allowMethods.includes(method)){
    return false
  }else{
    return true
  }
}
const country_field = document.getElementById('country')
if(country_field){
  country_field.addEventListener('change', changeCountry);
}
const orderForm = document.getElementById('order-form')
if(orderForm){
  orderForm.addEventListener('input', function(event) {
    if(event.target.tagName.toLowerCase() === 'input') {
      localStorage.setItem(event.target.name, event.target.value);
    }
  });
}

document.addEventListener('DOMContentLoaded', function() {

  var inputs = document.querySelectorAll('#order-form input');

  inputs.forEach(function(input) {
    var name = input.getAttribute('name');
    if (localStorage.getItem(name) !== null && input.value === '' && input.name != 'promo') {
      input.value = localStorage.getItem(name);
    }
  });

  if(country_field){
    changeCountry()
  }

  const shipping = thisShippingMethod()

  if(shipping) {
    if(shipping == 'cdek'){
      const pvz_id = document.getElementById('cdek-pvz-id')
      if (pvz_id.value){
        calculateModule('cdek', pvz_id.value)
      }
    }else if(shipping == 'boxberry'){
      const pvz_id = document.getElementById('boxberry-pvz-id')
      if (pvz_id.value){
        calculateModule('boxberry', pvz_id.value)
      }
    }else if(shipping == 'pochta'){
      const pochtaPostcode = document.getElementById('pochta-postcode')
      if(pochtaPostcode && pochtaPostcode.value != ''){
        pochtaPriceRussia(pochtaPostcode.value)
        const pochtaAddress = document.getElementById('pochta-address');
        if (pochtaAddress){
          const pochtaFields = pochtaAddress.querySelectorAll('input[data-required]')
          pochtaAddress.classList.remove('hidden');
          pochtaAddress.classList.add('block');
          pochtaFields.forEach((field) => {
            field.required = true;
          })
          const pochtaPostcode = document.getElementById('pochta-postcode')
          if(pochtaPostcode && pochtaPostcode.value != ''){
            pochtaPriceRussia(pochtaPostcode.value)
          }
        }
      }
    }
  }
});
function changeCountry(){
  const shippingMethods = document.querySelectorAll('.shipping-item')
  resetShipping()
  shippingMethods.forEach((item) => {
    if (!checkAccessShipping(item.value)){
      item.checked = false;
      item.disabled = true;
      item.closest('.shiping-field').style.opacity = '0.5';
    }else{
      item.disabled = false;
      item.closest('.shiping-field').style.opacity = '';
    }
  })
}

const coosePvzButtons = document.querySelectorAll('.shipping-methods .button-box a');
coosePvzButtons.forEach((button)=>{
  button.addEventListener('click', (event)=>{
    event.preventDefault()
    const shipping = event.target.dataset.shipping

    choosedShippingModule(shipping)
  })
})

var regionChoise = null;
var pvzChoise = null;
var cityChoise = null;
function choosedShippingModule(shipping){
  if (!document.querySelector('#blockShipping' + shipping)) {
    var shippingName;
    if (shipping == 'boxberry') {
      shippingName = 'Boxberry доставка';
    } else if (shipping == 'cdek') {
      shippingName = 'Доставка СДЭК';
    } else if (shipping == 'cdek_courier') {
      shippingName = 'Доставка СДЭК курьер';
    }
    var blockShippingModule = document.createElement('div');
    blockShippingModule.id = 'blockShipping' + shipping;
    blockShippingModule.style.display = 'none';
    blockShippingModule.classList.add('shipping-main');
    blockShippingModule.classList.add('container');
    blockShippingModule.classList.add('w-full');
    blockShippingModule.classList.add('max-w-screen-lg');

    var html = '<div><div class="mb-6"><h3 class="d-headline-4 m-headline-3">' + shippingName + '</h3></div>';
    html += '<div class="mb-6">';
    html += '  <label class="d-text-body m-text-body" for="' + shipping + '-region">Регион</label>';
    html += '  <select name="region" id="' + shipping + '-region" required class="block w-full border border-myGray bg-transparent placeholder-myGray m-subtitle-2 d-subtitle-2 py-2.5 sm:py-1.75 px-3 leading-none focus:ring-0 focus:border-black">';
    html += '    <option disabled value="" id="regionsLoading">Загрузка...</option>';
    html += '  </select>';
    html += '</div>';
    html += '<div class="mb-6" id="field-cities" style="display: none">';
    html += '  <label class="d-text-body m-text-body" for="' + shipping + '-cities">Город</label>';
    html += '  <select name="city" id="' + shipping + '-cities" required  class="block w-full border border-myGray bg-transparent placeholder-myGray m-subtitle-2 d-subtitle-2 py-2.5 sm:py-1.75 px-3 leading-none focus:ring-0 focus:border-black"><option disabled value="">Выбрать...</option></select>';
    html += '</div>';
    html += '<div id="' + shipping + '-shipping"></div></div>';
    blockShippingModule.innerHTML = html;
    document.body.append(blockShippingModule);
    var shippingMainElements = document.querySelectorAll('.shipping-main');
    for (var i = 0; i < shippingMainElements.length; i++) {
      if (shippingMainElements[i].id !== 'blockShipping' + shipping) {
        shippingMainElements[i].remove();
      }
    }
    const regionField = document.getElementById(shipping + '-region')
    regionField.addEventListener('change', () => {
      changeRegion(shipping)
    })
    const cityField = document.getElementById(shipping + '-cities')
    cityField.addEventListener('change', () => {
      changeCity(shipping)
    })
    // загружаем регионы
    loadRegions(shipping);
  }

  Fancybox.show(
    [
      {
        src: '#blockShipping' + shipping
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

function loadRegions(shipping) {
  var country = document.getElementById('country').value;
  if (shipping == 'ozon') {
    var link = myData.getVar('route_getRegions');
  } else if (shipping == 'boxberry') {
    var link = myData.getVar('route_getBoxberryRegions');
  } else if (shipping == 'cdek') {
    var link = myData.getVar('route_getCdekRegions');
  } else if (shipping == 'cdek_courier') {
    var link = myData.getVar('route_getCdekCourierRegions');
  }

  if (document.getElementById(shipping + '-region')) {
    window.ajax.get(link, {country: country}, (response) => {
      if (response != '') {
        document.getElementById('regionsLoading').remove();
        if (regionChoise !== null) {
          regionChoise.passedElement.element.removeEventListener('showDropdown', window.checkChoisesDropdown);
          regionChoise.destroy();
        }
        var option = document.createElement('option');
        option.innerText = 'Выбрать...';
        option.setAttribute('disabled', true);
        option.setAttribute('selected', true);
        document.getElementById(shipping + '-region').append(option);
        for (var i = 0; i < response.length; i++) {
          var option = document.createElement('option');
          if (shipping == 'boxberry') {
            option.value = response[i].id;
            option.innerText = response[i].name;
          } else if (shipping == 'cdek' || shipping == 'cdek_courier') {
            option.value = response[i].id;
            option.innerText = response[i].region;
          }
          document.getElementById(shipping + '-region').append(option);
        }
        regionChoise = new Choices(document.getElementById(shipping + '-region'), {
          removeItemButton: true,
          shouldSort: false,
          noChoicesText: 'Пусто',
          itemSelectText: 'Выбрать'
        })
        regionChoise.passedElement.element.addEventListener('showDropdown', window.checkChoisesDropdown);
      } else {
        alert('Ошибка загрузки доступных регионов');
      }
    })
  }

}
function changeRegion(shipping) {
  var blockShippingOzon = document.querySelector('#blockShipping' + shipping);
  var region = blockShippingOzon.querySelector('select[name="region"]').value;
  if (shipping == 'boxberry') {
    var link = myData.getVar('route_getBoxberryCities');
  } else if (shipping == 'cdek') {
    var link = myData.getVar('route_getCdekCities');
  } else if (shipping == 'cdek_courier') {
    var link = myData.getVar('route_getCdekCourierCities');
  }
  window.ajax.get(link, {region: region}, (response) => {
    if (response) {
      if (cityChoise !== null){
        cityChoise.passedElement.element.removeEventListener('showDropdown', window.checkChoisesDropdown);
        cityChoise.destroy();
      }
      document.getElementById(shipping + '-cities').innerHTML = '';
      for (var i = 0; i < response.length; i++) {
        var option = document.createElement('option');
        if (shipping == 'boxberry') {
          option.value = response[i].id;
          option.innerText = response[i].Name;
        } else if (shipping == 'cdek' || shipping == 'cdek_courier') {
          if (shipping == 'cdek'){
            option.value = response[i].id;
          }else{
            option.value = response[i].code;
          }
          if (response[i].sub_region) {
            option.innerText = response[i].city + ' (' + response[i].sub_region + ')';
          } else {
            option.innerText = response[i].city;
          }
        }
        document.getElementById(shipping + '-cities').append(option);
      }
      cityChoise = new Choices(document.getElementById(shipping + '-cities'), {
        removeItemButton: true,
        shouldSort: false,
        noChoicesText: 'Пусто',
        itemSelectText: 'Выбрать'
      })
      cityChoise.passedElement.element.addEventListener('showDropdown', window.checkChoisesDropdown);
      var fieldCities = document.getElementById('field-cities');
      if (fieldCities) {
        fieldCities.style.display = 'block';
      }

      var element = document.getElementById(shipping + '-cities');

      if (element) {
        element.style.display = 'block';

        var event = new Event('change', {
          'bubbles': true,
          'cancelable': true
        });
        element.dispatchEvent(event);
      }
    } else {
      var element = document.getElementById('field-cities');
      if (element) {
        element.parentNode.removeChild(element);
      }
      changeCity(shipping, region, null);
    }
  })
}

function changeCity(shipping) {

  var blockShippingOzon = document.querySelector('#blockShipping' + shipping);
  var region = blockShippingOzon.querySelector('select[name="region"]').value;
  var city = blockShippingOzon.querySelector('select[name="city"]').value;
  if (shipping == 'boxberry') {
    var link = myData.getVar('route_getBoxberryPvz');
  } else if (shipping == 'cdek') {
    var link = myData.getVar('route_getCdekPvz');
  }
  if (shipping != 'cdek_courier') {
    window.ajax.get(link, {region: region, city: city}, (response) => {
      if (response) {
        if (pvzChoise !== null){
          pvzChoise.passedElement.element.removeEventListener('showDropdown', window.checkChoisesDropdown);
          pvzChoise.destroy();
        }
        var element = document.getElementById(shipping + '-shipping');
        if (element) {
          element.innerHTML = response.html;
        }
        pvzChoise = new Choices(document.getElementById(shipping + '-pvz'), {
          removeItemButton: true,
          shouldSort: false,
          noChoicesText: 'Пусто',
          itemSelectText: 'Выбрать'
        })
        pvzChoise.passedElement.element.addEventListener('showDropdown', window.checkChoisesDropdown);
        const pvzField = document.getElementById(shipping + '-pvz')
        pvzField.addEventListener('change', (event) => {
          map_deliveryVariantIdChange(event.target)
        })
        setTimeout(function(){
          map_init(response.map);
          map_show_modal();
          //
          document.getElementById('blockShipping' + shipping).addEventListener('click', function(event) {
            if (event.target.classList.contains('ll_set_point')) {
              const pvzCode = event.target.dataset.pvzCode
              const pvzAddress = event.target.dataset.pvzAddress
              let cityCode
              if(event.target.dataset.cityCode){
                cityCode = event.target.dataset.cityCode
              }
              let city
              if(event.target.dataset.city){
                city = event.target.dataset.city
              }
              map_deliveryVariantId(pvzCode, pvzAddress, cityCode, city)
            }
          });
        },300);

      } else {
        alert('Ошибка. Перезагрузите страницу и попробуйте снова');
      }
    })
  } else {
    var html = '<div id="'+shipping+'-address-form"><div class="mb-6">';
    html += '  <input type="text" name="' + shipping + '-street" placeholder="Улица" class="block w-full border border-myGray bg-transparent placeholder-myGray m-subtitle-2 d-subtitle-2 py-2.5 sm:py-1.75 px-3 leading-none focus:ring-0 focus:border-black" value="" id="' + shipping + '-street">';
    html += '</div>';
    html += '<div class="flex mb-6 -mx-2"><div class="w-1/2 px-2"><div class="form-group">';
    html += '  <input type="text" name="' + shipping + '-house" placeholder="Дом" class="block w-full border border-myGray bg-transparent placeholder-myGray m-subtitle-2 d-subtitle-2 py-2.5 sm:py-1.75 px-3 leading-none focus:ring-0 focus:border-black" value="" id="' + shipping + '-house">';
    html += '</div></div>';
    html += '<div class="w-1/2 px-2"><div class="form-group">';
    html += '  <input type="text" name="' + shipping + '-flat" placeholder="Квартира" class="block w-full border border-myGray bg-transparent placeholder-myGray m-subtitle-2 d-subtitle-2 py-2.5 sm:py-1.75 px-3 leading-none focus:ring-0 focus:border-black" value="" id="' + shipping + '-flat">';
    html += '</div></div></div>';

    html += '<div style="margin-top: 40px;" id="' + shipping + '-btn"><button type="button" class="h-11 flex items-center justify-center px-3 md:px-4 px-7 border border-black text-xl leading-none font-medium md:h-14 md:w-full md:max-w-[285px] mx-auto" id="' + shipping + '-calculate">Рассчитать стоимость</button></div></div>';
    // html += '<div class="form-check">\n' +
    //   '                      <input class="form-check-input" type="checkbox" name="' + shipping + '-express" id="' + shipping + '-express" value="1" required>\n' +
    //   '                      <label class="form-check-label" for="' + shipping + '-express">Экспресс доставка</label>\n' +
    //   '                    </div>';
    if(!document.getElementById(+shipping+'-address-form')){
      var element = document.getElementById(shipping + '-shipping');
      if (element) {
        element.innerHTML = html;
        let button = document.getElementById(shipping + '-calculate')
        if (button){
          button.addEventListener('click', (event) => {
            event.preventDefault()
            cdekCourierCalculate(shipping)
          })
        }
      }
    }

  }
}

const thisShippingMethod = () => {
  var shippingMethod = document.querySelector('input[name="shipping-code"]:checked');
  var shipping
  if (shippingMethod){
    shipping = shippingMethod.value;
  }
  return shipping
}
function map_deliveryVariantId(id) {
  var shipping = thisShippingMethod()

  if (shipping) {
    calculateModule(shipping, id);

    // Установить выбранным элемент с id, соответствующим переменной id
    var selectElement = document.getElementById(id);
    if (selectElement) {
      selectElement.selected = true;
    }
  }
};

function map_deliveryVariantIdChange(elem, options = {}) {
  var shipping = thisShippingMethod()
  var id = elem.options[elem.selectedIndex].value;

  calculateModule(shipping, id);
};

function calculateModule(shipping, id) {
  if (shipping == 'boxberry') {
    var link = myData.getVar('route_calculateBoxberry');
    var params = {code: id};
  } else if (shipping == 'cdek') {
    var link = myData.getVar('route_calculateCdek');
    var params = {code: id};
  }
  document.getElementById('loader').classList.remove('hidden')
  window.ajax.get(link, params, (response) => {
    if (response) {
      var price;
      if (shipping == 'boxberry') {
        price = response.shippingPrice;
      } else if (shipping == 'cdek') {
        price = response.shippingPrice;
      }
      // обновляем системные данные для заказа
      document.getElementById('shipping-price').value = price
      document.getElementById(shipping).checked = true;
      document.getElementById(shipping + '-pvz-id').value = id;
      document.getElementById(shipping + '-pvz-address').value = response.address;

      window.getTotal()
      // визуализируем выбранный адрес и цену

      Fancybox.close();
    } else {
      alert('Ошибка рассчета стоимости доставки. Выберите другой способ доставки или попробуйте позже.');
    }
    document.getElementById('loader').classList.add('hidden')
  })
}

function cdekCourierCalculate(shipping){
  var regionElement = document.getElementById(shipping + '-region');
  var citiesElement = document.getElementById(shipping + '-cities');
  var streetElement = document.getElementById(shipping + '-street');
  var houseElement = document.getElementById(shipping + '-house');
  var flatElement = document.getElementById(shipping + '-flat');

// Получаем выбранные значения
  var region = regionElement.options[regionElement.selectedIndex].text;
  var region_code = regionElement.value;
  var city = citiesElement.options[citiesElement.selectedIndex].text;
  var city_code = citiesElement.value;
  var street = streetElement.value;
  var house = houseElement.value;
  var flat = flatElement.value;

  if (!region || !region_code || !city || !city_code || !street || !house) {
    window.alert('Укажите полный адрес');
    return false;
  }
  var address = region + ', ' + city + ', ' + street + ', д. ' + house;
  if (flat) {
    address += ', кв. ' + flat;
  }
  var link = myData.getVar('route_calculateCdek');
  var params = {city: city_code, shipping: 'cdek_courier'};
  document.getElementById('loader').classList.remove('hidden')
  window.ajax.get(link, params, (response) => {
    if(response){
      var price = response.shippingPrice;

      document.getElementById('shipping-price').value = price
      document.getElementById(shipping).checked = true;
      document.getElementById(shipping + '-form-region').value = region_code;
      document.getElementById(shipping + '-form-city').value = city_code;
      document.getElementById(shipping + '-form-street').value = street;
      document.getElementById(shipping + '-form-house').value = house;
      document.getElementById(shipping + '-form-flat').value = flat;
      document.getElementById(shipping + '-form-address').value = address;

      window.getTotal();

      Fancybox.close();
    } else {
      alert('Ошибка рассчета стоимости доставки. Выберите другой способ доставки или попробуйте позже.');
    }
    document.getElementById('loader').classList.add('hidden')
  })
}


function resetShipping(){
  document.getElementById('shipping-price').value = 0

  window.getTotal();
  var shipping_field = document.querySelector('input[name="shipping-code"]:checked')
  var shipping_price_field = document.getElementById('shipping-price-field')
  var shipping_price_text = document.getElementById('shipping-price-text')
  var shipping_price_info = document.getElementById('shipping-price-info')
  var shipping_address_info = document.getElementById('shipping-address-info')

  // shipping_field.checked = false
  shipping_price_text.textContent = 'Доставка'
  shipping_price_info.innerHTML = 0
  shipping_price_field.classList.add('hidden')
  shipping_address_info.textContent = ''
  shipping_address_info.classList.add('hidden')
}

window.getTotal = function(){
  var shippingPrice = Number(document.getElementById('shipping-price').value);
  var cartTotal = Number(document.getElementById('cart-total').value);

  var shipping_method_field = document.querySelector('input[name="shipping-code"]:checked')
  var shipping = thisShippingMethod()

  var shipping_price_field = document.getElementById('shipping-price-field')
  var shipping_price_text = document.getElementById('shipping-price-text')
  var shipping_price_info = document.getElementById('shipping-price-info')
  var shipping_address_info = document.getElementById('shipping-address-info')
  if(shipping){
    if(shippingPrice == 0 && ['boxberry', 'cdek', 'pochta'].includes(shipping)){
      shipping_price_text.textContent = 'Доставка'
      shipping_price_info.innerHTML = window.formatPrice(shippingPrice)
      shipping_price_field.classList.add('hidden')
    }else{
      var shipping_name = document.querySelector(`label[for="${shipping}"]`).textContent
      shipping_price_text.textContent = shipping_name
      shipping_price_info.innerHTML = window.formatPrice(shippingPrice)
      shipping_price_field.classList.remove('hidden')


      if(shipping == 'cdek'){
        var address = document.getElementById('cdek-pvz-address')
        shipping_address_info.innerText = 'Доставка '+shipping_name+': '+address.value
        shipping_address_info.classList.remove('hidden')
      }else if(shipping == 'cdek_courier'){
        var address = document.getElementById('cdek_courier-form-address')
        shipping_address_info.innerText = 'Доставка '+shipping_name+': '+address.value
        shipping_address_info.classList.remove('hidden')
      }else if(shipping == 'boxberry'){
        var address = document.getElementById('boxberry-pvz-address')
        shipping_address_info.innerText = 'Доставка '+shipping_name+': '+address.value
        shipping_address_info.classList.remove('hidden')
      }
    }
  }
  // promocode
  const orderTotalInfo = document.getElementById('order-total-info')
  // удаляем расчет скидки промокодов и сертификатов
  if(document.getElementById('discount-info')){
    document.getElementById('discount-info').remove()
  }
  const discountField = document.querySelector('input[name="discount"]')
  var discount = 0 // общая скидка
  var discounted = 0 // часть оставшейся скидки, для расчетов стомости доставки и товаров
  var getTotalPrice = cartTotal //
  if(discountField){
    discount = Number(discountField.value);
  }
  if(discount > 0){
    const promoField = document.querySelector('input[name="promo"]:checked')
    if(promoField.value == 'voucher'){
      // считаем стоимость доставки
      if (shippingPrice > discount) {
        discounted = discount
        shippingPrice = shippingPrice - discount
      } else { //
        discounted = shippingPrice
        shippingPrice = 0
      }
      discount = discount - discounted // вычитаем из скидки потраченную на стоимость доставки
      if (cartTotal > discount) { // если сумма в корзине больше скидки
        getTotalPrice = getTotalPrice - discount;
        discounted += discount;
      } else {
        discounted += getTotalPrice - 1;
        getTotalPrice = 1;
      }
      discount = discounted
      const discountInfo = document.createElement('tr')
      discountInfo.id = 'discount-info'
      discountInfo.innerHTML = `
        <td class="text-left border p-2">Подарочный сертификат</td>
        <td class="border p-2">-${formatPrice(discount)}</td>
      `
      orderTotalInfo.parentNode.insertBefore(discountInfo, orderTotalInfo);
    }else if(promoField.value == 'promocode'){
      if (getTotalPrice > discount) {
        getTotalPrice = getTotalPrice - discount;
      } else {
        discount = getTotalPrice - 1;
        getTotalPrice = 1;
      }
      const discountInfo = document.createElement('tr')
      discountInfo.id = 'discount-info'
      discountInfo.innerHTML = `
        <td class="text-left border p-2">Промокод</td>
        <td class="border p-2">-${formatPrice(discount)}</td>
      `
      orderTotalInfo.parentNode.insertBefore(discountInfo, orderTotalInfo);
    }
    //
    // if ($('[name="promo"]:checked').val() == 'voucher') {
    //   if (shippingPrice > discount) {
    //     discounted = discount;
    //     shippingPrice = Number(shippingPrice) - Number(discount);
    //   } else {
    //     discounted = shippingPrice;
    //     shippingPrice = 0;
    //   }
    //   discount = discount - discounted;
    //   if (price > discount) {
    //     getTotalPrice = getTotalPrice - discount;
    //     discounted = Number(discounted) + Number(discount);
    //   } else {
    //     discounted = discounted + getTotalPrice - 1;
    //     getTotalPrice = 1;
    //   }
    //   discount = discounted;
    //   $('#voucher-tr').remove();
    //   $('#promocode-tr').remove();
    //   $('#total-tr').before('<tr id="voucher-tr"><td width="60%">Подарочный сертификат</td><td id="delprice">-]</td></tr>');
    //   $('#discount-text').remove();
    // } else {
    //   if (getTotalPrice > discount) {
    //     getTotalPrice = getTotalPrice - discount;
    //   } else {
    //     discount = getTotalPrice - 1;
    //     getTotalPrice = 1;
    //   }
    //   $('#voucher-tr').remove();
    //   $('#promocode-tr').remove();
    //   $('#total-tr').before('<tr id="promocode-tr"><td width="60%">Промокод</td><td id="delprice">-' + discount + '</td></tr>');
    // }
  }

  document.getElementById('cart-total-info').innerHTML = window.formatPrice(cartTotal)
  document.getElementById('order-amount').innerHTML = window.formatPrice(getTotalPrice + shippingPrice)
}

function pochtaPriceRussia(postcode) {
  document.getElementById('loader').classList.remove('hidden')
  window.ajax.post(myData.getVar('route_calculatePochta'), {type: 'russia', to: postcode}, (response) => {
    if (response) {
      var price;
      if (response.price > 0) {
        price = Number(response.price.toFixed(0));
      } else {
        price = Number(response.price);
      }
      // обновляем системные данные для заказа
      document.getElementById('shipping-price').value = price
      document.getElementById('pochta').checked = true

      window.getTotal()
      // визуализируем выбранный адрес и цену

      Fancybox.close();
    } else {
      alert('Ошибка рассчета стоимости доставки. Выберите другой способ доставки или попробуйте позже.');
    }
    document.getElementById('loader').classList.add('hidden')
  })
}

const pochtaPostcode = document.getElementById('pochta-postcode')
if(pochtaPostcode){
  pochtaPostcode.addEventListener('change', (event) => {
    const field = event.currentTarget
    const postcode = field.value
    if (postcode){
      pochtaPriceRussia(postcode)
    }
  })
}

const openPickups = document.getElementById('open-pickups')
if(openPickups){
  openPickups.addEventListener('click', ()=>{
    const pickups = document.getElementById('pickups-container')
    pickups.classList.toggle('hidden')
  })
}
// promocode
function promocodeHandler(event){
  var field = event.currentTarget
  var code = field.value

  if(code){
    if(field.id == 'voucher'){
      window.checkVoucher(code)
    }else if(field.id == 'promocode'){
      window.checkPromocode(code)
    }
  }else{
    getTotal()
  }
}
const voucherField = document.getElementById('voucher')
if(voucherField){
  voucherField.addEventListener('change', promocodeHandler)
}
const promocodeField = document.getElementById('promocode')
if(promocodeField){
  promocodeField.addEventListener('change', promocodeHandler)
}


window.checkVoucher = function(voucher) {
  const route = myData.getVar('route_checkVoucher')
  const params = {
    voucher: voucher,
    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
  }

  window.ajax.post(route, params, (response) => {
    if (response) {
      var voucherMessage = document.getElementById('voucher-message')
      if(voucherMessage){
        voucherMessage.remove()
      }
      if(response.voucher_discount && response.voucher_discount > 0){
        var cartTotal = Number(document.getElementById('cart-total').value)
        var shippingPrice = Number(document.getElementById('shipping-price').value)
        var discount
        if (shippingPrice + cartTotal > response.voucher_discount) {
          discount = response.voucher_discount;
        } else {
          discount = shippingPrice + cartTotal - 1;
        }
        if (!document.querySelector('input[name="discount"]')) {
          let orderElement = document.getElementById('order');

          let newInput = document.createElement('input');
          newInput.type = 'hidden';
          newInput.name = 'discount';
          newInput.id = 'items-discount';
          newInput.value = discount;
          orderElement.prepend(newInput);
        }
        document.getElementById('voucher').style.color = 'green'
        getTotal()
      }
      if(response.error){
        if (document.querySelector('input[name="discount"]')) {
          document.querySelector('input[name="discount"]').remove()
        }
        if (document.getElementById('voucher-message')) {
          document.getElementById('voucher-message').remove()
        }
        if (document.getElementById('promocode-message')) {
          document.getElementById('promocode-message').remove()
        }

        voucherMessage = document.createElement('div')
        voucherMessage.id = 'voucher-message'
        voucherMessage.style.color = 'red'
        voucherMessage.innerHTML = response.error
        const voucherField = document.getElementById('box-field-voucher')
        voucherField.parentNode.appendChild(voucherMessage);
        voucherField.querySelector('input').style.color = ''
        getTotal();
      }
    } else {
      alert('Ошибка, попробуйте позже');
    }
  })
}

window.checkPromocode = function(promocode) {
  var total_for_discount = Number(document.getElementById('total_for_discount').value)
  if (total_for_discount > 0) {
    const route = myData.getVar('route_checkPromocode')
    const params = {
      promocode: promocode,
      _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
    window.ajax.post(route, params, (response) => {
      console.log(response);
      if (response) {
        var promocodeMessage = document.getElementById('promocode-message')
        if(promocodeMessage){
          promocodeMessage.remove()
        }
        if(response.promocode_discount && response.promocode_discount > 0){
          var discount
          total_for_discount = response.total_for_discount
          document.getElementById('total_for_discount').value = total_for_discount
          if (total_for_discount > response.promocode_discount) {
            discount = response.promocode_discount;
          } else {
            if (total_for_discount < response.promocode_discount) {
              discount = total_for_discount;
            } else {
              discount = total_for_discount - 1;
            }
          }

          if (document.querySelector('input[name="discount"]')) {
            document.querySelector('input[name="discount"]').remove()
          }
          let orderElement = document.getElementById('order');

          let newInput = document.createElement('input');
          newInput.type = 'hidden';
          newInput.name = 'discount';
          newInput.id = 'items-discount';
          newInput.value = discount;
          orderElement.prepend(newInput);

          document.getElementById('promocode').style.color = 'green'
          getTotal()
        }

        if(response.error){
          if (document.querySelector('input[name="discount"]')) {
            document.querySelector('input[name="discount"]').remove()
          }
          if (document.getElementById('voucher-message')) {
            document.getElementById('voucher-message').remove()
          }
          if (document.getElementById('promocode-message')) {
            document.getElementById('promocode-message').remove()
          }

          promocodeMessage = document.createElement('div')
          promocodeMessage.id = 'promocode-message'
          promocodeMessage.style.color = 'red'
          promocodeMessage.innerHTML = response.error
          const promocodeField = document.getElementById('box-field-promocode')
          promocodeField.parentNode.appendChild(promocodeMessage);
          promocodeField.querySelector('input').style.color = ''
          getTotal();
        }
      } else {
        alert('Ошибка, попробуйте позже');
      }
    });
  }
}

document.querySelectorAll('[name="promo"]').forEach(function(element) {
  element.addEventListener('change', function() {
    let this_val = this.value;

    if (document.querySelector('input[name="discount"]')) {
      document.querySelector('input[name="discount"]').remove()
    }
    if (document.getElementById('voucher-message')) {
      document.getElementById('voucher-message').remove()
    }
    if (document.getElementById('promocode-message')) {
      document.getElementById('promocode-message').remove()
    }
    // removeElementById('voucher-tr');
    // removeElementById('promocode-tr');
    // removeElementById('discount-text');
    // removeElementById('items-discount');
    // removeElementById('voucher-tr');

    getTotal(); // предполагается, что этот метод определен в другом месте

    clearInputValueById('box-field-voucher');
    clearInputValueById('box-field-promocode');

    if (this_val === 'promocode') {
      hideElementById('box-field-voucher');
      showElementById('box-field-promocode');
    } else if (this_val === 'voucher') {
      showElementById('box-field-voucher');
      hideElementById('box-field-promocode');
    }
  });
});

function removeElementById(id) {
  let element = document.getElementById(id);
  if (element) {
    element.remove();
  }
}

function clearInputValueById(id) {
  let element = document.getElementById(id);
  if (element) {
    element.querySelector('input[type="text"]').value = '';
  }
}

function hideElementById(id) {
  let element = document.getElementById(id);
  if (element) {
    element.style.display = 'none';
  }
}

function showElementById(id) {
  let element = document.getElementById(id);
  if (element) {
    element.style.display = 'block';
  }
}
// слушаем корзину

let tableCart = document.getElementById('table-cart');
if(tableCart){
  tableCart.addEventListener('updateCart', function(event) {
    const promocodeField = document.getElementById('promocode')
    const voucherField = document.getElementById('voucher')
    if(promocodeField && promocodeField.value){
      window.checkPromocode(promocodeField.value)
    }else if(voucherField && voucherField.value){
      window.checkVoucher(voucherField.value)
    }
  });
}

