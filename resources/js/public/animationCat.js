import gsap from 'gsap';
import { Flip } from 'gsap/Flip';

gsap.registerPlugin(Flip);
export { gsap, Flip };

const startBtn = document.querySelector(".start-btn");
const sacksContainer = document.querySelector(".sacks-animation-container");
if (startBtn && sacksContainer) {
  let sackCards = Array.from(document.querySelectorAll(".sack-card"));
  const subtitle = document.querySelector(".subtitle");
  const infoText = document.querySelector(".info-text");
  const prizeContainer = document.querySelector(".sack-price-container");
  const prizeCards = document.querySelector(".sack-price-cards");
  const sackCatWrapper = document.querySelector(".sack-cat-wrapper");
  const cabinetBtn = document.querySelector(".cabinet-btn");
  const catContainer = document.querySelector(".cat-container");
  const orderId = catContainer?.dataset?.orderId || null;
  const orderSlug = catContainer?.dataset?.orderSlug || null;
  const orderPath = orderId
    ? `/cat-in-bag/order/${orderId}`
    : (orderSlug ? `/cat-in-bag/${orderSlug}` : null);
  const openLimitAttr = catContainer?.dataset?.openLimit;
  const storageKey = orderId ? `cat-bags-assignment-${orderId}` : null;
  const prizesStorageKey = orderId ? `cat-bags-prizes-${orderId}` : null;
  let assignedImages = null;
  let hasShuffled = false;
  let sessionBags = [];
  let sessionLoaded = false;
  let sessionLoading = null;
  let lastSessionData = null;
  let prizeHistory = [];

  let categoryImages = (() => {
    try {
      return JSON.parse(catContainer?.dataset?.categoryImages || "[]");
    } catch (e) {
      return [];
    }
  })();
  const catBagImage = "/img/cat-bag/gift-cat.png";
  const goldenBagImage = "/img/cat-bag/gift-gold.png";
  const fallbackBagImage = "/img/cat-bag/animation-sack/sack.svg";
  const prizePlaceholderImage = "/img/cat-bag/animation-sack/product.svg";
  const preSackImages = [
    "/img/cat-bag/animation-sack/pre-sack1.png",
    "/img/cat-bag/animation-sack/pre-sack2.png",
    "/img/cat-bag/animation-sack/pre-sack3.png",
    "/img/cat-bag/animation-sack/pre-sack4.png"
  ];
  const openAnimationSrc = "/img/cat-bag/animation/animation.gif";
  const openAnimationPreload = new Image();
  openAnimationPreload.src = openAnimationSrc;

  const openedCards = new Set();
  let openedCount = 0;
  let openLimit = null;
  if (openLimitAttr) {
    const parsed = parseInt(openLimitAttr, 10);
    if (!Number.isNaN(parsed)) {
      openLimit = parsed;
    }
  }

  let isNextMode = false;
  let currentOpenedCard = null;

  if (sackCards.length === 4) {
    sacksContainer.classList.add("has-four-cards");
  }

  function ensureSackCount(count) {
    if (!sacksContainer || !count) {
      return;
    }
    if (sackCards.length === count) {
      return;
    }
    if (sackCards.length < count) {
      const template = sackCards[0] || null;
      for (let i = sackCards.length; i < count; i++) {
        if (!template) {
          break;
        }
        const clone = template.cloneNode(true);
        const img = clone.querySelector("img");
        if (img) {
          img.src = preSackImages[i] || fallbackBagImage;
          img.alt = "pre";
        }
        clone.classList.remove("opened-sack", "expanded-card", "product-card");
        clone.removeAttribute("data-bag-id");
        clone.removeAttribute("data-bag-type");
        const productText = clone.querySelector(".sack-product-text");
        if (productText) {
          productText.style.display = "none";
        }
        sacksContainer.appendChild(clone);
        sackCards.push(clone);
      }
    } else if (sackCards.length > count) {
      for (let i = sackCards.length - 1; i >= count; i--) {
        const card = sackCards[i];
        if (card && card.parentNode === sacksContainer) {
          sacksContainer.removeChild(card);
        }
        sackCards.pop();
      }
    }

    if (sackCards.length === 4) {
      sacksContainer.classList.add("has-four-cards");
    } else {
      sacksContainer.classList.remove("has-four-cards");
    }
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

  function applyImages(images) {
    sackCards.forEach((card, index) => {
      const img = card.querySelector("img");
      if (img) {
        img.src = images[index] || preSackImages[index] || fallbackBagImage;
        img.alt = "sack";
      }
    });
  }

  function getInitialImages() {
    const images = [];
    images.push(categoryImages[0] || preSackImages[0]);
    images.push(categoryImages[1] || preSackImages[1]);
    if (sackCards.length >= 3) {
      images.push(catBagImage);
    }
    if (sackCards.length === 4) {
      images.push(goldenBagImage);
    }
    return images.slice(0, sackCards.length);
  }

  function shuffleArray(array) {
    const result = array.slice();
    for (let i = result.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [result[i], result[j]] = [result[j], result[i]];
    }
    return result;
  }

  function getBagImagesPool() {
    const images = [...categoryImages];
    images.push(catBagImage);
    if (sackCards.length === 4) {
      images.push(goldenBagImage);
    }
    while (images.length < sackCards.length) {
      images.push(fallbackBagImage);
    }
    return images.slice(0, sackCards.length);
  }

  function assignBagData(assignments) {
    sackCards.forEach((card, index) => {
      const assigned = assignments[index] || null;
      card.dataset.bagId = assigned?.bagId || "";
      card.dataset.bagType = assigned?.type || "";
    });
  }

  function enableSelection() {
    subtitle.textContent = "Выберите мешок и испытайте удачу!";
    infoText.classList.add("visible");
    sackCards.forEach((card) => {
      card.removeEventListener("click", openSack);
      card.addEventListener("click", openSack);
      card.style.cursor = "pointer";
    });
  }

  function applyOpenedState() {
    if (!sessionLoaded || !Array.isArray(sessionBags) || sessionBags.length === 0) {
      return;
    }
    const hasBagIds = sackCards.some(card => card.dataset.bagId);
    if (!hasBagIds && sessionBags.length === sackCards.length) {
      const sorted = [...sessionBags].sort((a, b) => {
        const left = typeof a?.position === "number" ? a.position : 0;
        const right = typeof b?.position === "number" ? b.position : 0;
        return left - right;
      });
      sorted.forEach((bag, index) => {
        const card = sackCards[index];
        if (!card) {
          return;
        }
        card.dataset.bagId = bag?.id || "";
        card.dataset.bagType = bag?.type || "";
      });
    }
    const openedIds = new Set(sessionBags.filter(b => b.opened_at).map(b => String(b.id)));
    const limit = typeof openLimit === "number" ? openLimit : sackCards.length;
    sackCards.forEach((card) => {
      const bagId = card.dataset.bagId;
      if (bagId && openedIds.has(String(bagId))) {
        card.classList.add("opened-sack");
        if (!card.classList.contains("product-card") && !card.classList.contains("expanded-card")) {
          const img = card.querySelector("img");
          if (img) {
            img.src = fallbackBagImage;
            img.alt = "sack";
          }
          const productText = card.querySelector(".sack-product-text");
          if (productText) {
            productText.style.display = "none";
          }
        }
        card.style.pointerEvents = "none";
        card.style.cursor = "default";
      } else if (openedCount >= limit) {
        card.style.pointerEvents = "none";
        card.style.cursor = "default";
      }
    });
  }

  function updateInfoText() {
    if (!infoText) {
      return;
    }
    const limit = typeof openLimit === "number" ? openLimit : sackCards.length;
    const remaining = Math.max(0, limit - openedCount);
    infoText.innerHTML = `осталось <span>${remaining}</span> из <span>${limit}</span> попыток`;
    if (cabinetBtn) {
      if (remaining === 0) {
        cabinetBtn.classList.remove("hidden");
      } else {
        cabinetBtn.classList.add("hidden");
      }
    }
  }

  function renderPrizeCards() {
    if (!prizeCards) {
      return;
    }
    prizeCards.innerHTML = "";
    prizeHistory.forEach((item) => {
      const card = createPrizeCard(item.image, item.title);
      prizeCards.appendChild(card);
    });
    if (prizeHistory.length) {
      prizeContainer.classList.add("visible");
    }
  }

  function persistPrizeHistory() {
    if (prizesStorageKey) {
      try {
        localStorage.setItem(prizesStorageKey, JSON.stringify(prizeHistory));
      } catch (e) {}
    }
    renderPrizeCards();
  }

  function upsertPrizeRecord(record) {
    if (!record) {
      return;
    }
    if (record.bagId) {
      const existingIndex = prizeHistory.findIndex(item => item.bagId === record.bagId);
      if (existingIndex >= 0) {
        prizeHistory[existingIndex] = record;
      } else {
        prizeHistory.push(record);
      }
    } else {
      prizeHistory.push(record);
    }
  }

  function syncPrizeHistoryFromSession(bags) {
    if (!Array.isArray(bags) || bags.length === 0) {
      return;
    }
    let changed = false;
    bags.forEach((bag) => {
      if (!bag?.opened_at) {
        return;
      }
      const bagId = bag?.id ? String(bag.id) : "";
      const prize = bag?.prize || null;
      const isEmpty = bag?.prize_type === "empty" || !prize;
      const title = isEmpty ? "Пусто" : (prize?.name || "Подарок");
      const image = isEmpty ? "" : (prize?.image || prizePlaceholderImage);
      const record = { bagId, title, image };
      const existingIndex = bagId
        ? prizeHistory.findIndex(item => item.bagId === bagId)
        : -1;
      if (existingIndex >= 0) {
        const existing = prizeHistory[existingIndex];
        if (isEmpty && existing?.title && existing.title !== "Пусто") {
          return;
        }
      }
      if (existingIndex === -1 || !isEmpty) {
        upsertPrizeRecord(record);
        changed = true;
      }
    });
    if (changed) {
      persistPrizeHistory();
    }
  }

  function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta?.getAttribute('content') || null;
  }

  function updateCategoryImagesFromSession(data) {
    const fromSession = Array.isArray(data?.visible_categories) ? data.visible_categories : [];
    if (!fromSession.length) {
      return categoryImages;
    }
    categoryImages = fromSession
      .map((category) => category?.image_thumb || category?.image || null)
      .filter(Boolean);
    if (!hasShuffled) {
      applyImages(getInitialImages());
    }
    return categoryImages;
  }

  function applyAssignmentFromIds(ids) {
    if (!Array.isArray(ids) || !ids.length || !Array.isArray(sessionBags) || !sessionBags.length) {
      return false;
    }
    const bagMap = new Map(sessionBags.map(bag => [String(bag.id), bag]));
    const assignments = ids.map((id) => {
      const bag = bagMap.get(String(id));
      return {
        bagId: bag?.id || null,
        type: bag?.type || "normal",
      };
    });
    assignedImages = assignments;
    hasShuffled = true;
    changeSacksImages();
    assignBagData(assignedImages);
    startBtn.classList.add("hidden");
    startBtn.disabled = false;
    enableSelection();
    if (storageKey) {
      try {
        localStorage.setItem(storageKey, JSON.stringify(assignedImages));
      } catch (e) {}
    }
    return true;
  }

  async function persistAssignment(assignments) {
    if (!orderPath || !Array.isArray(assignments) || !assignments.length) {
      return;
    }
    const bagIds = assignments.map(item => item?.bagId).filter(Boolean);
    if (!bagIds.length) {
      return;
    }
    try {
      await fetch(`${orderPath}/assignment`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": getCsrfToken() || "",
          "Accept": "application/json"
        },
        credentials: "same-origin",
        body: JSON.stringify({ assignments: bagIds })
      });
    } catch (e) {}
  }

  function syncSessionFromData(data) {
    if (!data) {
      return;
    }
    lastSessionData = data;
    if (Array.isArray(data?.bags)) {
      sessionBags = data.bags;
    }
    if (typeof data?.session?.bag_count === "number") {
      ensureSackCount(data.session.bag_count);
    } else if (Array.isArray(data?.bags)) {
      ensureSackCount(data.bags.length);
    }
    updateCategoryImagesFromSession(data);
    if (typeof data?.session?.open_limit === "number") {
      openLimit = data.session.open_limit;
    }
    if (typeof data?.session?.opened_count === "number") {
      openedCount = data.session.opened_count;
    }
  }

  function buildAssignmentsFromSession(bags) {
    if (!Array.isArray(bags) || bags.length === 0) {
      return [];
    }
    const sorted = [...bags].sort((a, b) => {
      const left = typeof a?.position === "number" ? a.position : 0;
      const right = typeof b?.position === "number" ? b.position : 0;
      return left - right;
    });
    return sorted.map(bag => ({
      bagId: bag?.id || null,
      type: bag?.type || "normal"
    }));
  }

  function ensureShuffledFromSession(data) {
    if (!data?.session || !Array.isArray(data?.bags)) {
      return;
    }
    if (Array.isArray(data?.assignment) && data.assignment.length) {
      applyAssignmentFromIds(data.assignment);
      return;
    }
    if (hasShuffled && Array.isArray(assignedImages) && assignedImages.length) {
      persistAssignment(assignedImages);
      return;
    }
    const openedCountValue = typeof data.session.opened_count === "number"
      ? data.session.opened_count
      : 0;
    const hasOpenedBag = Array.isArray(data?.bags) && data.bags.some(bag => bag?.opened_at);
    if (openedCountValue <= 0 && !hasOpenedBag) {
      return;
    }
    if (!hasShuffled) {
      assignedImages = buildAssignmentsFromSession(data.bags);
      if (storageKey) {
        try {
          localStorage.setItem(storageKey, JSON.stringify(assignedImages));
        } catch (e) {}
      }
      hasShuffled = true;
      changeSacksImages();
      assignBagData(assignedImages);
      startBtn.classList.add("hidden");
      startBtn.disabled = false;
      enableSelection();
    }
  }

  async function ensureSessionLoaded() {
    if (sessionBags.length) {
      return true;
    }
    const data = await loadSession(true);
    if (!sessionBags.length && lastSessionData?.bags) {
      syncSessionFromData(lastSessionData);
    } else if (data) {
      syncSessionFromData(data);
    }
    return sessionBags.length > 0;
  }

  async function loadSession(force = false) {
    if (!orderPath) {
      return;
    }
    if (sessionLoaded && !force) {
      return null;
    }
    if (sessionLoading) {
      await sessionLoading;
      return null;
    }
    sessionLoading = fetch(`${orderPath}/session`, { credentials: "same-origin" })
      .then(response => response.ok ? response.json() : null)
      .then(data => {
        sessionBags = Array.isArray(data?.bags) ? data.bags : [];
        openLimit = typeof data?.session?.open_limit === "number" ? data.session.open_limit : openLimit;
        openedCount = typeof data?.session?.opened_count === "number" ? data.session.opened_count : 0;
        sessionLoaded = Boolean(data?.session);
        if (sessionLoaded) {
          lastSessionData = data;
        }
        if (typeof data?.session?.bag_count === "number") {
          ensureSackCount(data.session.bag_count);
        } else if (sessionBags.length) {
          ensureSackCount(sessionBags.length);
        }
        updateCategoryImagesFromSession(data);
        ensureShuffledFromSession(data);
        updateInfoText();
        applyOpenedState();
        syncPrizeHistoryFromSession(sessionBags);
        return data;
      })
      .catch(() => {
        sessionBags = [];
        sessionLoaded = false;
        return null;
      })
      .finally(() => {
        sessionLoading = null;
      });
    return await sessionLoading;
  }

  function buildAssignments() {
    const types = [];
    types.push({ type: "category" });
    types.push({ type: "category" });
    types.push({ type: "cat" });
    if (sackCards.length === 4) {
      types.push({ type: "golden" });
    }
    const shuffledTypes = shuffleArray(types);
    const goldenBag = sessionBags.find(b => b.type === "golden");
    const normalBagIds = shuffleArray(sessionBags.filter(b => b.type !== "golden").map(b => b.id));
    let normalIndex = 0;
    return shuffledTypes.map(item => {
      if (item.type === "golden") {
        return { bagId: goldenBag?.id || normalBagIds[normalIndex++] || null, type: "golden" };
      }
      return { bagId: normalBagIds[normalIndex++] || null, type: item.type };
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
    const safeImage = imageUrl || "/img/cat-bag/animation-sack/product.svg";

    prizeCard.innerHTML = `
    <img src="${safeImage}" alt="prize">
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

  async function openSack(event) {
    const card = event.currentTarget;
    const img = card.querySelector("img");
    const bagId = card.dataset.bagId;
    const limit = typeof openLimit === "number" ? openLimit : sackCards.length;
    if (!bagId || openedCards.has(card) || openedCount >= limit) {
      return;
    }

    const prizeImageUrlFallback = prizePlaceholderImage;
    preloadPrizeImage(prizeImageUrlFallback);

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
    img.src = openAnimationSrc;

    const hiddenPrizeImg = document.createElement('img');
    hiddenPrizeImg.src = prizeImageUrlFallback;
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

    const openBagUrl = orderPath ? `${orderPath}/bag/${bagId}/open` : null;
    let prizeImageUrl = prizeImageUrlFallback;
    let prizeTitle = "Подарок";
    let isEmptyPrize = false;
    if (openBagUrl) {
      try {
        const response = await fetch(openBagUrl, {
          method: "POST",
          headers: {
            "X-CSRF-TOKEN": getCsrfToken() || "",
            "Accept": "application/json"
          },
          credentials: "same-origin"
        });
        const data = await response.json();
        const prizeType = data?.bag?.prize_type || null;
        const prizeData = data?.bag?.prize || null;
        if (!prizeData || prizeType === "empty") {
          isEmptyPrize = true;
          prizeTitle = "Пусто";
        } else {
          prizeImageUrl = prizeData?.image || prizeImageUrlFallback;
          prizeTitle = prizeData?.name || prizeTitle;
        }
      } catch (e) {}
    }
    preloadPrizeImage(prizeImageUrl);

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

          card.dataset.prizeImage = isEmptyPrize ? "" : prizeImageUrl;
          card.dataset.prizeTitle = prizeTitle;

          const productTextElement = card.querySelector(".sack-product-text");
          if (productTextElement) {
            const truncatedText = truncateText(prizeTitle, 2);
            productTextElement.textContent = truncatedText;
            productTextElement.title = prizeTitle;
            productTextElement.style.display = "block";
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

          openedCount = Math.min(limit, openedCount + 1);
          updateInfoText();
          applyOpenedState();
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
        if (openedCount < (typeof openLimit === "number" ? openLimit : sackCards.length)) {
          c.style.pointerEvents = "auto";
          c.style.cursor = "pointer";
        } else {
          c.style.pointerEvents = "none";
          c.style.cursor = "default";
        }
      }
    });

    openedCard.classList.remove("product-card");

    const img = openedCard.querySelector("img");
    img.src = "/img/cat-bag/animation-sack/sack.svg";

    const productText = openedCard.querySelector(".sack-product-text");
    if (productText) {
      productText.style.display = "none";
    }

    const text = openedCard.dataset.prizeTitle || (productText ? productText.title || productText.textContent : "Подарок");
    const productImageUrl = openedCard.dataset.prizeImage || prizePlaceholderImage;
    const bagId = openedCard.dataset.bagId;

    const record = { bagId, title: text, image: productImageUrl };
    upsertPrizeRecord(record);
    persistPrizeHistory();
  }

  async function shuffleSacks() {
    if (hasShuffled) {
      return;
    }
    const hasSession = await ensureSessionLoaded();
    if (!hasSession) {
      subtitle.textContent = "Не удалось загрузить данные игры. Попробуйте позже.";
      startBtn.classList.remove("hidden");
      startBtn.disabled = true;
      return;
    }
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
    assignedImages = buildAssignments();
    assignBagData(assignedImages);
    hasShuffled = true;
    persistAssignment(assignedImages);
    if (storageKey) {
      try {
        localStorage.setItem(storageKey, JSON.stringify(assignedImages));
      } catch (e) {}
    }
    enableSelection();
    updateInfoText();
  }

  startBtn.addEventListener("click", () => {
    if (isNextMode) {
      returnToSelection(currentOpenedCard);
    } else {
      shuffleSacks();
    }
  });

  window.addEventListener("open-cat-bags", async () => {
    const data = await loadSession(true);
    if (data) {
      syncSessionFromData(data);
      ensureShuffledFromSession(data);
    }
    if (!hasShuffled) {
      applyImages(getInitialImages());
    }
    updateInfoText();
  });

  if (storageKey) {
    try {
      const saved = localStorage.getItem(storageKey);
      if (saved) {
        assignedImages = JSON.parse(saved);
        if (Array.isArray(assignedImages) && typeof assignedImages[0] === "string") {
          assignedImages = null;
          hasShuffled = false;
          localStorage.removeItem(storageKey);
        } else {
          hasShuffled = true;
        }
      }
    } catch (e) {}
  }
  if (prizesStorageKey) {
    try {
      const savedPrizes = localStorage.getItem(prizesStorageKey);
      if (savedPrizes) {
        prizeHistory = JSON.parse(savedPrizes) || [];
      }
    } catch (e) {}
  }
  if (hasShuffled && Array.isArray(assignedImages) && assignedImages.length) {
    changeSacksImages();
    assignBagData(assignedImages);
    startBtn.classList.add("hidden");
    enableSelection();
    updateInfoText();
    loadSession();
    applyOpenedState();
  } else {
    applyImages(getInitialImages());
    updateInfoText();
    loadSession();
    applyOpenedState();
  }
  renderPrizeCards();

  sackCards.forEach(card => {
    const productText = card.querySelector(".sack-product-text");
    if (productText) {
      const originalText = productText.textContent;
      const truncatedText = truncateText(originalText, 2);
      productText.textContent = truncatedText;
      productText.title = originalText;
    }
  });
}
