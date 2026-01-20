export function trisFadeOut(elem, timeout = 300){
  elem.style.opacity = window.getComputedStyle(elem).getPropertyValue('opacity') ?? 1;
  elem.style.transition = `opacity ${timeout/1000}s ease`;
  elem.style.opacity = 0;
  if(elem.style.display !== 'none') {
    elem.addEventListener('transitionend', () => {
      setTimeout(() => {
        if(elem.style.opacity === '0'){
          elem.style.display = 'none';
          elem.style.opacity = null;
          elem.style.transition = null;
        }
      },10)
    }, { once: true });
  }
}
export function trisFadeIn(elem, timeout = 300){
  elem.style.display = null;
  elem.style.transition = `opacity ${timeout/1000}s ease`;
  elem.style.opacity = 0;
  setTimeout(()=>{
    elem.style.opacity = 1;
  },0)
}

export function countKeys(){
  let keyCounter = document.querySelector('.key-counter__current');
  if(keyCounter){
    let keyCounterVal = Number(keyCounter.innerText)
    keyCounter.innerText = keyCounterVal - 1
    trisFadeIn(document.querySelector('.key-counter'))
  }
}

