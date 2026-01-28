<div class="cat-container">
    <button class="btn-close">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd"
                d="M18.3637 19.0708L19.0708 18.3637L12.7069 11.9998L19.0708 5.63592L18.3637 4.92882L11.9998 11.2927L5.63582 4.92871L4.92871 5.63582L11.2927 11.9998L4.92871 18.3638L5.63582 19.071L11.9998 12.7069L18.3637 19.0708Z"
                fill="#010101" />
        </svg>
    </button>
    <div class="sack-cat-wrapper">
        <div class="sack-cat-container">
            <div class="title-container">
                <h3 class="title">КОТ В МЕШКЕ</h3>
                <p class="subtitle font-inter_font">Запустите игру и испытайте удачу!</p>
            </div>

            <div class="sacks-animation-container font-inter_font">
                <div class="sack-card">
                    <img src="/img/cat-bag/animation-sack/pre-sack1.png" alt="pre">
                    <p class="sack-product-text">Очень длинное название для этого продукта будет находиться в этом</p>
                </div>
                <div class="sack-card">
                    <img src="/img/cat-bag/animation-sack/pre-sack2.png" alt="pre">
                    <p class="sack-product-text">Очень длинное название для этого продукта будет находиться в этом</p>
                </div>
                <div class="sack-card">
                    <img src="/img/cat-bag/animation-sack/pre-sack3.png" alt="pre">
                    <p class="sack-product-text">Очень длинное название для этого продукта будет находиться в этом</p>
                </div>
                <!-- 4 мешок, по коду 2 варианта перемешивания -->
                {{-- <div class="sack-card">
                    <img src="/img/cat-bag/animation-sack/pre-sack4.png" alt="pre">
                    <p class="sack-product-text">Очень длинное название для этого продукта будет находиться в этом</p>
                </div> --}}
            </div>
        </div>
        <div class="sack-price-container">
            <h3 class="prize-title">Мои подарки</h3>
            <div class="sack-price-cards font-inter_font">

            </div>
        </div>
    </div>


    <div class="btn-info-container">
        <p class="info-text font-inter_font">осталось <span>3</span> из <span>3</span> попыток</p>

        <x-cat-bag-button class="start-btn">
            Перемешать
        </x-cat-bag-button>
    </div>
</div>
