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
const sackCatWrapper = document.querySelector(".sack-cat-wrapper");

const openedCards = new Set();

let isNextMode = false;
let currentOpenedCard = null;

if (sackCards.length === 4) {
  sacksContainer.classList.add("has-four-cards");
}

function isMobileDevice() {
  return window.innerWidth <= 600;
}

function preloadPrizeImage(imageUrl) {
  const img = new Image();
  img.src = imageUrl;
}

function changeSacksImages() {
  sackCards.forEach((card) => {
    const img = card.querySelector("img");
    img.src = "/img/cat-bag/animation-sack/sack.svg";
    img.alt = "sack";
  });
}

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
    [2, 0, 3, 1],
    [1, 2, 3, 0],
    [2, 3, 1, 0],
    [3, 2, 1, 0],
    [0, 1, 2, 3],
  ],
};

function getTriangleCenter(cards) {
  const rects = cards.map(c => c.getBoundingClientRect());

  const centerX =
    (rects[0].left + rects[1].right) / 2;

  const centerY =
    (rects[0].top + rects[2].bottom) / 2;

  return { x: centerX, y: centerY };
}

async function shuffleRound(orderPattern, isCenterPosition = false) {
  if (!isCenterPosition) {
    return new Promise(resolve => {
      const state = Flip.getState(sackCards);

      sackCards.forEach((card, i) => {
        card.style.order = orderPattern[i];
      });

      Flip.from(state, {
        duration: 0.6,
        ease: "power2.inOut",
        scale: true,
        onComplete: resolve
      });
    });
  }

  const center = getTriangleCenter(sackCards);

  const offsets = sackCards.map(card => {
    const r = card.getBoundingClientRect();
    return {
      x: center.x - (r.left + r.width / 2),
      y: center.y - (r.top + r.height / 2)
    };
  });

  await gsap.to(sackCards, {
    x: i => offsets[i].x,
    y: i => offsets[i].y,
    duration: 0.4,
    ease: "power2.inOut"
  });

  await new Promise(r => setTimeout(r, 600));
  await gsap.to(sackCards, {
    x: 0,
    y: 0,
    duration: 0.4,
    ease: "power2.inOut"
  });

  return new Promise(resolve => {
    const state = Flip.getState(sackCards);

    sackCards.forEach((card, i) => {
      card.style.order = orderPattern[i];
    });

    Flip.from(state, {
      duration: 0.6,
      ease: "power2.inOut",
      scale: true,
      onComplete: resolve
    });
  });
}

function createPrizeCard(imageUrl, productText) {
  const prizeCard = document.createElement("div");
  prizeCard.className = "prize-card";

  const truncatedText = truncateText(productText, 2);

  prizeCard.innerHTML = `
    <img src="${imageUrl}" alt="prize">
    <p class="prize-text" title="${productText}">${truncatedText}</p>
  `;

  return prizeCard;
}

function truncateText(text, maxLines) {
  const words = text.split(' ');
  let result = '';
  let lineCount = 0;

  for (let word of words) {
    const testText = result + (result ? ' ' : '') + word;
    const tempElement = document.createElement('div');
    tempElement.style.cssText = `
      position: absolute;
      visibility: hidden;
      font-size: 14px;
      line-height: 1.4;
      width: 100%;
      max-width: 366px;
      word-wrap: break-word;
    `;
    tempElement.textContent = testText;
    document.body.appendChild(tempElement);

    const height = tempElement.offsetHeight;
    const lineHeight = 14 * 1.4;
    const currentLines = Math.ceil(height / lineHeight);

    document.body.removeChild(tempElement);

    if (currentLines <= maxLines) {
      result = testText;
    } else {
      if (lineCount < maxLines) {
        result = testText;
        lineCount++;
      } else {
        break;
      }
    }
  }

  return result + (result.length < text.length ? '...' : '');
}

function openSack(event) {
  const card = event.currentTarget;
  const img = card.querySelector("img");

  const prizeImageUrl = "/img/cat-bag/animation-sack/product.png";
  preloadPrizeImage(prizeImageUrl);

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

  if (isMobileDevice()) {
    sackCatWrapper.style.width = "auto";
  }

  card.classList.add("expanded-card");
  img.src = "/img/cat-bag/animation/animation.gif";

  const hiddenPrizeImg = document.createElement('img');
  hiddenPrizeImg.src = prizeImageUrl;
  hiddenPrizeImg.style.cssText = `
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    z-index: -1;
    object-fit: contain;
  `;
  card.appendChild(hiddenPrizeImg);

  setTimeout(() => {
    gsap.to(img, {
      opacity: 0,
      duration: 0.3,
      onComplete: () => {
        img.src = prizeImageUrl;
        img.style.opacity = 0;

        gsap.to(img, {
          opacity: 1,
          duration: 0.5,
          ease: "power2.inOut"
        });

        if (hiddenPrizeImg.parentNode) {
          hiddenPrizeImg.remove();
        }

        card.classList.remove("expanded-card");
        card.classList.add("product-card");

        card.dataset.productImage = prizeImageUrl;

        const productTextElement = card.querySelector(".sack-product-text");
        if (productTextElement) {
          const originalText = productTextElement.textContent;
          const truncatedText = truncateText(originalText, 2);
          productTextElement.textContent = truncatedText;
          productTextElement.title = originalText;
        }

        if (isMobileDevice()) {
          sackCatWrapper.style.width = "100%";
        }

        subtitle.textContent = "Ваша удача в акции";

        startBtn.textContent = "Далее";
        startBtn.classList.remove("hidden");
        startBtn.disabled = false;

        isNextMode = true;
        currentOpenedCard = card;
      }
    });
  }, 3800);
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
  img.src = "/img/cat-bag/animation-sack/sack.svg";

  const productText = openedCard.querySelector(".sack-product-text");
  if (productText) {
    productText.style.display = "none";
  }

  const imageUrl = img.src;
  const text = productText ? productText.title || productText.textContent : "Подарок";
  const productImageUrl = openedCard.dataset.productImage || "/img/cat-bag/animation-sack/product.png";

  const prizeCard = createPrizeCard(productImageUrl, text);
  prizeCards.appendChild(prizeCard);

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
    const isCenterPosition = (sackCards.length === 3 && i === 3) ||
      (sackCards.length === 4 && i === 3);

    await shuffleRound(sequence[i], isCenterPosition);
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

sackCards.forEach(card => {
  const productText = card.querySelector(".sack-product-text");
  if (productText) {
    const originalText = productText.textContent;
    const truncatedText = truncateText(originalText, 2);
    productText.textContent = truncatedText;
    productText.title = originalText;
  }
});