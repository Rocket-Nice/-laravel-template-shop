import {Cart} from "./objects/cart.js";
import {Shipping} from "./order/shipping.js";
import {Discount} from "./order/discount.js";
import {generatePictureElements, draggable, formatPrice, ymGoal} from "./utilites.js"

const cart = new Cart();
const shipping = new Shipping();
const discountObject = new Discount();
window.starter = () => {
  cart.init();
  draggable();
  generatePictureElements();
  // window.dynamicAdapt();
  if(window.location.pathname === '/order'){
    discountObject.init();
    shipping.init();

    const orderForm = document.getElementById('order-form')
    if(orderForm){
      orderForm.addEventListener('input', function(event) {
        let elem = event.target;
        if(elem.tagName.toLowerCase() === 'input') {
          localStorage.setItem(elem.name, elem.value);
        }
      });
    }
    var inputs = document.querySelectorAll('#order-form input');

    inputs.forEach(function(input) {
      var name = input.getAttribute('name');
      if (localStorage.getItem(name) !== null && input.value === '' && input.name !== 'promocode' && input.name !== 'voucher') {
        input.value = localStorage.getItem(name);
      }
    });
   
  }
}
window.listenCart = () => {
  cart.removeButtonListeners();
  cart.addButtonListeners();
}
document.addEventListener('DOMContentLoaded', function() {
  window.starter();
  const sizeInputs = document.querySelectorAll('input[name="size"].product-option');
  let selectedSize = null;

  sizeInputs.forEach(input => {
    input.addEventListener('change', (e) => {
      if (e.target.checked) {
        selectedSize = e.target.value;
        const button = document.querySelector(e.currentTarget.dataset.button)
        if(button){
          button.dataset.option = selectedSize
        }
      }
    });
  });
});


export function getOrderTotal(){
  if(window.location.pathname !== '/order') return false;
  const discountInfo = document.getElementById('discount-info');
  if(discountInfo){
    discountInfo.remove();
  }
  const cartTotal = cart.total;
  let shippingPrice = shipping.price;

  let discount = discountObject.discount; // общая скидка
  let discounted = 0; // часть оставшейся скидки, для расчетов стомости доставки и товаров
  let getTotalPrice = cartTotal; //

  if(discount > 0){
    const orderTotalInfo = document.getElementById('order-total-info');
    if(discountObject.method === 'promocode'){
      if (getTotalPrice > discount) {
        getTotalPrice = getTotalPrice - discount;
      } else {
        discount = getTotalPrice - 1;
        getTotalPrice = 1;
      }
      const discountInfo = document.createElement('tr')
      discountInfo.id = 'discount-info'
      discountInfo.innerHTML = `
        <td class="text-left border-b border-black py-4">Промокод</td>
        <td class="border-b border-black py-4 text-right"><span class="subtitle-1 text-myBrown cormorantInfant">-${formatPrice(discount)}</span></td>
      `
      orderTotalInfo.parentNode.insertBefore(discountInfo, orderTotalInfo);
    }else if(discountObject.method === 'voucher'){
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
        <td class="text-left border-b border-black py-4">Подарочный сертификат</td>
        <td class="border-b border-black py-4 text-right"><span class="subtitle-1 text-myBrown cormorantInfant">-${formatPrice(discount)}</span></td>
      `
      orderTotalInfo.parentNode.insertBefore(discountInfo, orderTotalInfo);
    }else if(discountObject.method === 'bonuses'){
      if (getTotalPrice > discount) {
        getTotalPrice = getTotalPrice - discount;
      } else {
        discount = getTotalPrice - 1;
        getTotalPrice = 1;
      }
      const discountInfo = document.createElement('tr')
      discountInfo.id = 'discount-info'
      discountInfo.innerHTML = `
        <td class="text-left border-b border-black py-4">Бонусные баллы</td>
        <td class="border-b border-black py-4 text-right"><span class="subtitle-1 text-myBrown cormorantInfant">-${formatPrice(discount)}</span></td>
      `
      orderTotalInfo.parentNode.insertBefore(discountInfo, orderTotalInfo);
    }

    const orderTotal = getTotalPrice + shippingPrice;
    if(!(!isNaN(parseFloat(orderTotal)) && isFinite(orderTotal) && /^[-+]?\d*\.?\d+$/.test(orderTotal))) return false;
    const orderAmount = document.getElementById('order-amount');
    if(orderAmount) orderAmount.innerHTML = formatPrice(orderTotal);
  }
  document.getElementById('cart-total-info').innerHTML = formatPrice(cartTotal)
  document.getElementById('order-amount').innerHTML = formatPrice(getTotalPrice + shippingPrice)
}
