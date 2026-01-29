@section('title', 'Тест компонента Cat Popup')

<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-md p-6 mb-8 flex items-center justify-center flex-col">
                <h1 class="text-3xl font-bold text-gray-900 mb-8">Тестирование компонента &lt;x-cat-popup&gt;</h1>

                <h2 class="text-xl font-semibold text-gray-800 mb-4">Тот, что открывается попапом при клике в баннере
                </h2>
                <x-cat-popup />

                <br><br>

                <h2 class="text-xl font-semibold text-gray-800 mb-4">Тот, что в блоке "спасибо"</h2>
                <x-cat-bag-in-th-order />

                <br><br>

                <h2 class="text-xl font-semibold text-gray-800 mb-4">Тот, что в корзине</h2>
                <x-cat-bag-in-basket />

                <br><br>

                <h2 class="text-xl font-semibold text-gray-800 mb-4">Тот, что в заказе подарками</h2>
                <x-cat-bag-gifts />

                <br><br>

                <h2 class="text-xl font-semibold text-gray-800 mb-4">попап собственность</h2>
                <button onclick="window.dispatchEvent(new CustomEvent('open-popup-own'))"
                    class="px-6 py-3 rounded-lg bg-gray-900 text-white font-medium hover:bg-gray-800 transition">
                    Информация об интеллектуальной собственности
                </button>

                <x-popup-own />

            </div>
        </div>
    </div>

    <br><br>

    <h2 class="text-xl font-semibold text-gray-800 mb-4">Акция с анимацией</h2>
    <x-cat-bags />
</x-app-layout>
