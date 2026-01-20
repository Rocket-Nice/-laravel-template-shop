import {HappyCoupon} from "./coupons.js";

document.addEventListener('DOMContentLoaded', () => {
  if(document.querySelector('.coupones-grid')){
    window.happyCoupon = new HappyCoupon();
  }
});
