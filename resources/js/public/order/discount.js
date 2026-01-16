import {fetchData, formatPrice} from "../utilites.js";
import {getOrderTotal} from "../starter.js";

export class Discount{
  constructor() {
    this.method;
    this.discount = 0;
    this.discountElem;
    this.promocode;
    this.promocodeElem;
    this.promocodeField;
    this.promocodeMessage;
    this.voucher;
    this.voucherElem;
    this.voucherField;
    this.voucherMessage;
    this.userBonuses = 0;
    this.bonusesElem = null;
    this.routes = {};
    this.onChangePromocode = this.onChangePromocode.bind(this);
    this.onChangeVoucher = this.onChangeVoucher.bind(this);
    this.onInputPromocode = this.onInputPromocode.bind(this);
    this.onInputVoucher = this.onInputVoucher.bind(this);
    this.onChangeBonuses = this.onChangeBonuses.bind(this);
  }

  init(){
    const inputJsData = document.querySelectorAll('.js_data.discount_route');
    for (var i = 0;i<inputJsData.length;i++){
      let element = inputJsData[i];
      this.routes[element.id] = element.value;
      let parent = element.parentNode;
      parent.removeChild(element);
    }


    const promocodeElem = document.getElementById('promocode');
    if(promocodeElem){
      this.promocodeElem = promocodeElem;
      promocodeElem.addEventListener('input', this.onInputPromocode);
      promocodeElem.addEventListener('change', this.onChangePromocode);
    }
    const promocodeField = document.getElementById('box-field-promocode');
    if(promocodeField){
      this.promocodeField = promocodeField;
    }
    const voucherElem = document.getElementById('voucher');
    if(voucherElem){
      this.voucherElem = voucherElem;
      voucherElem.addEventListener('input', this.onInputVoucher);
      voucherElem.addEventListener('change', this.onChangeVoucher);
    }
    const voucherField = document.getElementById('box-field-voucher');
    if(voucherField){
      this.voucherField = voucherField;
    }
    const bonusesElem = document.getElementById('bonuses');
    if(bonusesElem){
      this.bonusesElem = bonusesElem;
      const userBonuses = document.getElementById('user-bonuses');
      if(userBonuses) {
        this.userBonuses = parseInt(userBonuses.value);
        bonusesElem.addEventListener('change', this.onChangeBonuses);
      }


    }
  }

  setDiscount(discount){
    if(!(!isNaN(parseFloat(discount)) && isFinite(discount) && /^[-+]?\d*\.?\d+$/.test(discount))) discount = 0;

    this.discount = parseInt(discount);
    if(this.discount > 0){
      if(this.discountElem) this.discountElem.remove();
      let orderElement = document.getElementById('order');
      let discountElem = document.createElement('input');
      discountElem.type = 'hidden';
      discountElem.name = 'discount';
      discountElem.id = 'items-discount';
      discountElem.value = this.discount;
      orderElement.prepend(discountElem);
      this.discountElem = discountElem;
    }
  }
  setPromocode(promocode){
    this.promocode = promocode;
    if(!this.promocodeElem) return;
    if(!this.promocodeElem.value) this.promocodeElem.value = promocode;
  }
  setVoucher(voucher){
    this.voucher = voucher;
    if(!this.voucherElem) return;
    if(!this.voucherElem.value) this.voucherElem.value = voucher;
  }

  onChangePromocode(e){
    this.setPromocode(this.promocodeElem.value);
    this.checkDiscount()
  }
  onChangeVoucher(e){
    this.setVoucher(this.voucherElem.value);
    this.checkDiscount()
  }
  onChangeBonuses(e){
    if(this.method !== 'bonuses') this.method = 'bonuses';
    this.resetDiscount();
    if(this.userBonuses < 1){
      return false;
    }
    let cartTotal = Number(document.getElementById('cart-total').value)
    let shippingPrice = Number(document.getElementById('shipping-price').value)
    let discount;
    if (shippingPrice + cartTotal > this.userBonuses) {
      discount = Number(this.userBonuses);
    } else {
      discount = shippingPrice + cartTotal - 1;
    }
    this.setDiscount(discount);
    getOrderTotal();
  }
  onInputPromocode(e){
    if(this.method !== 'promocode') this.method = 'promocode';
    this.resetDiscount();
    this.promocode = this.promocodeElem.value;
  }
  onInputVoucher(e){
    if(this.method !== 'voucher') this.method = 'voucher';
    this.resetDiscount();
    this.voucher = this.voucherElem.value;
  }
  checkDiscount(){
    const route = this.routes[`route_check_${this.method}`];
    const params = {
      _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };
    if(this.method === 'promocode'){
      if(!this.promocode){
        return false;
      }
      params.promocode = this.promocode;
    }else if(this.method === 'voucher'){
      if(!this.voucher){
        return false;
      }
      params.voucher = this.voucher;
    }
    fetchData(route, 'POST', params).then(response => {
      console.log('response', response)
      if(!response) alert('Ошибка, попробуйте позже');
      if(response.error){
        this.createMessage(response.error, 'red');
        return false;
      }
      this.resetDiscount();
      let discount;
      if(this.method==='promocode'){
        let cartTotal = Number(document.getElementById('cart-total').value);
        let totalForDiscount = response.total_for_discount;
        document.getElementById('total_for_discount').value = totalForDiscount;
        if (totalForDiscount > response.promocode_discount) {
          discount = response.promocode_discount;
        } else {
          if (totalForDiscount < cartTotal) {
            discount = totalForDiscount;
          } else {
            discount = totalForDiscount - 1;
          }
        }
        if(!(discount > 0)){
          return false;
        }
        this.promocodeElem.style.color = 'green';
      }else if(this.method==='voucher'){
        let cartTotal = Number(document.getElementById('cart-total').value)
        let shippingPrice = Number(document.getElementById('shipping-price').value)
        if (shippingPrice + cartTotal > response.voucher_discount) {
          discount = response.voucher_discount;
        } else {
          discount = shippingPrice + cartTotal - 1;
        }
        if(!(discount > 0)){
          return false;
        }
        this.voucherElem.style.color = 'green';
      }
      this.setDiscount(discount);
      getOrderTotal();
    }).catch(error => {
      console.log(error);
      alert(`Ошибка проверки скидки. ${error}`);
    });
  }
  createMessage(text, color){
    let field;
    if(this.method === 'promocode'){
      field = this.promocodeField;
    }
    if(this.method === 'voucher'){
      field = this.voucherField;
    }
    const message = document.createElement('div')
    message.id = `${this.method}-message`
    message.style.color = color
    message.innerHTML = text
    field.parentNode.appendChild(message);
    if(color === 'red'){
      field.querySelector('input').style.color = ''
    }
    if(this.method === 'promocode'){
      this.promocodeMessage = message;
    }else if(this.method === 'voucher'){
      this.voucherMessage = message;
    }
  }
  resetDiscount(){
    this.promocodeElem.style.color = null;
    this.voucherElem.style.color = null;
    if(this.voucherMessage) this.voucherMessage.remove();
    if(this.promocodeMessage) this.promocodeMessage.remove();
    if(this.discountElem) this.discountElem.remove();
    if(this.method !== 'promocode'){
      this.promocodeElem.value = '';
    }
    if(this.method !== 'voucher'){
      this.voucherElem.value = '';
    }
    if(this.method !== 'bonuses' && this.bonusesElem){
      this.bonusesElem.checked = false;
    }
    const discountInfo = document.getElementById('discount-info');
    if(discountInfo){
      discountInfo.remove();
    }
    const discount = this.discount;
    this.setDiscount(0);
    if(discount){
      getOrderTotal();
    }
  }
}
