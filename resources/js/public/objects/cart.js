import {formatPrice, ymGoal} from "../utilites.js"
import {calculateShippingPrice, getOrderTotal} from "../starter.js";
export class Cart {
  constructor() {
    this.count;
    this.total;
    this.totalForDiscount;
    this.items = [];
    this.cartIsUpdating = false;
    this.onCartHandler = this.onCartHandler.bind(this);
    this.refreshCart = this.refreshCart.bind(this);
  }

  init(){
    const totalForDiscount = document.getElementById('total_for_discount');
    if(totalForDiscount) this.totalForDiscount = Number(totalForDiscount);
    this.fetchCartData(window.cart.init);
    this.addButtonListeners();
  }

  // обновляем данные корзины
  async fetchCartData(url, method = 'GET', data = null, handler = null) {
    this.cartIsUpdating = true;
    try {
      let response_params = {
        method,
        headers: {
          'Content-Type': 'application/json',
        },
      };
      if (data) response_params.body = JSON.stringify(data);

      const response = await fetch(url, response_params);

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const result = await response.json();
      this.items = result.cart;
      this.count = parseInt(result.cartCount);
      if(this.count === 0){
        localStorage.removeItem('deadTime');
      }
      this.total = parseInt(result.total);
      if(result.message){
        let toast_params = {
          message: result.message.text,
          type: result.message.type,
        };
        if (toast_params.type == 'success' && result.cartCount > 0 && window.location.pathname !== '/order'){
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
        if(typeof window.promo113 != "undefined" && window.promo113 && typeof result.promo_alert != "undefined" && result.promo_alert) {
          const cartNotification = document.createElement('div')
          cartNotification.id = 'cartNotification'
          cartNotification.className = '!px-4 !py-6 sm:!py-[60px] sm:!px-6 w-full max-w-[547px]'
          cartNotification.style.display = 'none'
          cartNotification.innerHTML = `<div class="flex items-center justify-between">
              <h4 class="d-headline-4 m-headline-3 leading-none lh-outline-none cormorantInfant">${result.promo_alert}</h4>
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
      }
      if(handler){
        handler(result)
      }
      getOrderTotal();
      document.querySelectorAll('.cart-counter').forEach(item => item.innerText = this.count);
      this.cartIsUpdating = false;
      // ym goals
      if(data && data.cart_data.type === 'add'){
        ymGoal('add_to_cart');
      }
      return result;
    } catch (error) {
      if(data && data.cart_data.type === 'add'){
        ymGoal('err_add_to_cart');
      }
      throw error;
    }
  }

  changeCart(itemData, handler = null) {
    var params = {
      cart_data: itemData,
      _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };
    this.fetchCartData(window.cart.update, 'POST', params, handler);
  }
  removeCartItem(rowId, handler = null) {
    var params = {
      row_id: rowId,
      _method: 'DELETE',
      _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };
    this.fetchCartData(window.cart.remove, 'POST', params, handler);
  }

  addButtonListeners() {
    // кропка "добавить в корзину" в карточке товара
    const toCartButtons = document.querySelectorAll('.toCart[data-id]')
    toCartButtons.forEach((button) => {
      button.addEventListener('click', this.onCartHandler)
      // console.log('add', button)
    })
    // выбор количества из select
    const productQty = document.querySelectorAll('.product-qty')
    productQty.forEach((button) => {
      button.addEventListener('change', this.onCartHandler)
    })
    // кнопка "минус" на странице оформления заказа
    const productDecrease = document.querySelectorAll('.product-sub')
    productDecrease.forEach((button) => {
      button.addEventListener('click', this.onCartHandler)
    })
    // кнопка "плюс" на странице оформления заказа
    const productIncrease = document.querySelectorAll('.product-add')
    productIncrease.forEach((button) => {
      button.addEventListener('click', this.onCartHandler)
    })
    const productRemove = document.querySelectorAll('.product-remove')
    productRemove.forEach((button) => {
      button.addEventListener('click', this.onCartHandler)
    })
  }
  removeButtonListeners() {
    // кропка "добавить в корзину" в карточке товара
    const toCartButtons = document.querySelectorAll('.toCart[data-id]')
    toCartButtons.forEach((button) => {
      button.removeEventListener('click', this.onCartHandler)
      // console.log('remove', button)
    })
    // выбор количества из select
    const productQty = document.querySelectorAll('.product-qty')
    productQty.forEach((button) => {
      button.removeEventListener('change', this.onCartHandler)
    })
    // кнопка "минус" на странице оформления заказа
    const productDecrease = document.querySelectorAll('.product-sub')
    productDecrease.forEach((button) => {
      button.removeEventListener('click', this.onCartHandler)
    })
    // кнопка "плюс" на странице оформления заказа
    const productIncrease = document.querySelectorAll('.product-add')
    productIncrease.forEach((button) => {
      button.removeEventListener('click', this.onCartHandler)
    })
    const productRemove = document.querySelectorAll('.product-remove')
    productRemove.forEach((button) => {
      button.removeEventListener('click', this.onCartHandler)
    })
  }
  onCartHandler(e){
    e.preventDefault();
    if(this.cartIsUpdating){
      return false;
    }
    let method;
    let productId;
    let qty;
    let handler = null;
    const button = e.currentTarget;

    if(button.classList.contains('toCart')){
      method = 'add';
      productId = button.dataset.id;
      qty = this.getQtyValueFromButton(button);
    }else if(button.classList.contains('product-remove')){
      const cartItem = button.closest('.cart-item')
      const rowId = cartItem.dataset.rowId
      if(window.location.pathname === '/order') handler = this.refreshCart;
      this.removeCartItem(rowId, handler);
      return false;
    }else{
      method = 'update';
      const cartItem = button.closest('.cart-item')
      productId = cartItem.dataset.product
      qty = Number(cartItem.dataset.quantity)
      if(button.classList.contains('product-add')){
        qty++
      }else if(button.classList.contains('product-sub')){
        qty--
      }else if(button.classList.contains('product-qty')){
        qty = Number(button.value)
      }
      if(window.location.pathname === '/order') handler = this.refreshCart;
    }
    var params = {
      id: productId,
      qty: qty,
      type: method
    }
    if (button.hasAttribute('data-option')) {
      params.option = button.dataset.option;
    }
    this.changeCart(params, handler);
  }

  // обновить корзину на странице оформления заказа
  refreshCart() {
    this.removeButtonListeners();
    let table = document.getElementById('table-cart');
    if(!table){
      return false;
    }
    table.innerHTML = ''

    if (this.count === 0) {
      table.innerHTML = '<div class="text-center text-2xl text-myDark p-6">Корзина пуста</div>';
      document.getElementById('order-form').remove();
      return false;
    }
    let totalForDiscount = 0;
    for (let rowId in this.items) {
      let item = this.items[rowId];
      let is_gift = false;
      if (item.price <= 1) {
        is_gift = true;
      }else{
        if (!item.options ||
          !Object.prototype.hasOwnProperty.call(item.options, 'discount_does_not_apply') ||
          !!item.options.discount_does_not_apply === false) {
          totalForDiscount += item.price * item.qty;
        }
      }


      let cartItem = document.createElement('div');
      cartItem.className = 'cart-item border-b border-black pb-6 mb-6'
      cartItem.dataset.price = item.price;

      if (!is_gift) {
        cartItem.dataset.product = item.options.product_id;
      }
      cartItem.dataset.rowId = item.rowId;
      cartItem.dataset.shipping = item.options.shipping;
      cartItem.dataset.quantity = item.qty;

      let itemImage = `
        <div class="w-[86px] mr-4 md:mr-6"><div class="item-square">
          <img src="${item.options.image}" alt="${item.name}" class="object-bottom object-cover block"></div>
        </div>
      `
      let subtitle = '';

      if((item.options.subtitle ?? null) && item.options.subtitle !== "null"){
        subtitle = `<div class="text-myBrown d-text-body mt-2">${item.options.subtitle}</div>`;
      }
      let itemName = `
        <div class="cart-item-name-${rowId} flex justify-between flex-1 max-w-full">
          <div>
            <h3 class="text-2xl lg:text-32 font-light">${item.name}</h3>${subtitle}
            <div class="text-base lg:text-lg my-4">Артикул: ${item.options.sku}</div>
            ${item.options.old_price ? `<div data-da=".mobile-cart-info-${rowId},first,1023" class="text-base sm:text-md md:text-lg cormorantInfant italic font-semibold text-myGray line-through">${window.formatPrice(item.options.old_price * item.qty, true)}</div>` : ''}
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
      if (is_gift) {
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
      </div></div><div class="mt-4 lt:hidden flex justify-between items-center mobile-cart-info-${rowId}"></div>`
      table.appendChild(cartItem);
    }
    if (window.promo20) {
      if(this.total >= 3599){
        let cartItem = document.createElement('div');
        cartItem.className = 'border-b border-black pb-6 mb-6'
        cartItem.innerHTML = `<div class="flex">
                      <div class="w-[86px] mr-4 md:mr-6">
                        <div class="item-square bg-springGreen">
                          <div class="flex justify-center-items-center p-4">
                            <img src="/img/happy_coupon/cabinet-gift.png" class="object-bottom object-cover block">
                          </div>
                        </div>
                      </div>
                      <div class="flex-1 flex flex-col lg:flex-row justify-between lg:space-x-6">
                        <div class="flex justify-between flex-1 max-w-full">
                          <div>
                            <h3 class="text-2xl lg:text-32 font-light">Подарок из ассортимента Le&nbsp;Mousse</h3>
                          </div>
                        </div>
                        <div class="flex items-center space-x-6">

                        </div>
                      </div>
                    </div>`
        table.appendChild(cartItem);
      }
      let need_to_keys = '';
      if (this.total < 3599) {
        need_to_keys = `Пополните корзину на <span class="font-bold cormorantInfant">${window.formatPrice(3599 - this.total)}</span>, чтобы участвовать в акции`;
      } else{
        need_to_keys = '';
      }
      document.getElementById('coupons-info').innerHTML = need_to_keys;
      if(need_to_keys === ''){
        document.getElementById('coupons-info').style.display = 'none';
      }else{
        document.getElementById('coupons-info').style.display = 'block';
      }
    }else{ //  if(window.promo30)
      let need_to_keys = '';
      // if (this.count < 3) {
      //   need_to_keys = `Добавьте ещё ${denum(3 - this.count, ['<span style="font-style:italic;line-height:0;font-size:2.5em;">%d</span> позицию', '<span style="font-style:italic;line-height:0;font-size:2.5em;">%d</span> позиции', '<span style="font-style:italic;line-height:0;font-size:2.5em;">%d</span> позиций'])} в корзину и получите бесплатную доставку!`;
      // } else{
      //   need_to_keys = 'Ваша доставка будет бесплатной!*';
      // }
      // document.getElementById('coupons-content').innerHTML = need_to_keys;
      if(need_to_keys === ''){
        document.getElementById('coupons-info').style.display = 'none';
      }else{
        document.getElementById('coupons-info').style.display = 'block';
      }
    }
    document.getElementById('cart-total').value = this.total;
    document.getElementById('cart-count').value = this.count;
    document.getElementById('total_for_discount').value = totalForDiscount;
    calculateShippingPrice();
    getOrderTotal();
    table.dispatchEvent(new CustomEvent('updateCart', {
      detail: {},
      bubbles: true,
      cancelable: true
    }));
    this.addButtonListeners();
    window.updateDynamicAdapt();
  }

  // получить количество из кнопки "добавить в корзину" или из input с количеством, если он есть
  getQtyValueFromButton(button) {
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
}
