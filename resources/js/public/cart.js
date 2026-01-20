

function getQtyValueFromButton(button) {
  if (!button.hasAttribute('data-qty-id')) {
    return 1;
  }
  let inputId = button.getAttribute('data-qty-id');
  let inputElement = document.getElementById(inputId);

  if (!inputElement) {
    console.error(`Input с ID=${inputId} не найден.`);
    return 1;
  }
  let value = parseInt(inputElement.value, 10);
  return isNaN(value) ? 1 : value;
}
var cartIsUpdating = false;
const updateCart = (cart_data, handler = null) => {
  cartIsUpdating = true;
  var element = cart_data.elem
  delete cart_data.elem;
  var params = {
    cart_data: cart_data,
    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
  };
  window.ajax.post(window.cart.update, params, (response) => {
    if (response) {
      if (response.success) {
        let toast_params = {
          message: response.message,
          type: 'success',
        };
        if (response.cartCount > 0 && window.location.pathname != '/order'){
          toast_params.customButtons = [
            {
              text: 'Оформить заказ',
              onClick: function () {
                window.location.href = window.order.index;
              }
            }
          ];
        }
        new Toast(toast_params);

        if(typeof window.promo113 != "undefined" && window.promo113 && typeof response.promo_alert != "undefined" && response.promo_alert) {
          const cartNotification = document.createElement('div')
          cartNotification.id = 'cartNotification'
          cartNotification.className = '!px-4 !py-6 sm:!py-[60px] sm:!px-6 w-full max-w-[547px]'
          cartNotification.style.display = 'none'
          cartNotification.innerHTML = `<div class="flex items-center justify-between">
              <h4 class="d-headline-4 m-headline-3 leading-none lh-outline-none cormorantInfant">${response.promo_alert}</h4>
              <button class="outline-none" onclick="Fancybox.close()" tabindex="-1"><img src="https://lemousse.shop/img/icons/close-circle.svg" alt="" class="w-6 h-6"></button>
            </div>`
          document.body.append(cartNotification)
          Fancybox.show(
            [
              {
                src: '#cartNotification'
              },
            ],
            {
              loop: false,
              touch: false,
              contentClick: false,
              dragToClose: false,
              closeButton: false,
              Toolbar: {
                display: {
                  left: [],
                  middle: [],
                  right: [],
                },
              },
            }
          );
        }
        if(handler){
          handler(response.cart);
        }
      }else{
        new Toast({
          message: response.message,
          type: 'danger'
        });
      }
    }else{
      alert('Что-то сломалось, попробуйте снова с другого устройства')
    }
    cartIsUpdating = false
    element.textContent = 'В корзину'
  });
}
const removeCart = (row_id, handler = null) => {
  if(cartIsUpdating){
    return false;
  }
  if(!confirm('Удалить товар из корзины?')){
    return false;
  }
  cartIsUpdating = true;
  var params = {
    row_id: row_id,
    _method: 'DELETE',
    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
  };
  window.ajax.post(window.cart.remove, params, (response) => {
    if (response != '') {
      if (response.success) {
        let toast_params = {
          message: response.message,
          type: 'success',
        };
        new Toast(toast_params);
      }else{
        new Toast({
          message: json.message,
          type: 'danger'
        });
      }
      // if (response.cartCount == 0 && window.location.pathname == '/order'){
      //   // window.location = window.location.href;
      // }
      if(handler){
        handler(response.cart);
      }
    }else{
      alert('Что-то сломалось, попробуйте снова с другого устройства')
    }
    cartIsUpdating = false
  });
}
// кнопка "добавить в корзину" в маленьких карточках
const toCartButtons = document.querySelectorAll('.toCart[data-id]')

toCartButtons.forEach((button) => {
  button.addEventListener('click', (event) => {
    event.currentTarget.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-loader block mx-auto" width="32" height="32" viewBox="0 0 24 24" stroke-width="1" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
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
    window.toCartHandler(event)
    Fancybox.close()
  })
})
window.toCartHandler = (event) => {
  event.preventDefault()
  const button = event.currentTarget
  const productId = button.dataset.id
  const qty = getQtyValueFromButton(button)
  var params = {
    id: productId,
    qty: qty,
    type: 'add',
    elem: event.currentTarget
  }
  if (button.hasAttribute('data-option')) {
    params.option = button.dataset.option;
  }
  if(button.hasAttribute('data-builder')){
    params.builder = builder;
  }
  updateCart(params);
}
const listenCartButtons = () => {
// кнопка "минус" на странице оформления заказа
  const productDecrease = document.querySelectorAll('.product-sub')
  productDecrease.forEach((button) => {
    button.addEventListener('click', quantityHandler)
  })
  // кнопка "плюс" на странице оформления заказа
  const productIncrease = document.querySelectorAll('.product-add')
  productIncrease.forEach((button) => {
    button.addEventListener('click', quantityHandler)
  })
  const productRemove = document.querySelectorAll('.product-remove')

  productRemove.forEach((button) => {
    button.addEventListener('click', removeHandler)
  })
}
listenCartButtons()

function removeHandler(event){
  event.preventDefault()
  const button = event.currentTarget
  const cartItem = button.closest('.cart-item')
  // const productId = cartItem.dataset.product
  const rowId = cartItem.dataset.rowId
  removeCart(rowId, refreshCart)
}
function quantityHandler(event){
  event.preventDefault()
  if(cartIsUpdating){
    return false
  }
  cartIsUpdating = true
  const button = event.currentTarget
  const cartItem = button.closest('.cart-item')
  const productId = cartItem.dataset.product
  if(!productId){
    return false;
  }
  let qty = Number(cartItem.dataset.quantity)
  if(button.classList.contains('product-add')){
    qty++
  }else if(button.classList.contains('product-sub')){
    qty--
  }
  var params = {
    id: productId,
    qty: qty,
    type: 'update'
  }
  updateCart(params, refreshCart);
}
function refreshCart(cartData){
  removeCartHandlers();
  createCartHtml(cartData)
  listenCartButtons()
}

function removeCartHandlers(){
  let table = document.getElementById('table-cart');
  const productDecrease = table.querySelectorAll('.product-sub')
  productDecrease.forEach((button) => {
    button.removeEventListener('click', quantityHandler)
  })
  // кнопка "плюс" на странице оформления заказа
  const productIncrease = table.querySelectorAll('.product-add')
  productIncrease.forEach((button) => {
    button.removeEventListener('click', quantityHandler)
  })
  const productRemove = document.querySelectorAll('.product-remove')
  productRemove.forEach((button) => {
    button.removeEventListener('click', removeHandler)
  })
}
let updateCartEvent = new CustomEvent('updateCart', {
  detail: { /* здесь можете передать дополнительные данные, если нужно */ },
  bubbles: true,
  cancelable: true
});
function createCartHtml(cartData){
  let table = document.getElementById('table-cart');
  table.innerHTML = ''

  if (Object.keys(cartData).length === 0) {
    table.innerHTML = '<div class="text-center text-2xl text-customBrown p-6">Корзина пуста</div>'
    document.getElementById('order-form').remove()
  }else{
    let cartTotal = 0,
      cartCount = 0

    for (let rowId in cartData) {
      let item = cartData[rowId];

      let is_gift = false
      if(item.price <= 1){
        is_gift = true
      }
      cartTotal += item.price * item.qty
      cartCount += item.qty

      let cartItem = document.createElement('div');
      cartItem.className = 'cart-item border-b border-black pb-6 mb-6'
      cartItem.dataset.price = item.price;
      if(!is_gift){
        cartItem.dataset.product = item.options.product_id;
      }

      cartItem.dataset.rowId = item.rowId;
      cartItem.dataset.shipping = item.options.shipping;
      cartItem.dataset.quantity = item.qty;


      let itemImage = `
        <div class="w-[86px] h-[86px] mr-4 md:mr-6"><div class="item-square">
          <img src="${item.options.image}" alt="${item.name}" class="object-bottom object-cover block"></div>
        </div>
      `
      let itemName = `
        <div class="cart-item-name-${rowId} flex justify-between flex-1 max-w-full">
          <div>
            <h3 class="text-2xl lg:text-32 font-light">${item.name}</h3>
            <div class="text-base lg:text-lg my-4">Артикул: ${item.options.sku}</div>
            <div class="subtitle-1 text-myBrown" data-da=".mobile-cart-info-${rowId},last,1023">${window.formatPrice(item.price * item.qty, true)}</div>
          </div>
        </div>
      `
      let itemQuantity = `
          <div class="flex items-center space-x-6">
            <div data-da=".mobile-cart-info-${rowId},first,1023"
              class="flex justify-between items-center border border-black border-1 w-auto h-11 md:h-14">
              <button class="product-sub bg-transparent border-0 outline-none p-3">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M3.5 7H10.5" stroke="#2C2E35" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round"/>
                </svg>
              </button>
              <div class="flex-1 text-center mx-2 whitespace-nowrap text-xl md:text-2xl"><span class="cormorantInfant">${item.qty}</span> шт.</div>
              <button class="product-add bg-transparent border-0 outline-none p-3"  data-field="productQty">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M3.5 7H10.5" stroke="#2C2E35" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round"/>
                  <path d="M7 10.5V3.5" stroke="#2C2E35" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round"/>
                </svg>
              </button>
            </div>
            <div data-da=".cart-item-name-${rowId},last,1023">
              <button type="button" tabindex="-1" class="product-remove text-center text-xl leading-none min-w-4 h-4 sm:min-w-5 md:min-w-6 max-w-4 h-4 sm:max-w-5 md:max-w-6 sm:h-5 md:h-6 flex justify-center items-center leading-none ml-5 lg:ml-6">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 mx-auto" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M2 2L22 22M2 22L22 2" stroke="#2C2E35" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </button>
            </div>
          </div>
      `
      if(is_gift){
        itemQuantity = `
          <div class="flex items-center space-x-6">
            <div data-da=".mobile-cart-info-${rowId},first,1023"
              class="flex justify-between items-center border border-black border-1 w-auto h-11 md:h-14">
              <button class="bg-transparent border-0 outline-none p-3" data-field="productQty" style="pointer-events: none; opacity: .3">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M3.5 7H10.5" stroke="#2C2E35" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round"/>
                </svg>
              </button>
              <div class="flex-1 text-center mx-2 whitespace-nowrap text-xl md:text-2xl"><span class="cormorantInfant">${item.qty}</span> шт.</div>
              <button class="bg-transparent border-0 outline-none p-3"  data-field="productQty" style="pointer-events: none; opacity: .3">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M3.5 7H10.5" stroke="#2C2E35" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round"/>
                  <path d="M7 10.5V3.5" stroke="#2C2E35" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round"/>
                </svg>
              </button>
            </div>
            <div data-da=".cart-item-name-${rowId},last,1023">
              <button type="button" tabindex="-1" class="product-remove text-center text-xl leading-none min-w-4 h-4 sm:min-w-5 md:min-w-6 max-w-4 h-4 sm:max-w-5 md:max-w-6 sm:h-5 md:h-6 flex justify-center items-center leading-none ml-5 lg:ml-6">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 mx-auto" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M2 2L22 22M2 22L22 2" stroke="#2C2E35" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </button>
            </div>
          </div>
      `
      }


      cartItem.innerHTML = `<div class="flex">${itemImage}
      <div class="flex-1 flex flex-col lg:flex-row justify-between lg:space-x-6">
      ${itemName}${itemQuantity}
      </div></div><div class="mt-4 lt:hidden flex justify-between items-center mobile-cart-info-${rowId}"></div>
      `
      table.appendChild(cartItem);
    }
    document.getElementById('cart-total').value = cartTotal
    document.getElementById('cart-count').value = cartCount
    window.getTotal()
    table.dispatchEvent(updateCartEvent);
  }
  window.updateDynamicAdapt();
  return true;
}



