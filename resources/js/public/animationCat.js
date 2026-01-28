// Импортируем GSAP и плагины
import gsap from 'gsap';
import { Flip } from 'gsap/Flip';

gsap.registerPlugin(Flip);
export { gsap, Flip };

const startBtn = document.querySelector(".start-btn");
const sacksContainer = document.querySelector(".sacks-animation-container");
const sackCards = Array.from(document.querySelectorAll(".sack-card"));
const subtitle = document.querySelector(".subtitle");
const infoText = document.querySelector(".info-text");
const prizeContainer = document.querySelector(".sack-price-container");
const prizeCards = document.querySelector(".sack-price-cards");

const openedCards = new Set();

let isNextMode = false;
let currentOpenedCard = null;

if (sackCards.length === 4) {
  sacksContainer.classList.add("has-four-cards");
}

// тут меняю на серые мешки, если нужна плавность, то всё через css и тут заменить логику по применению стилей на add() remove()
function changeSacksImages() {
  sackCards.forEach((card) => {
    const img = card.querySelector("img");
    img.src = "/img/cat-bag/animation-sack/sack.png";
    img.alt = "sack";
  });
}

// порядок анимации, настроил его (не трогать)
const SHUFFLE_SEQUENCES = {
  3: [
    [1, 2, 0],
    [1, 0, 2],
    [0, 1, 2],
    [1, 0, 2],
    [1, 2, 0],
    [0, 1, 2],
  ],
  4: [
    // 4 мешка
    [2, 0, 3, 1],
    [1, 2, 3, 0],
    [2, 3, 1, 0],
    [3, 2, 1, 0],
    [0, 1, 2, 3],
  ],
};

function shuffleRound(orderPattern) {
  return new Promise((resolve) => {
    const state = Flip.getState(sackCards);

    sackCards.forEach((card, index) => {
      card.style.order = orderPattern[index];
    });

    Flip.from(state, {
      duration: 0.6,
      ease: "power2.inOut",
      scale: true,
      onComplete: resolve,
    });
  });
}

// тут создаём карточку приза
function createPrizeCard(imageUrl, productText) {
  const prizeCard = document.createElement("div");
  prizeCard.className = "prize-card";

  //  Макс, вот тут можно заменить на нужные тебе данные
  prizeCard.innerHTML = `
    <img src="${imageUrl}" alt="prize">
    <p class="prize-text">${productText}</p>
  `;

  return prizeCard;
}

function openSack(event) {
  const card = event.currentTarget;
  const img = card.querySelector("img");

  openedCards.add(card);

  sackCards.forEach((c) => {
    c.style.pointerEvents = "none";
    c.style.cursor = "default";
  });

  sackCards.forEach((c) => {
    if (c !== card) {
      c.style.display = "none";
    }
  });

  card.classList.add("expanded-card");
  // вот тут смотри по маршрутам гифки, если что сам переделаешь
  img.src = "/img/cat-bag/animation/animation.gif";

  setTimeout(() => {
    card.classList.remove("expanded-card");
    card.classList.add("product-card");

    // тут также
    img.src = "/img/cat-bag/animation-sack/product.png";

    // вот тут если нужно тяни данные с бэка
    card.dataset.productImage = "/img/cat-bag/animation-sack/product.png";
    // вот тут если нужно тяни данные с бэка

    subtitle.textContent = "Ваша удача в акции";

    startBtn.textContent = "Далее";
    startBtn.classList.remove("hidden");
    startBtn.disabled = false;

    isNextMode = true;
    currentOpenedCard = card;
    // вот тут задержка на время проигрывания гифки
  }, 4000);
}

function returnToSelection(openedCard) {
  startBtn.classList.add("hidden");

  isNextMode = false;
  currentOpenedCard = null;

  subtitle.textContent = "Выберите мешок и испытайте удачу!";

  sackCards.forEach((c) => {
    c.style.display = "block";

    if (openedCards.has(c)) {
      c.classList.add("opened-sack");
      c.style.pointerEvents = "none";
    } else {
      c.style.pointerEvents = "auto";
      c.style.cursor = "pointer";
    }
  });

  openedCard.classList.remove("product-card");

  const img = openedCard.querySelector("img");
  img.src = "/img/cat-bag/animation-sack/sack.png";

  const productText = openedCard.querySelector(".sack-product-text");
  if (productText) {
    productText.style.display = "none";
  }
  // тут тоже смотри
  const imageUrl = img.src;
  const text = productText ? productText.textContent : "Подарок";

  // тут тоже смотри
  const productImageUrl =
    openedCard.dataset.productImage ||
    "/img/cat-bag/animation-sack/product.png";

  const prizeCard = createPrizeCard(productImageUrl, text);
  prizeCards.appendChild(prizeCard);

  // тут показ контейнера
  prizeContainer.classList.add("visible");
}

async function shuffleSacks() {
  startBtn.disabled = true;
  startBtn.classList.add("hidden");

  sacksContainer.classList.add("animating");
  changeSacksImages();

  await new Promise((resolve) => setTimeout(resolve, 400));

  const sequence = SHUFFLE_SEQUENCES[sackCards.length];

  for (let i = 0; i < sequence.length; i++) {
    await shuffleRound(sequence[i]);
  }
  await new Promise((resolve) => setTimeout(resolve, 200));

  sacksContainer.classList.remove("animating");

  subtitle.textContent = "Выберите мешок и испытайте удачу!";

  infoText.classList.add("visible");

  sackCards.forEach((card) => {
    card.addEventListener("click", openSack);
    card.style.cursor = "pointer";
  });
}

startBtn.addEventListener("click", () => {
  if (isNextMode) {
    returnToSelection(currentOpenedCard);
  } else {
    shuffleSacks();
  }
});
