
function initTinymce(element){
  let toolbar = 'clearstyles | fontfamily bold italic strikethrough letter-case | align bullist forecolor | code'
  if (element.dataset.toolbar){
    toolbar = element.dataset.toolbar
  }
  var protocol = window.location.protocol;
  var hostname = window.location.hostname;
  var port = window.location.port;

  var domain = protocol + "//" + hostname + (port ? ':' + port : '');
  let tynymce_params = {
    //newline_behavior: 'invert',
    selector: '#' + element.id,
    height: 200,
    setup: function (editor) {
      editor.ui.registry.addButton('clearstyles', {
        icon: 'clear-formatting',
        onAction: function() {
          let content = editor.getContent();
          let div = document.createElement('div');
          div.innerHTML = content;
          // Удаление стилей и классов
          let elements = div.querySelectorAll('*');
          for (let el of elements) {
            el.removeAttribute('style');
            el.removeAttribute('class');
          }
          editor.setContent(div.innerHTML);
        }
      });
      editor.ui.registry.addButton('arrowBottom', {
        icon: 'add-arrow-bottom',
        onAction: function () {
          // HTML-код, который будет вставлен
          var htmlContent = `
                    <p>
                        <img src="${domain}/img/icons/arrb.svg" width="20" height="20" />
                    </p>`;

          // Вставка HTML-кода в текущую позицию курсора
          editor.insertContent(htmlContent);
        }
      });
      editor.ui.registry.addButton('addSpace', {
        text: '_',
        onAction: function () {
          // HTML-код, который будет вставлен
          var htmlContent = `<hr style="height: 1em; opacity:0;"/>`;

          // Вставка HTML-кода в текущую позицию курсора
          editor.insertContent(htmlContent);
        }
      });
      editor.ui.registry.addButton('addHalfSpace', {
        text: '/_',
        onAction: function () {
          // HTML-код, который будет вставлен
          var htmlContent = `<hr style="height: 1px; opacity:0;"/>`;

          // Вставка HTML-кода в текущую позицию курсора
          editor.insertContent(htmlContent);
        }
      });
      editor.ui.registry.addIcon('add-arrow-bottom', `<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-narrow-down" width="24" height="24" viewBox="0 0 24 24" stroke-width="1" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
          <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
          <path d="M12 5l0 14" />
          <path d="M16 15l-4 4" />
          <path d="M8 15l4 4" />
        </svg>`);

      editor.ui.registry.addIcon('clear-formatting', '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clear-formatting" width="24" height="24" viewBox="0 0 24 24" stroke-width="1" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">\n' +
        '  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>\n' +
        '  <path d="M17 15l4 4m0 -4l-4 4" />\n' +
        '  <path d="M7 6v-1h11v1" />\n' +
        '  <path d="M7 19l4 0" />\n' +
        '  <path d="M13 5l-4 14" />\n' +
        '</svg>');

      editor.ui.registry.addMenuButton('letter-case', {
        text: 'Aa',
        fetch: function (callback) {
          var items = [
            {
              type: 'menuitem',
              text: 'Верхний регистр',
              onAction: function () {
                window.toUpperCase(editor);
              }
            },
            {
              type: 'menuitem',
              text: 'Нижний регистр',
              onAction: function () {
                window.toLowerCase(editor);
              }
            }
          ];
          callback(items);
        }
      });

      editor.ui.registry.addMenuButton('headlines', {
        text: 'H',
        fetch: function (callback) {
          var items = [
            {
              type: 'menuitem',
              text: 'Заголовок',
              onAction: function () {
                editor.insertContent('<h3 class="uppercase d-headline-4 text-lg font-semibold" style="text-align:center;font-weight:600;text-transform:uppercase;">Заголовок</h3>');
              }
            },
            {
              type: 'menuitem',
              text: 'Заголовок с подчеркиванием',
              onAction: function () {
                editor.insertContent('<div><h3 class="uppercase d-headline-4 text-lg font-semibold" style="text-align:center;font-weight:600;text-transform:uppercase;">Заголовок</h3><div style="height:0px;margin-top:8px;margin-left:auto;margin-right:auto;border-bottom:1px solid white;opacity:0.24;width:100%;max-width:200px;"></div></div>');
              }
            },
          ];
          callback(items);
        }
      });

      // editor.ui.registry.addButton('uppercase', {
      //   text: 'A↑',
      //   tooltip: 'Uppercase',
      //   onAction: function () {
      //     window.toUpperCase(editor);
      //   }
      // });
      //
      // editor.ui.registry.addButton('lowercase', {
      //   text: 'a↓',
      //   tooltip: 'Lowercase',
      //   onAction: function () {
      //     window.toLowerCase(editor);
      //   }
      // });
    },
    menubar: false,
    relative_urls: false,
    plugins: 'code lists link',
    toolbar: toolbar,
    font_family_formats: "Cormorant Garamond=Cormorant Garamond, serif;Cormorant Infant=Cormorant Infant, serif;Playfair Display=Playfair Display, sans-serif;",
    toolbar_mode: 'floating',
    color_map: [
      "000000", "Черный",
      "FFFFFF", "Белый",
      "B1908E", "Коричневый",
      "919583", "Зеленый",
    ],
    color_cols: 4
  }
  if (element.dataset.style_formats){
    tynymce_params.style_formats = [
      {
        title: 'Цветной блок',
        block: 'div', // или span, в зависимости от того, какой элемент вы хотите использовать
        classes: 'highlight-content',
        wrapper: true
      }
    ]
  }
  let content_style = `
  @import url(\'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Cormorant+Infant:wght@400;600&family=Montserrat:wght@400;500&family=Playfair+Display:ital@1&display=swap\');
  body {font-family: \'Cormorant Garamond\', serif;}
  h3 {margin:0}
  p {margin:0;margin-bottom:8px}
  .highlight-content {padding: 4px;background: rgba(255,255,255,.16);}
  `

  if (element.dataset.background){
    content_style += "body { background-color: "+element.dataset.background+"; }"
  }
  if (element.dataset.color){
    content_style += "body { color: "+element.dataset.color+"; }"
  }
  tynymce_params.content_style = content_style
  if (element.dataset.toolbar && element.dataset.toolbar.includes('arrowBottom')){
    tynymce_params.valid_elements = '*[*]'
    tynymce_params.extended_valid_elements = 'svg[*],path[*]'
  }
  tinymce.init(tynymce_params);
}
function playEditor() {
  var elems = document.querySelectorAll('.tinymce-textarea');
  elems.forEach((field) => {
    if(field.disabled){
      return false;
    }
    initTinymce(field)
  });
}
playEditor();

// tabs

const tabs = document.querySelectorAll('[role="tab"]');
const tabList = document.querySelector('[role="tablist"]');
const tabContent = document.getElementById('tab-content');

function activateTab(tab) {
  // Deactivate all tabs
  tabs.forEach((t) => {
    t.setAttribute('aria-selected', 'false');
    t.classList.remove('tab-btn-active');
    t.classList.add('text-gray-500');
  });

  // Activate the clicked tab
  tab.setAttribute('aria-selected', 'true');
  tab.classList.remove('text-gray-500');
  tab.classList.add('tab-btn-active');

  // Hide all tab content
  const tabPanels = tabContent.querySelectorAll('[role="tabpanel"]');
  tabPanels.forEach((panel) => {
    panel.hidden = true;
  });

  // Show the content panel for the clicked tab
  const tabPanel = document.getElementById(tab.getAttribute('aria-controls'));
  tabPanel.hidden = false;
}

if (tabs.length){
  // Add event listeners to each tab
  tabs.forEach((tab) => {
    tab.addEventListener('click', () => {
      activateTab(tab);

      // Update URL
      const tabId = tab.getAttribute('id');
      history.pushState({}, '', `#${tabId}`);
    });
  });

  // Check if there's a hash in the URL that matches a tab id
  if (location.hash && document.querySelector(location.hash)) {
    const tabFromURL = document.querySelector(location.hash);
    activateTab(tabFromURL);
  } else {
    // Set up initial tab state
    if(tabList.querySelector('[aria-selected="true"]')) {
      tabList.querySelector('[aria-selected="true"]').click();
    }else{
      tabList.querySelector('[role="tab"]:first-child').click();
    }
  }
}

// Handle the popstate event for the browser's back/forward buttons
window.addEventListener('popstate', function() {
  if (location.hash && document.querySelector(location.hash)) {
    const tabFromURL = document.querySelector(location.hash);
    activateTab(tabFromURL);
  }
});
// tabs end
// только цифры длф полей .numeric-field
window.numericFieldsListener()


// end
// dropdown-menu
const dropdownButtons = document.querySelectorAll('.dropdown-button');
dropdownButtons.forEach((button) => {
  const dropdownMenu = document.querySelector('div[aria-labelledby="'+button.id+'"]');
  button.addEventListener('click', (event) => {
    const openedMenu = document.querySelectorAll('div[role="menu"]:not(.hidden)');
    openedMenu.forEach((menu) => {
      if (dropdownMenu != menu) {
        menu.classList.toggle('hidden');
      }
    })
    if(dropdownMenu.classList.contains('hidden')){

    }
    dropdownMenu.classList.remove('bottom-full')
    if(getDistanceFromBottom(dropdownMenu)!==null&&getDistanceFromBottom(dropdownMenu)<0){
      dropdownMenu.classList.add('bottom-full')
    }
    dropdownMenu.classList.toggle('hidden');
  });
  dropdownMenu.addEventListener('click', (event) => {
    if (!event.target.closest('.py-1 a')) return;
    dropdownMenu.classList.add('hidden');
  });
  document.addEventListener('click', (event) => {
    if (!event.target.closest('.dropdown-box')) {
      dropdownMenu.classList.add('hidden');
    }
  });
});

function getDistanceFromBottom(divElement) {
  if (!divElement.classList.contains('hidden')){
    return null;
  }
  // Временно делаем блок видимым, чтобы получить его положение и размеры
  divElement.classList.remove('hidden');

  // Получаем положение и размеры блока
  const rect = divElement.getBoundingClientRect();

  // Возвращаем стиль блока к исходному
  divElement.classList.add('hidden');
  var height = window.innerHeight || document.documentElement.clientHeight;

  if(divElement.closest('table')&&divElement.closest('table').closest('.relative')){
    const parentElement = divElement.closest('table').closest('.relative');
    const parentRect = parentElement.getBoundingClientRect();
    height = parentRect.bottom;
  }
  // Вычисляем расстояние от нижней части блока до нижнего края экрана
  const distanceFromBottom = height - rect.bottom;

  return distanceFromBottom;
}

// end
// datepicker
function stringToDate(dateString){
  let dateTimeRegex = /^\d{1,2}\.\d{1,2}\.\d{4}( \d{1,2}:\d{1,2})?$/;
  if(dateTimeRegex.test(dateString)) {
    let parts = dateString.split(' ');
    let dateParts = parts[0].split('.');
    let timeParts = (parts[1] !== undefined) ? parts[1].split(':') : [0, 0];

    return new Date(dateParts[2], Number(dateParts[1]) - 1, dateParts[0], timeParts[0], timeParts[1]);
  }else{
    return null;
  }
}
function datepickerInit(elem, options = {}) {
  const datepickerParams = {
    ...options,
    timepicker: true,
    position({$datepicker, $target, $pointer}) {
      let coords = $target.getBoundingClientRect(),
        dpHeight = $datepicker.clientHeight,
        dpWidth = $datepicker.clientWidth;

      let top = coords.y + coords.height / 2 + window.scrollY - dpHeight / 2;
      let left = coords.x + coords.width / 2 - dpWidth / 2;

      $datepicker.style.left = `${left}px`;
      $datepicker.style.top = `${top}px`;

      $pointer.style.display = 'none';
    }
  };

  if (elem.value != ''){
    datepickerParams.selectedDates = [stringToDate(elem.value)];
  }
  if (elem.dataset.mindate !== 'false'){
    datepickerParams.minDate = new Date();
  }
  if (elem.dataset.timepicker === '0'){
    datepickerParams.timepicker = false;
  }
  new AirDatepicker(elem, datepickerParams)
}
var datepickerFields = document.querySelectorAll('input.datepicker');
datepickerFields.forEach((field)=>{
  datepickerInit(field)
})
// end

async function copyTextToClipboardAsync(text) {
  try {
    await navigator.clipboard.writeText(text);
    alert('Ссылка скопирована в буфер обмена!');
  } catch (err) {
    console.error('Не удалось скопировать ссылку: ', err);
  }
}
function copyItemsListener() {
  const copyItems = document.querySelectorAll('.copy-to-clipboard');
  copyItems.forEach((item)=>{
    item.addEventListener('click', (event) => {
      event.preventDefault();
      const text = event.target.dataset.copy;
      copyTextToClipboardAsync(text);
    })
  })
}
copyItemsListener()

const customSearch = function(value, item) {
  // Проверяем совпадения по обычному тексту
  let isTextMatch = item.label.toLowerCase().includes(value.toLowerCase());

  // Проверяем совпадения по ключевым словам
  let keywordAttribute = item.element.getAttribute('data-keywords');
  let isKeywordMatch = keywordAttribute ? keywordAttribute.toLowerCase().includes(value.toLowerCase()) : false;

  return isTextMatch || isKeywordMatch;
};
function multipleSelect(){
  const multipleSelects = document.querySelectorAll('select.multipleSelect');
  multipleSelects.forEach((select)=>{
    const options = {
      shouldSort: false,
      removeItemButton: true,
      noChoicesText: 'Пусто',
      itemSelectText: 'Выбрать'
    }
    if(typeof select.dataset.keywords != "undefined"){
      options.searchFn = customSearch
    }
    new Choices(select, options)
  })
}
if(document.querySelectorAll('select.multipleSelect').length>0){
  multipleSelect()
}



document.addEventListener('DOMContentLoaded', function() {
  if(document.querySelector('.ace-editor-area[id]')){
    const fieldsAceEditor = document.querySelectorAll('.ace-editor-area[id]')
    fieldsAceEditor.forEach((field) => {
      const hiddenInput = field.previousElementSibling;
      hiddenInput.id = 'hidden-' + field.id;
      field.insertAdjacentElement('afterend', hiddenInput);
      const editor = ace.edit(field.id);

      editor.setTheme("ace/theme/monokai");
      editor.session.setMode("ace/mode/html");
      editor.session.on('change', () => {
        document.getElementById(hiddenInput.id).value = editor.getValue();
      });
    })
  }
  // Получение всех чекбоксов .action и элемента action-box
  const actions = document.querySelectorAll('.action');
  const actionBox = document.getElementById('action-box');

  // Функция для проверки и обновления видимости action-box
  function updateActionBoxVisibility() {
    const anyChecked = Array.from(actions).some(checkbox => checkbox.checked);
    actionBox.classList.toggle('hidden', !anyChecked);
  }

  // Назначение слушателей для каждого чекбокса .action
  actions.forEach(action => {
    action.addEventListener('change', updateActionBoxVisibility);
  });

  // Обработка чекбокса "Выбрать все"
  const checkAll = document.getElementById('check_all');
  if (checkAll) {
    checkAll.addEventListener('change', function() {
      actions.forEach(action => {
        action.checked = checkAll.checked;
      });
      // Обновление видимости action-box после изменения состояния чекбоксов
      updateActionBoxVisibility();
    });
  }
  const commentForms = document.querySelectorAll('form.add_comment');
  commentForms.forEach((form) => {
    form.addEventListener('submit', (event) => {
      event.preventDefault()
      const form = event.target
      const data = serialize(form)
      window.ajax.post(form.action, data, (response) => {
        clearFormData(form)
        document.getElementById('comment-'+data.order_id).innerText = data.comment;
        Fancybox.close();
      })
    })
  })
});
function serialize(formElement) {
  const elements = formElement.elements;
  let serializedObject = {};

  for (let i = 0; i < elements.length; i++) {
    const element = elements[i];
    const type = element.type;
    const name = element.name;
    const value = element.value;

    if (!name || element.disabled) continue;

    if (type === 'text' || type === 'email' || type === 'password' || type === 'hidden' || element.nodeName === 'TEXTAREA') {
      serializedObject[name] = value;
    } else if (type === 'radio' || type === 'checkbox') {
      if (element.checked) {
        serializedObject[name] = value;
      }
    } else if (element.nodeName === 'SELECT') {
      if (element.multiple) {
        serializedObject[name] = [];
        for (let j = 0; j < element.options.length; j++) {
          const option = element.options[j];
          if (option.selected) {
            serializedObject[name].push(option.value);
          }
        }
      } else {
        serializedObject[name] = element.value;
      }
    }
  }

  return serializedObject;
}
function clearFormData(formElement) {
  const elements = formElement.elements;

  for (let i = 0; i < elements.length; i++) {
    const element = elements[i];
    const type = element.type;

    if (element.disabled) continue;

    if (type === 'text' || type === 'email' || type === 'password' || type === 'hidden' || element.nodeName === 'TEXTAREA') {
      element.value = '';
    } else if (type === 'radio' || type === 'checkbox') {
      element.checked = false;
    } else if (element.nodeName === 'SELECT') {
      element.selectedIndex = -1;
    }
  }
}
//
// order
//
function resetShipping(){
  document.getElementById('shipping-info').innerHTML = '';
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

function ozl_init(collection) {
  ymaps.ready(
    function () {
      if (typeof collection != 'undefined' && collection != '') {
        ozl_create_modal();
        var map = new ymaps.Map('ozl_map', {
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

function ozl_create_modal() {
  // html = '<div id="ozl_modal" class="ll_map popup" style="padding-left:0;padding-right:0;padding-bottom:40px;width: 100%;min-height: 320px"></div>';
  //
  // $('#ozonMap').append(html);
  // Создать новый элемент div
  var newDiv = document.createElement('div');

  newDiv.id = 'ozl_modal';
  newDiv.className = 'll_map popup';
  newDiv.style.paddingLeft = '0';
  newDiv.style.paddingRight = '0';
  newDiv.style.paddingBottom = '40px';
  newDiv.style.width = '100%';
  newDiv.style.minHeight = '320px';

  var parentDiv = document.getElementById('ozonMap');
  parentDiv.appendChild(newDiv);
  // var width = $('#ozl_modal .ll_modal').width(),
  //   height = document.documentElement.clientHeight * .5;
  //
  // $('#ozl_modal').append('<div id="ozl_map" style="width: ' + width + 'px; height: ' + height + 'px;"></div>');
  var modalElement = document.querySelector('#ozl_modal');
  var width = modalElement.offsetWidth;

  var height = document.documentElement.clientHeight * 0.5;

  var newDiv = document.createElement('div');
  newDiv.id = 'ozl_map';
  newDiv.style.width = width + 'px';
  newDiv.style.height = height + 'px';

  var parentDiv = document.getElementById('ozl_modal');
  parentDiv.appendChild(newDiv);
}

var ozl_code = null;

function ozl_show_modal(filter, code, type) {
  if (typeof code != 'undefined' && code != '') {
    ozl_code = code;
  }

  // if (typeof type != 'undefined' && type != '') {
  //   $('#ozl_filter_' + type).click();
  // }
  // $('#ozl_modal').show();
  if (typeof type !== 'undefined' && type !== '') {
    var filterElement = document.getElementById('ozl_filter_' + type);
    if (filterElement) {
      filterElement.click();
    }
  }

  var modalElement = document.getElementById('ozl_modal');
  if (modalElement) {
    modalElement.style.display = 'block';
  }
}

const shipping_fields = document.querySelectorAll('.shipping-item')

for (let i = 0; i < shipping_fields.length; i++) {
  shipping_fields[i].addEventListener('change', changeMethodShipping);
}
function getShippingMeghod(){
  const shippingInput = document.querySelector('input[name="shipping"]');
  return shippingInput.value;
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
function changeMethodShipping(event){
  const this_method = event.target;
  const checkedInput = document.querySelector('.shipping-item:checked');
  const shippingInput = document.querySelector('input[name="shipping"]');

  shippingInput.value = this_method.value;
  resetShipping();
  // скрываем и показываем нужную кнопку
  const button_box = this_method.closest('.form-group').querySelector('.button-box');
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
    }else{
      pochtaAddress.classList.remove('block');
      pochtaAddress.classList.add('hidden');
      pochtaFields.forEach((field) => {
        field.required = false;
      })
    }
  }
}
const country_field = document.getElementById('country')
if(country_field){
  country_field.addEventListener('change', changeCountry);
}
function changeCountry(event){
  const field = event.target;
  const allowMethods = field.options[field.selectedIndex].dataset.shipping.split(',');
  const shippingMethods = document.querySelectorAll('.shipping-item')
  shippingMethods.forEach((item) => {
    if (!checkAccessShipping(item.value)){
      item.checked = false;
      item.disabled = true;
      var event = new Event('change');
      item.dispatchEvent(event);
    }else{
      item.disabled = false;
    }
  })
}
const coosePvzButtons = document.querySelectorAll('.shipping-methods .button-box a');
coosePvzButtons.forEach((button)=>{
  button.addEventListener('click', (event)=>{
    event.preventDefault()
    const chipping = event.target.dataset.shipping
    choosedShippingModule(chipping)
  })
})
function choosedShippingModule(shipping){
  if (!document.querySelector('#blockShipping' + shipping)) {
    var shippingName;
    if (shipping == 'boxberry') {
      shippingName = 'Boxberry доставка';
    } else if (shipping == 'cdek') {
      shippingName = 'Доставка СДЭК';
    } else if (shipping == 'cdek_courier') {
      shippingName = 'Доставка СДЭК курьер';
    } else if (shipping === 'x5post') {
      shippingName = 'Доставка 5 Пост';
    }
    var blockShippingModule = document.createElement('div');
    blockShippingModule.id = 'blockShipping' + shipping;
    blockShippingModule.style.display = 'none';
    blockShippingModule.classList.add('shipping-main');
    blockShippingModule.classList.add('container');
    blockShippingModule.classList.add('w-full');
    blockShippingModule.classList.add('max-w-screen-lg');

    var html = '<div class="p-4"><h3 class="text-xl mb-2 font-bold">' + shippingName + '</h3>';
    html += '<div class="form-group">';
    html += '  <label for="' + shipping + '-region">Регион</label>';
    html += '  <select class="form-control w-full" name="region" id="' + shipping + '-region" required>';
    html += '    <option disabled value="" id="regionsLoading">Загрузка...</option>';
    html += '  </select>';
    html += '</div>';
    html += '<div class="form-group" id="field-cities" style="display: none">';
    html += '  <label for="' + shipping + '-cities">Город</label>';
    html += '  <select class="form-control w-full" name="city" id="' + shipping + '-cities" required><option disabled value="">Выбрать...</option></select>';
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
  } else if (shipping === 'x5post') {
    link = myData.getVar('route_getX5PostRegions');
  }

  if (document.getElementById(shipping + '-region')) {
    window.ajax.get(link, {country: country}, (response) => {
      if (response != '') {
        var option = document.createElement('option');
        option.innerText = 'Выбрать...';
        option.setAttribute('disabled', true);
        option.setAttribute('selected', true);
        document.getElementById(shipping + '-region').append(option);
        for (var i = 0; i < response.length; i++) {
          var option = document.createElement('option');
          if (shipping == 'ozon') {
            option.value = response[i].id;
            option.innerText = response[i].name;
          } else if (shipping == 'boxberry') {
            option.value = response[i].id;
            option.innerText = response[i].name;
          }  else if (shipping === 'x5post') {
            option.value = response[i].id;
            option.innerText = response[i].name;
          } else if (shipping == 'cdek' || shipping == 'cdek_courier') {
            option.value = response[i].id;
            option.innerText = response[i].region;
          }
          document.getElementById(shipping + '-region').append(option);
        }
        document.getElementById('regionsLoading').remove();
      } else {
        alert('Ошибка загрузки доступных регионов');
      }
    })
    // $.ajax({
    //   type: 'GET',
    //   url: link,
    //   data: {country: country},
    //   success: function (response) { //Если все нормально
    //     if (response != '') {
    //       var option = document.createElement('option');
    //       option.innerText = 'Выбрать...';
    //       option.setAttribute('disabled', true);
    //       option.setAttribute('selected', true);
    //       document.getElementById(shipping + '-region').append(option);
    //       for (var i = 0; i < response.length; i++) {
    //         var option = document.createElement('option');
    //         if (shipping == 'ozon') {
    //           option.value = response[i].id;
    //           option.innerText = response[i].name;
    //         } else if (shipping == 'boxberry') {
    //           option.value = response[i].id;
    //           option.innerText = response[i].name;
    //         } else if (shipping == 'cdek' || shipping == 'cdek_courier') {
    //           option.value = response[i].id;
    //           option.innerText = response[i].region;
    //         }
    //         document.getElementById(shipping + '-region').append(option);
    //       }
    //       document.getElementById('regionsLoading').remove();
    //     } else {
    //       alert('Ошибка загрузки доступных регионов');
    //     }
    //   },
    //   error: function () {
    //   }
    //
    // });
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
  } else if (shipping === 'x5post') {
    link = myData.getVar('route_getX5PostCities');
  }
  window.ajax.get(link, {region: region}, (response) => {
    if (response != '') {
      document.getElementById(shipping + '-cities').innerHTML = '';
      for (var i = 0; i < response.length; i++) {
        var option = document.createElement('option');
        if (shipping == 'ozon') {
          option.value = response[i].id;
          option.innerText = response[i].name;
        } else if (shipping == 'boxberry') {
          option.value = response[i].id;
          option.innerText = response[i].Name;
        } else if (shipping === 'x5post') {
          option.value = response[i].id;
          option.innerText = response[i].name;
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
  // $.ajax({
  //   type: 'GET',
  //   url: link,
  //   data: {region: region},
  //   success: function (response) { //Если все нормально грузим города
  //     if (response != '') {
  //       document.getElementById(shipping + '-cities').innerHTML = '';
  //       for (var i = 0; i < response.length; i++) {
  //         var option = document.createElement('option');
  //         if (shipping == 'ozon') {
  //           option.value = response[i].id;
  //           option.innerText = response[i].name;
  //         } else if (shipping == 'boxberry') {
  //           option.value = response[i].id;
  //           option.innerText = response[i].Name;
  //         } else if (shipping == 'cdek' || shipping == 'cdek_courier') {
  //           if (shipping == 'cdek'){
  //             option.value = response[i].id;
  //           }else{
  //             option.value = response[i].code;
  //           }
  //           if (response[i].sub_region) {
  //             option.innerText = response[i].city + ' (' + response[i].sub_region + ')';
  //           } else {
  //             option.innerText = response[i].city;
  //           }
  //         }
  //         document.getElementById(shipping + '-cities').append(option);
  //       }
  //       $('#field-cities').show();
  //       $('#' + shipping + '-cities').show();
  //       $('#' + shipping + '-cities').trigger('change');
  //     } else {
  //       $('#field-cities').remove();
  //       changeCity(shipping, region, null);
  //     }
  //   }
  // });
}

function changeCity(shipping) {

  var blockShippingOzon = document.querySelector('#blockShipping' + shipping);
  var region = blockShippingOzon.querySelector('select[name="region"]').value;
  var city = blockShippingOzon.querySelector('select[name="city"]').value;
  if (shipping == 'boxberry') {
    var link = myData.getVar('route_getBoxberryPvz');
  } else if (shipping == 'cdek') {
    var link = myData.getVar('route_getCdekPvz');
  } else if (shipping === 'x5post') {
    link = myData.getVar('route_getX5PostPvz');
  }
  if (shipping != 'cdek_courier') {
    window.ajax.get(link, {region: region, city: city}, (response) => {
      if (response !== '') {
        // Находим элемент с соответствующим id и обновляем его HTML-содержимое
        var element = document.getElementById(shipping + '-shipping');
        if (element) {
          element.innerHTML = response.html;
        }
        const pvzField = document.getElementById(shipping + '-pvz')
        pvzField.addEventListener('change', (event) => {
          ozl_deliveryVariantIdChange(event.target)
        })
        setTimeout(function(){
          ozl_init(response.map);
          ozl_show_modal();
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
              ozl_deliveryVariantId(pvzCode, pvzAddress, cityCode, city)
            }
          });
        },1000);

      } else {
        alert('Ошибка. Перезагрузите страницу и попробуйте снова');
      }
    })
    // $.ajax({
    //   type: 'GET',
    //   url: link,
    //   data: {region: region, city: city},
    //   success: function (response) { //Если все нормально
    //     if (response != '') {
    //       $('#' + shipping + '-shipping').html(response);
    //     } else {
    //       alert('Ошибка. Перезагрузите страницу и попробуйте снова');
    //     }
    //   }
    // });
  } else {
    var html = '<div id="'+shipping+'-address-form"><div class="form-group">';
    html += '  <label for="' + shipping + '-street">Улица</label>';
    html += '  <input type="text" name="' + shipping + '-street" class="form-control w-full" value="" id="' + shipping + '-street">';
    html += '</div>';
    html += '<div class="flex flex-wrap -m-1"><div class="w-1/2 p-1"><div class="form-group">';
    html += '  <label for="' + shipping + '-house">Дом</label>';
    html += '  <input type="text" name="' + shipping + '-house" class="form-control w-full" value="" id="' + shipping + '-house">';
    html += '</div></div>';
    html += '<div class="w-1/2 p-1"><div class="form-group">';
    html += '  <label for="' + shipping + '-flat">Квартира</label>';
    html += '  <input type="text" name="' + shipping + '-flat" class="form-control w-full" value="" id="' + shipping + '-flat">';
    html += '</div></div></div>';

    html += '<div style="margin-top: 40px;" id="' + shipping + '-btn"><button type="button" class="button button-primary text-black" id="' + shipping + '-calculate">Далее</button></div></div>';
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

function ozl_deliveryVariantId(id, address, city = false, city_name = null) {
  var shippingMethod = document.querySelector('input[name="shipping-method"]:checked');
  var shipping
  if (shippingMethod){
    shipping = shippingMethod.value;
  }

  if (shipping) {
    calculateModule(shipping, id, address, city, city_name);

    // Установить выбранным элемент с id, соответствующим переменной id
    var selectElement = document.getElementById(id);
    if (selectElement) {
      selectElement.selected = true;
    }
  }
};

function ozl_deliveryVariantIdChange(elem) {
  var shippingMethod = document.querySelector('input[name="shipping-method"]:checked');
  var shipping
  if (shippingMethod){
    shipping = shippingMethod.value;
  }
  var address = elem.options[elem.selectedIndex].getAttribute('data-address');
  var city = false;
  var city_name = false;
  if (shipping == 'cdek') {
    city = elem.options[elem.selectedIndex].getAttribute('data-city');
    city_name = elem.options[elem.selectedIndex].getAttribute('data-city-name');
  }
  calculateModule(shipping, elem.value, address, city, city_name);
  // $('#ozon-pvz-id').val(elem.value);
  // $('#ozon-pvz-address').val(address);
  // $('#form-submit').show();
};

function calculateModule(shipping, id, address, city = false, city_name = null) {
  if (shipping == 'boxberry') {
    var shippingName = 'Boxberry доставка';
    var params = {code: id};
  } else if (shipping == 'cdek') {
    var shippingName = 'Доставка СДЭК';
    var params = {city: city};
    if (city_name){
      address = city_name+', '+address;
    }
  }else if(shipping === 'x5post'){
    shippingName = 'Доставка 5 Пост';
  }
  resetShipping();

  var shippingElement = document.getElementById(shipping);
  var pvzIdElement = document.getElementById(shipping + '-pvz-id');
  var pvzAddressElement = document.getElementById(shipping + '-pvz-address');

  if(shippingElement){
    shippingElement.checked = true;
  }
  if(pvzIdElement){
    pvzIdElement.value = id;
  }
  if(pvzAddressElement){
    pvzAddressElement.value = address;
  }

  var alert = document.createElement('div');
  alert.className = 'p-4 rounded-md bg-green-100';
  alert.setAttribute('role', 'alert');
  alert.textContent = shippingName + ' до пункта выдачи по адресу: ' + address;

  document.getElementById('shipping-info').innerHTML = '';
  document.getElementById('shipping-info').append(alert);

  Fancybox.close();
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
    alert('Укажите полный адрес');
    return false;
  }
  var address = region + ', ' + city + ', ' + street + ', д. ' + house;
  if (flat) {
    address += ', кв. ' + flat;
  }
  // Получаем элементы с помощью метода getElementById
  var regionElement = document.getElementById(shipping + '-form-region');
  var cityElement = document.getElementById(shipping + '-form-city');
  var streetElement = document.getElementById(shipping + '-form-street');
  var houseElement = document.getElementById(shipping + '-form-house');
  var flatElement = document.getElementById(shipping + '-form-flat');
  var addressElement = document.getElementById(shipping + '-form-address');

// Устанавливаем значения для элементов
  regionElement.value = region_code;
  cityElement.value = city_code;
  streetElement.value = street;
  houseElement.value = house;
  flatElement.value = flat;
  addressElement.value = address;

  var alert = document.createElement('div');
  alert.className = 'p-4 rounded-md bg-green-100';
  alert.setAttribute('role', 'alert');
  alert.textContent = 'Доставка курьером СДЭК по адресу: ' + address;

  document.getElementById('shipping-info').innerHTML = '';
  document.getElementById('shipping-info').append(alert);
  Fancybox.close();
}

//
// order end
//
// vertical tabs
document.addEventListener("DOMContentLoaded", function() {
  const toggleWrappers = document.querySelectorAll('.toggle-wrapper');

  toggleWrappers.forEach(wrapper => {
    const content = wrapper.querySelector('.toggle-content');
    const toggleButton = wrapper.querySelector('.toggle-button');
    const arrow = wrapper.querySelector('svg');

    // Сохраняем высоту контента для каждого блока
    let contentHeight = content.scrollHeight + "px";

    // Устанавливаем начальную высоту и overflow
    content.style.height = '0px';
    content.style.overflow = 'hidden';

    toggleButton.addEventListener('click', () => {
      if(content.style.height === '0px') {
        content.style.height = contentHeight;
        arrow.style.transform = 'rotate(180deg)'; // Переворачиваем стрелку вниз
      } else {
        content.style.height = '0px';
        arrow.style.transform = 'rotate(0deg)'; // Возвращаем стрелку в начальное положение
      }
    });
  });
});
const boxCounter = () => document.getElementById('product-cards_container').querySelectorAll('.product-card_item').length + 1
// vertical tabs end
if(document.getElementById('addPCItem')){
  document.getElementById('addPCItem').addEventListener('click', () => {
    const card_style = document.getElementById('card_style').value
    const element = document.getElementById(card_style+'_donor').firstElementChild.cloneNode(true)
    const inputs = element.querySelectorAll('input, textarea')
    const index = boxCounter()
    element.dataset.field = document.getElementById(card_style+'_donor').dataset.field
    element.dataset.cardStyle = card_style
    inputs.forEach((input, idx) => {
      let name = input.dataset.name
      if(input.dataset.parent){
        name = input.dataset.parent+'-'+name
      }

      input.name = `product_cards[${index}][${name}]`
      let id = `product_cards-${index}-${name}`
      if(input.type=='radio'||input.type=='checkbox'){
        id = `product_cards-${index}-${name}-${idx}`
      }
      if(input.dataset.name == 'card_style'){
        input.value = card_style
      }
      // if(input.type=='radio'){
      //   input.addEventListener('click', function(event) {
      //     // Если радио-кнопка уже была выбрана, отменяем выбор
      //     if (input.getAttribute('data-checked') === '1') {
      //       input.checked = false;
      //       input.setAttribute('data-checked', '0');
      //     } else {
      //       // Отмечаем радио-кнопку как выбранную
      //       input.setAttribute('data-checked', '1');
      //     }
      //
      //     // Снимаем выбор с других радио-кнопок в этой же группе
      //     inputs.forEach(function(otherRadio) {
      //       if (otherRadio !== input && otherRadio.name === input.name) {
      //         otherRadio.setAttribute('data-checked', '0');
      //       }
      //     });
      //   });
      // }
      input.id = id
      if(input.closest('.form-group, .radio-group')){
        const label = input.closest('.form-group, .radio-group').querySelector('label')
        if(label){
          label.setAttribute('for', id)
        }
      }
    })
    element.querySelector('.lfm-preview').id = `lfm-preview-${index}`
    let file_btn = element.querySelector('.addImage')
    if(file_btn){
      lfm(file_btn, 'image', {
        prefix: window.filemanger.image,
        working_dir: window.filemanger.working_dir,
        input: element.querySelector('input[data-name="img"]').id,
        input_preview: element.querySelector('input[data-name="thumb"]').id,
        preview: `lfm-preview-${index}`,
        callback: 'fieldImages'
      });
    }
    document.getElementById('product-cards_container').appendChild(element)
    initBox(element, index);
    window.updateTextSize();
  })
}

function initBox(box, boxCount) {
  let fields = box.querySelector(".fields");
  const fieldCounter = (fields) => fields.querySelectorAll('.field').length + 1; // счётчик для полей внутри блока
  // Поднять блок
  box.querySelector(".btn-up").addEventListener("click", function(event) {
    event.preventDefault()
    let prev = box.previousElementSibling;
    if (prev) {
      box.parentNode.insertBefore(box, prev);
    }
  });

  // Опустить блок
  box.querySelector(".btn-down").addEventListener("click", function(event) {
    event.preventDefault()
    let next = box.nextElementSibling;
    if (next) {
      box.parentNode.insertBefore(next, box);
    }
  });

  // Удалить блок
  box.querySelector(".btn-remove").addEventListener("click", function(event) {
    event.preventDefault()
    if (!confirm('Удалить элемент?')) {
      return false;
    }
    box.remove();
  });

  // Добавить новое поле при клике на .addfield
  if (box.querySelector(".addField")){
    box.querySelector(".addField").addEventListener("click", function(event) {
      event.preventDefault()
      const element = document.getElementById(box.dataset.field).firstElementChild.cloneNode(true);
      const inputs = element.querySelectorAll('input, textarea')

      inputs.forEach((input, idx) => {
        let name = input.dataset.name
        if(input.dataset.parent){
          name = input.dataset.parent+'-'+name
        }
        input.name = `product_cards[${boxCount}][fields][${fieldCounter(fields)}][${name}]`

        let id = `product_cards-${boxCount}-fields-${fieldCounter(fields)}-${name}`
        if(input.type=='radio'||input.type=='checkbox'){
          id = `product_cards-${boxCount}-fields-${fieldCounter(fields)}-${name}-${idx}`
        }
        // if(input.type=='radio'){
        //   input.addEventListener('click', function(event) {
        //     // Если радио-кнопка уже была выбрана, отменяем выбор
        //     if (input.getAttribute('data-checked') === '1') {
        //       input.checked = false;
        //       input.setAttribute('data-checked', '0');
        //     } else {
        //       // Отмечаем радио-кнопку как выбранную
        //       input.setAttribute('data-checked', '1');
        //     }
        //
        //     // Снимаем выбор с других радио-кнопок в этой же группе
        //     inputs.forEach(function(otherRadio) {
        //       if (otherRadio !== input && otherRadio.name === input.name) {
        //         otherRadio.setAttribute('data-checked', '0');
        //       }
        //     });
        //   });
        // }
        input.id = id
        const label = input.closest('.form-group, .radio-group').querySelector('label')
        if(label){
          label.setAttribute('for', id)
        }
      })

      fields.appendChild(element);

      // Добавляем обработчики для поля
      initField(element);
    });
  }
  // запускаем поля, которые добавлены изначально
  const boxFields = box.querySelectorAll('.field-input')
  boxFields.forEach((field) => {
    initField(field)
  })
  // зпускаем действия
  const actionButtons = box.querySelectorAll('.card-action')
  if (actionButtons.length){
    const fieldContainer = box.querySelector('.card_fields')
    const cardActionsContainer = box.querySelector('.card-actions')
    actionButtons.forEach((button) => {
      if(button.type == 'radio') {
        button.addEventListener('change', () => {
          const name = button.dataset.name
          const value = button.value
          let styleTranform = fieldContainer.style.transform
          if (name == 'text-align') {
            fieldContainer.style.textAlign = value
          }else if(name == 'align'){
            const fieldsByName = cardActionsContainer.querySelectorAll('[name="'+button.name+'"]')
            fieldsByName.forEach((fieldByName) => {
              fieldContainer.classList.remove(fieldByName.value)
            })
            fieldContainer.classList.add(value)
            if (value.endsWith('center') && !styleTranform.includes('translateX(-50%)')){
              styleTranform += ' translateX(-50%)'
            }else if(!value.endsWith('center') && styleTranform){
              styleTranform = styleTranform.split('translateX(-50%)').join("")
            }
            fieldContainer.style.left = ''
            fieldContainer.style.right = ''

            if(cardActionsContainer.querySelector('input[data-name="'+name+'-value"]')){
              cardActionsContainer.querySelector('input[data-name="'+name+'-value"]').dispatchEvent(new Event("input"));
            }
          }else if(name == 'vertical-align'){
            const fieldsByName = cardActionsContainer.querySelectorAll('[name="'+button.name+'"]')
            fieldsByName.forEach((fieldByName) => {
              fieldContainer.classList.remove(fieldByName.value)
            })
            fieldContainer.classList.add(value)
            if (value.endsWith('center') && !styleTranform.includes('translateY(-50%)')){
              styleTranform += ' translateY(-50%)'
            }else if(!value.endsWith('center') && styleTranform){
              styleTranform = styleTranform.split('translateY(-50%)').join("")
            }
            fieldContainer.style.top = ''
            fieldContainer.style.bottom = ''
            if(cardActionsContainer.querySelector('input[data-name="'+name+'-value"]')){
              cardActionsContainer.querySelector('input[data-name="'+name+'-value"]').dispatchEvent(new Event("input"));
            }
          }
          if (styleTranform) {
            fieldContainer.style.transform = styleTranform
          }else{
            fieldContainer.style.transform = ''
          }
        })
      }else if(button.type == 'text'){
        button.addEventListener('input', () => {
          const name = button.dataset.name
          const thisAction = window.removeSuffix(name, '-value')
          if(thisAction=='align'){
            const actionButton = cardActionsContainer.querySelector('[data-name="'+thisAction+'"]:checked')
            if(actionButton){
              if (actionButton.value=='h-pos-left'){
                fieldContainer.style.left = (button.value != '' ? button.value : 0)+'%'
                fieldContainer.style.right = 'auto'
              }else if(actionButton.value=='h-pos-right'){
                fieldContainer.style.right = (button.value != '' ? button.value : 0)+'%'
                fieldContainer.style.left = 'auto'
              }
            }
          }else if(thisAction=='vertical-align'){
            const actionButton = cardActionsContainer.querySelector('[data-name="'+thisAction+'"]:checked')
            if(actionButton){
              if (actionButton.value=='v-pos-top'){
                fieldContainer.style.top = (button.value != '' ? button.value : 0)+'%'
                fieldContainer.style.bottom = 'auto'
              }else if(actionButton.value=='v-pos-bottom'){
                fieldContainer.style.bottom = (button.value != '' ? button.value : 0)+'%'
                fieldContainer.style.top = 'auto'
              }
            }
          }else if(thisAction == 'background'){
            fieldContainer.style.background = ''
            if(button.value){
              fieldContainer.style.background = button.value
            }
          }else if(thisAction == 'color'){
            fieldContainer.style.color = ''
            if(button.value){
              fieldContainer.style.color = button.value
            }
          }
        })
      }
    })
  }
  window.numericFieldsListener()
}
function initField(field) {
  const btnUp = field.querySelector(".field-btn-up");
  if(btnUp){
    btnUp.addEventListener("click", function(event) {
      event.preventDefault()
      let prev = field.previousElementSibling;
      if (prev) {
        field.parentNode.insertBefore(field, prev);
      }
    });
  }

  const btnDown = field.querySelector(".field-btn-down");
  if(btnDown){
    btnDown.addEventListener("click", function(event) {
      event.preventDefault()
      let next = field.nextElementSibling;
      if (next) {
        field.parentNode.insertBefore(next, field);
      }
    });
  }

  const btnRemove = field.querySelector(".field-btn-remove");
  if(btnRemove){
    btnRemove.addEventListener("click", function(event) {
      event.preventDefault()
      if (!confirm('Удалить элемент?')) {
        return false;
      }
      const textField = field.querySelector('textarea')
      const previewField = document.querySelector('div[data-field-id="'+textField.id+'"]')
      previewField.remove()
      field.remove();
    });
  }

  // генерируем поле в превью
  const previewField = document.createElement('div')
  const textField = field.querySelector('textarea')
  if(textField.dataset.name=='small-text'){
    previewField.className = 'cormorantGaramond lh-base product-description-smallText subtitle-3'
  }else if(textField.dataset.name=='big-text'){
    previewField.className = 'cormorantGaramond lh-base product-description-bigText subtitle-3'
  }else if(textField.dataset.name=='text'){
    previewField.className = 'cormorantGaramond lh-base product-description-item subtitle-3'
  }
  previewField.dataset.fieldId = textField.id
  field.closest('.product-card_item').querySelector('.card_fields').append(previewField)

  textField.addEventListener('input', (event) => {
    const elem = event.target
    previewField.innerText = elem.value
  })
  //  управление полем
  const actionButtons = field.querySelectorAll('.field-action')
  if (actionButtons.length){
    actionButtons.forEach((button) => {
      if(button.type == 'radio') {
        button.addEventListener('change', () => {
          const name = button.dataset.name
          const value = button.value
          let styleTranform = previewField.style.transform
          if (name == 'text-align') {
            previewField.style.textAlign = value
          }else if(name == 'align'){
            const fieldsByName = field.querySelectorAll('[name="'+button.name+'"]')
            fieldsByName.forEach((fieldByName) => {
              previewField.classList.remove(fieldByName.value)
            })
            previewField.classList.add(value)
            if (value.endsWith('center') && !styleTranform.includes('translateX(-50%)')){
              styleTranform += ' translateX(-50%)'
            }else if(!value.endsWith('center') && styleTranform){
              styleTranform = styleTranform.split('translateX(-50%)').join("")
            }
            previewField.style.left = ''
            previewField.style.right = ''

            if(field.querySelector('input[data-name="'+name+'-value"]')){
              field.querySelector('input[data-name="'+name+'-value"]').dispatchEvent(new Event("input"));
            }
          }else if(name == 'vertical-align'){
            const fieldsByName = field.querySelectorAll('[name="'+button.name+'"]')
            fieldsByName.forEach((fieldByName) => {
              previewField.classList.remove(fieldByName.value)
            })
            previewField.classList.add(value)
            if (value.endsWith('center') && !styleTranform.includes('translateY(-50%)')){
              styleTranform += ' translateY(-50%)'
            }else if(!value.endsWith('center') && styleTranform){
              styleTranform = styleTranform.split('translateY(-50%)').join("")
            }
            previewField.style.top = ''
            previewField.style.bottom = ''
            if(field.querySelector('input[data-name="'+name+'-value"]')){
              field.querySelector('input[data-name="'+name+'-value"]').dispatchEvent(new Event("input"));
            }
          }
          if (styleTranform) {
            previewField.style.transform = styleTranform
          }else{
            previewField.style.transform = ''
          }
        })
      }else if(button.type == 'text'){
        button.addEventListener('input', () => {
          const name = button.dataset.name
          const thisAction = window.removeSuffix(name, '-value')
          if(thisAction=='align'){
            const actionButton = field.querySelector('[data-name="'+thisAction+'"]:checked')
            if(actionButton){
              if (actionButton.value=='h-pos-left'){
                previewField.style.left = (button.value != '' ? button.value : 0)+'%'
                previewField.style.right = 'auto'
              }else if(actionButton.value=='h-pos-right'){
                previewField.style.right = (button.value != '' ? button.value : 0)+'%'
                previewField.style.left = 'auto'
              }
            }
          }else if(thisAction=='vertical-align'){
            const actionButton = field.querySelector('[data-name="'+thisAction+'"]:checked')
            if(actionButton){
              if (actionButton.value=='v-pos-top'){
                previewField.style.top = (button.value != '' ? button.value : 0)+'%'
                previewField.style.bottom = 'auto'
              }else if(actionButton.value=='v-pos-bottom'){
                previewField.style.bottom = (button.value != '' ? button.value : 0)+'%'
                previewField.style.top = 'auto'
              }
            }
          }else if(thisAction == 'background'){
            previewField.style.background = ''
            if(button.value){
              previewField.style.background = button.value
            }
          }else if(thisAction == 'color'){
            previewField.style.color = ''
            if(button.value){
              previewField.style.color = button.value
            }
          }
        })
      }
    })
  }
  window.numericFieldsListener()
}
// dynamic field end
// files
// window.fieldImages = (items) => {
//   if (myData.getVar('target_input')){
//     const target_input = document.getElementById(myData.getVar('target_input'));
//     var file_path = items
//       .map(function (item) {
//         return item.url;
//       })
//       .join(",");
//     target_input.value = file_path;
//     target_input.dispatchEvent(new Event("change"));
//   }
//   if (myData.getVar('target_preview')){
//     const target_preview = document.getElementById(myData.getVar('target_preview')).querySelector('.img');
//     const target_input_thumb = document.getElementById(myData.getVar('target_input_preview'));
//     target_preview.innerHTML = "";
//
//     items.forEach(function (item) {
//       // let full_image = document.createElement("a");
//       // full_image.href = 'javascript:;';
//       // full_image.style.display = 'inline-block';
//       // full_image.setAttribute('data-fancybox', true);
//       // full_image.setAttribute('data-src', item.url);
//       let img = document.createElement("img");
//       img.style.height = 'height: 5rem';
//       img.src = item.url;
//       img.className = 'overflow-hidden max-w-full object-cover object-center'
//       // full_image.appendChild(img);
//       target_preview.appendChild(img);
//       target_input_thumb.value = item.thumb_url;
//
//       Fancybox.bind('[data-fancybox]',{
//         Toolbar: {
//           display: {
//             left: ["infobar"],
//             middle: [],
//             right: ["close"],
//           },
//         },
//       });
//     });
//
//     target_preview.dispatchEvent(new Event("change"));
//   }
//   Fancybox.close();
// }
// window.choosedImages = (items) => {
//   if (myData.getVar('target_input')){
//     const target_input = document.getElementById(myData.getVar('target_input'));
//     var file_path = items
//       .map(function (item) {
//         return item.url;
//       })
//       .join(",");
//     target_input.value = file_path;
//     target_input.dispatchEvent(new Event("change"));
//   }
//   if (myData.getVar('target_preview')){
//     const target_preview = document.getElementById(myData.getVar('target_preview'));
//     const target_input_thumb = document.getElementById(myData.getVar('target_input_preview'));
//     target_preview.innerHTML = "";
//
//     items.forEach(function (item) {
//       // let full_image = document.createElement("a");
//       // full_image.href = 'javascript:;';
//       // full_image.style.display = 'inline-block';
//       // full_image.setAttribute('data-fancybox', true);
//       // full_image.setAttribute('data-src', item.url);
//       let img = document.createElement("img");
//       img.style.height = 'height: 5rem';
//       img.src = item.url;
//       img.className = 'overflow-hidden max-w-full object-cover object-center'
//       // full_image.appendChild(img);
//       target_preview.appendChild(img);
//       target_input_thumb.value = item.thumb_url;
//
//       Fancybox.bind('[data-fancybox]',{
//         Toolbar: {
//           display: {
//             left: ["infobar"],
//             middle: [],
//             right: ["close"],
//           },
//         },
//       });
//     });
//
//     target_preview.dispatchEvent(new Event("change"));
//   }
//   Fancybox.close();
// }
// window.choosedFiles = (items) => {
//   if (myData.getVar('target_input_files')){
//     const target_input = document.getElementById(myData.getVar('target_input_files'));
//     var file_path = items
//       .map(function (item) {
//         return item.url;
//       })
//       .join(",");
//     target_input.value = file_path;
//     target_input.dispatchEvent(new Event("change"));
//   }
//   const button = document.getElementById('lfm-files');
//   const text = button.textContent.split('(')[0].trim();
//   button.textContent = text+' (' + items.length + ')';
//   var html = '<ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside mt-2 mb-4">';
//   items.forEach((item) => {
//     html += '<li>' + item.name + '</li>';
//   })
//   html += '</ul>'
//   document.querySelector('.files-list').innerHTML = html;
//   Fancybox.close();
// }
const lfm = function (button, type, options) {
  if (!button){
    return false;
  }
  options.type = type
  if (typeof options.callback == 'undefined') {
    if (type=='image'){
      options.callback = 'choosedImages'
    }else if(type=='file'){
      options.callback = 'choosedFiles'
    }else if(type=='video'){
      options.callback = 'choosedVideo'
    }
  }
  button.addEventListener('click', lfm_event(options));
  button.setAttribute('data-event-listener', 'lfm_event')
};
function lfm_event(options) {
  return (event) => {
    if (options.type=='image'){
      window.target_input = options.input;
      window.target_input_preview = options.input_preview;
      window.target_preview = options.preview;
    }else if(options.type=='file'){
      window.target_input_files = options.input;
      window.target_preview = options.preview;
    }else if(options.type=='video'){
      window.target_input_files = options.input;
      window.target_preview = options.preview;
    }
    var route_prefix = (options && options.prefix) ? options.prefix : "/laravel-filemanager";
    var working_dir = options.working_dir;

    let route = route_prefix + "?callback=" + options.callback + "&type=" + (options.type || "file") + "&working_dir=" + (working_dir || "")
    if(typeof options.multiple != "undefined" && options.multiple == "1"){
      route += "&multiple=1"
    }
    new Fancybox([{
      src: route,
      type: "iframe",
      width: "900px",
      height: "600px",
    }])
  }
}

const lfmButtons = document.querySelectorAll('[data-lfm]')

lfmButtons.forEach((button) => {
  const type = button.dataset.lfm
  if(type == 'image'){
    const img_input = button.dataset.input
    const thumb_input = button.dataset.thumb
    const preview = button.dataset.preview
    lfm(button, button.dataset.lfm, {
      prefix: window.filemanger.image,
      working_dir: window.filemanger.working_dir,
      input: img_input,
      input_preview: thumb_input,
      preview: preview,
      multiple: button.dataset.multiple ?? "0"
    });
  }else if(type == 'file'){
    const file_input = button.dataset.input
    let path = window.filemanger.working_dir
    if(typeof window.filemanger.file_dir != "undefined"){
      path = window.filemanger.file_dir
    }

    const preview = button.dataset.preview
    lfm(button, button.dataset.lfm, {
      prefix: window.filemanger.image,
      working_dir: path,
      input: file_input,
      preview: preview,
      multiple: button.dataset.multiple ?? "0"
    });
  }else if(type == 'video'){
    const video_input = button.dataset.input
    let path = window.filemanger.working_dir
    if(typeof window.filemanger.video_dir != "undefined"){
      path = window.filemanger.video_dir
    }

    const preview = button.dataset.preview
    lfm(button, button.dataset.lfm, {
      prefix: window.filemanger.image,
      working_dir: path,
      input: video_input,
      preview: preview,
      multiple: button.dataset.multiple ?? "0"
    });
  }
})
//
// if (typeof window.failmanger_route !== "undefined"&&typeof window.failmanger_type !== "undefined"){
//   lfm('lfm', window.failmanger_type, {prefix: window.failmanger_route});
// }

// files end
function checkDesignPageURL(url) {
  // Регулярное выражение для проверки URL с учетом GET-параметров и якорей
  const pattern = /^https?:\/\/[^\/]+\/admin\/products\/[^\/]+\/edit\/design(\/?(\?.+)?(#.+)?|$)/;
  return pattern.test(url);
}

// Дебаунсинг


// Обновление размера текста

if(checkDesignPageURL(window.location)){
  // собираем карточки из json
  const cards = document.querySelectorAll('#product-cards_container .product-card_item')
  cards.forEach((card) => {
    const cardJson = window.htmlDecode(card.querySelector('input[name="card_data"]').value)
    const cardData = JSON.parse(cardJson)
    card.querySelector('input[name="card_data"]').remove()
    const card_style = cardData['card_style']
    const inputs = card.querySelectorAll('input, textarea')
    const index = Number(card.querySelector('input[name="card_index"]').value)
    let emitInputs = []
    card.querySelector('input[name="card_index"]').remove()
    card.dataset.field = document.getElementById(card_style+'_donor').dataset.field
    card.dataset.cardStyle = card_style
    inputs.forEach((input, idx) => {
      if(!input.dataset.name){
        return false;
      }
      let name = input.dataset.name
      if(input.dataset.parent){
        name = input.dataset.parent+'-'+name
      }
      input.name = `product_cards[${index}][${name}]`
      let id = `product_cards-${index}-${name}`
      if(input.type=='radio'||input.type=='checkbox'){
        id = `product_cards-${index}-${name}-${idx}`
      }
      if (typeof cardData[name] != 'undefined'){
        if (input.type=='text'||input.tagName === 'TEXTAREA'||input.type=='hidden'){
          input.value = cardData[name]
          emitInputs.push(input)
        }else if(input.type=='radio'&&input.value==cardData[name]){
          input.checked = true
          emitInputs.push(input)
        }
      }

      input.id = id
      if(input.closest('.form-group, .radio-group')){
        const label = input.closest('.form-group, .radio-group').querySelector('label')
        if(label){
          label.setAttribute('for', id)
        }
      }
    })
    card.querySelector('.lfm-preview').id = `lfm-preview-${index}`
    let file_btn = card.querySelector('.addImage')
    if(file_btn){
      lfm(file_btn, 'image', {
        prefix: window.filemanger.image,
        working_dir: window.filemanger.working_dir,
        input: card.querySelector('input[data-name="img"]').id,
        input_preview: card.querySelector('input[data-name="thumb"]').id,
        preview: `lfm-preview-${index}`,
        callback: 'fieldImages'
      });
    }
    if(typeof cardData.img != "undefined") {
      const target_preview = card.querySelector('.product_card_preview');
      target_preview.innerHTML = "";

      let img = document.createElement("img");
      img.style.height = 'height: 5rem';
      img.src = cardData.img;
      img.className = 'overflow-hidden max-w-full object-cover object-center'
      // full_image.appendChild(img);
      target_preview.appendChild(img);

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
    if(typeof cardData.fields != "undefined") {
      for (let key in cardData.fields) {
        let field = cardData.fields[key];
        let fieldCounter = parseInt(key);

        if(!document.getElementById(card.dataset.field)){
          continue;
          console.log('card', card);
          console.log('card.dataset.field', card.dataset.field);
        }
        if(!document.getElementById(card.dataset.field).firstElementChild){
          continue;
          console.log('2', document.getElementById(card.dataset.field))
        }
        const element = document.getElementById(card.dataset.field).firstElementChild.cloneNode(true);
        const inputs = element.querySelectorAll('input, textarea')

        inputs.forEach((input, idx) => {
          let name = input.dataset.name
          if(input.dataset.parent){
            name = input.dataset.parent+'-'+name
          }
          input.name = `product_cards[${index}][fields][${fieldCounter}][${name}]`
          let id = `product_cards-${index}-fields-${fieldCounter}-${name}`
          if(input.type=='radio'||input.type=='checkbox'){
            id = `product_cards-${index}-fields-${fieldCounter}-${name}-${idx}`
          }
          if (typeof field[name] != 'undefined'){
            if (input.type=='text'||input.tagName === 'TEXTAREA'||input.type=='hidden'){
              input.value = field[name]
              if (field[name]){
                emitInputs.push(input)
              }
            }else if(input.type=='radio'&&input.value==field[name]){
              input.checked = true
              emitInputs.push(input)
            }
          }


          input.id = id
          if(!input.closest('.form-group, .radio-group')){
            console.log('input', input)
          }
          const label = input.closest('.form-group, .radio-group').querySelector('label')
          if(label){
            label.setAttribute('for', id)
          }
        })

        card.querySelector('.fields').appendChild(element);

        // Добавляем обработчики для поля
        initField(element);
      }
    }

    initBox(card, index);
    for(let key in emitInputs){
      let input = emitInputs[key];
      if (input.type=='text'||input.tagName === 'TEXTAREA'){
        input.dispatchEvent(new Event("input"));
      }else if(input.type=='radio'){
        input.dispatchEvent(new Event("change"));
      }
    }
    window.updateTextSize();
  })
  // Добавление обработчика события resize с дебаунсингом
  window.addEventListener('resize', () => debouncedResize(window.updateTextSize, 200));
  window.updateTextSize();
}
const choisesImgSelect = document.querySelectorAll('select.choisesImgSelect')
choisesImgSelect.forEach((select) => {
  if(select.disabled){
    return false;
  }
  var choices = new Choices(select, {
    itemSelectText: '',
    callbackOnCreateTemplates: function(template) {
      return {
        item: ({ classNames }, data) => {
          return template(`
                        <div class="${classNames.item} ${
            data.highlighted
              ? classNames.highlightedState
              : classNames.itemSelectable
          } ${
            data.placeholder ? classNames.placeholder : ''
          }" data-item data-id="${data.id}" data-value="${data.value}" ${
            data.active ? 'aria-selected="true"' : ''
          } ${data.disabled ? 'aria-disabled="true"' : ''} style="display:flex;align-items:center;">
                          <img src="${data.value}" style="width:40px;height:40px;margin-right:15px;"/> ${data.label}
                        </div>
                      `);
        },
        choice: ({ classNames }, data) => {
          return template(`
                  <div class="${classNames.item} ${classNames.itemChoice} ${
            data.disabled ? classNames.itemDisabled : classNames.itemSelectable
          }" data-select-text="${this.config.itemSelectText}" data-choice ${
            data.disabled
              ? 'data-choice-disabled aria-disabled="true"'
              : 'data-choice-selectable'
          } data-id="${data.id}" data-value="${data.value}" ${
            data.groupId > 0 ? 'role="treeitem"' : 'role="option"'
          } style="display:flex;align-items:center;">
                    <img src="${data.value}" style="width:40px;height:40px;margin-right:15px;"/> ${data.label}
                  </div>
                `);
        },
      };
    },

  });
})
// контенет, слайдер констркутор
const carouselAddSlide = document.querySelectorAll('.carousel-contructor .addSlide')
carouselAddSlide.forEach((button) => {
  let field_name = button.dataset.field ?? 'carousel_data'
  button.addEventListener('click', () => {
    const container = button.closest('.carousel-contructor').querySelector('.carousel')

    const elemId = button.dataset.id
    var donorId = button.dataset.donorId ?? elemId+'_donor'
    if(button.dataset.donor){
      donorId = button.dataset.donor
    }

    const element = document.getElementById(donorId).firstElementChild.cloneNode(true)

    // const buttons = element.querySelectorAll('button.addSlide')
    const inputs = element.querySelectorAll('input, textarea, select')
    const indexCounter = () => container.querySelectorAll('.carousel-slide').length + 1
    const index = indexCounter()
    inputs.forEach((input, idx) => {
      let name = input.dataset.name
      let parent = ''
      if(input.dataset.parent){
        parent = '['+input.dataset.parent+']'
      }
      if(input.dataset.field){
        field_name = input.dataset.field
      }
      input.name = `${field_name}[${elemId}][${index}]${parent}[${name}]`

      let id = `${field_name}-${elemId}-${index}-${parent}${name}`
      if(input.type=='radio'||input.type=='checkbox'){
        id = `${field_name}-${index}-${index}-${parent}${name}-${idx}`
      }
      if(input.dataset.name == 'card_style'){
        input.value = card_style
      }
      input.id = id
      input.removeAttribute('disabled')

      if(input.closest('.form-group, .radio-group')){
        const label = input.closest('.form-group, .radio-group').querySelector('label')
        if(label){
          label.setAttribute('for', id)
        }
      }
      if(input.tagName == 'TEXTAREA' && input.classList.contains('tinymce-textarea')){
        setTimeout(()=>{
          initTinymce(input)
        },100)
      }
      if(input.tagName == 'SELECT' && input.classList.contains('choisesImgSelect')){
        var choices = new Choices(input, {
          itemSelectText: '',
          callbackOnCreateTemplates: function(template) {
            return {
              item: ({ classNames }, data) => {
                return template(`
                        <div class="${classNames.item} ${
                                data.highlighted
                                  ? classNames.highlightedState
                                  : classNames.itemSelectable
                              } ${
                                data.placeholder ? classNames.placeholder : ''
                              }" data-item data-id="${data.id}" data-value="${data.value}" ${
                                data.active ? 'aria-selected="true"' : ''
                              } ${data.disabled ? 'aria-disabled="true"' : ''} style="display:flex;align-items:center;">
                          <img src="${data.value}" style="width:40px;height:40px;margin-right:15px;"/> ${data.label}
                        </div>
                      `);
                      },
                      choice: ({ classNames }, data) => {
                        return template(`
                  <div class="${classNames.item} ${classNames.itemChoice} ${
                          data.disabled ? classNames.itemDisabled : classNames.itemSelectable
                        }" data-select-text="${this.config.itemSelectText}" data-choice ${
                          data.disabled
                            ? 'data-choice-disabled aria-disabled="true"'
                            : 'data-choice-selectable'
                        } data-id="${data.id}" data-value="${data.value}" ${
                          data.groupId > 0 ? 'role="treeitem"' : 'role="option"'
                        } style="display:flex;align-items:center;">
                    <img src="${data.value}" style="width:40px;height:40px;margin-right:15px;"/> ${data.label}
                  </div>
                `);
              },
            };
          },

        });

        // input.addEventListener('choice', function(event) {
        //   var items = document.querySelectorAll('.choices__item');
        //   items.forEach(function(item) {
        //     console.log(item)
        //     var imgSrc = item.getAttribute('data-img-src');
        //     if (imgSrc && item.querySelector('img') === null) {
        //       var imgTag = document.createElement('img');
        //       imgTag.src = imgSrc;
        //       imgTag.width = 50;
        //       imgTag.height = 50;
        //       item.appendChild(imgTag);
        //     }
        //   });
        // });
      }

    })
    const slideImage = element.querySelector('.slideImage')
    if(slideImage){
      slideImage.querySelector('.lfm-preview').id = `lfm-preview-${elemId}-${index}`
      slideImage.querySelector('.lfm-preview').dataset.name = `${field_name}[${elemId}][${index}][image]`
      let file_btn = slideImage.querySelector('button[data-lfm="image"]')
      if(file_btn){
        file_btn.id = elemId+'-image'
        slideImage.querySelector('label').for = elemId+'-image'
        const preview = `lfm-preview-${elemId}-${index}`
        lfm(file_btn, file_btn.dataset.lfm, {
          prefix: window.filemanger.image,
          working_dir: window.filemanger.working_dir,
          preview: preview,
        });
      }
    }
    const slideAceEditor = element.querySelector('.ace-editor-area')
    if(slideAceEditor){
      slideAceEditor.id = window.generateRandomString()
    }
    const slideFile = element.querySelector('.slideFile')
    if(slideFile){
      slideFile.querySelector('.lfm-preview').id = `lfm-preview-${elemId}-${index}`
      slideFile.querySelector('.lfm-preview').dataset.name = `${field_name}[${elemId}][${index}]`
      let file_btn = slideFile.querySelector('button[data-lfm="file"]')
      if(file_btn){
        file_btn.id = elemId+'-file'
        slideFile.querySelector('label').for = elemId+'-file'
        const preview = `lfm-preview-${elemId}-${index}`
        lfm(file_btn, file_btn.dataset.lfm, {
          prefix: window.filemanger.file,
          working_dir: window.filemanger.file_dir,
          preview: preview,
        });
      }
    }

    container.appendChild(element)
    if(slideAceEditor){
      const hiddenInput = slideAceEditor.previousElementSibling;
      hiddenInput.id = 'hidden-' + slideAceEditor.id;
      slideAceEditor.insertAdjacentElement('afterend', hiddenInput);
      const editor = ace.edit(slideAceEditor.id);
      editor.setTheme("ace/theme/monokai");
      editor.session.setMode("ace/mode/html");
      editor.session.on('change', () => {
        document.getElementById(hiddenInput.id).value = editor.getValue();
      });
    }
    initSlide(element, index);
  })
})
function initSlide(box) {
  // Поднять блок
  box.querySelector(".btn-up").addEventListener("click", function(event) {
    event.preventDefault()
    let prev = box.previousElementSibling;
    if (prev) {
      box.parentNode.insertBefore(box, prev);
      if(box.querySelector('textarea.tinymce-textarea')){
        const fieldsTinymce = box.querySelectorAll('textarea.tinymce-textarea')
        fieldsTinymce.forEach((field) => {
          tinymce.get(field.id).remove();
          initTinymce(field);
        })
      }
    }
  });

  // Опустить блок
  box.querySelector(".btn-down").addEventListener("click", function(event) {
    event.preventDefault()
    let next = box.nextElementSibling;
    if (next) {
      box.parentNode.insertBefore(next, box);
    }
  });
  // Удалить блок
  box.querySelector(".btn-remove").addEventListener("click", function(event) {
    event.preventDefault()
    if (!confirm('Удалить слайд?')) {
      return false;
    }
    box.remove();
  });
  window.numericFieldsListener()
}
const carousel = document.querySelectorAll('.carousel-contructor .carousel-slide')
carousel.forEach((slide) => {
  initSlide(slide)
})


let allStatuses = document.querySelectorAll('.all_statuses');

allStatuses.forEach(status => {
  status.addEventListener('change', function() {
    let name = this.getAttribute('data-id-name');
    let editStatuses = document.querySelectorAll(`.edit_status[data-id="${name}"]`);

    editStatuses.forEach(editStatus => {
      editStatus.checked = this.checked;
      editStatus.dispatchEvent(new Event('change'));
    });
  });
});
const quantity_inputs = document.querySelectorAll('input.edit_quantity:not([name])');
quantity_inputs.forEach((input) => {
  input.addEventListener('keyup', handleInputChange);
  input.addEventListener('input', handleInputChange);
  input.addEventListener('change', handleInputChange);
})

const status_inputs = document.querySelectorAll('input.edit_status');
status_inputs.forEach((input) => {
  input.addEventListener('change', handleStatusChange);
})

function handleStatusChange(event) {
  let target = event.target;
  let name = target.dataset.fieldName;
  let offInput = document.querySelector('.off[data-field-name="'+name+'"]');

  if (!target.getAttribute('name')) {
    target.setAttribute('name', name);
  }
  if (!offInput.getAttribute('name')) {
    offInput.setAttribute('name', name);
  }

  if (target.checked) {
    offInput.checked = false;
  } else {
    offInput.checked = true;
  }

  document.getElementById('btn-update').style.display = 'block';
}
function handleInputChange(event) {
  let target = event.target;
  let name = target.getAttribute('data-field-name');
  target.setAttribute('name', name);
  target.closest('td').borderColor = 'blue';
  target.closest('td').style.background = '#e2e8f9';
  document.getElementById('btn-update').style.display = 'block';
}

const td_target = document.querySelectorAll('td[data-target]');
td_target.forEach((td) => {
  td.addEventListener('click', function (event) {
    if(event.target.matches('input.edit_status')){
      return false;
    }
    let target = event.target;
    if (!event.target.matches('td[data-target]')) {
      target = target.closest('td[data-target]');
    }
    let name = target.dataset.target;
    let elem = document.querySelector('input[data-field-name="'+name+'"]')
    if(elem){
      elem.focus();
    }
  });
})
