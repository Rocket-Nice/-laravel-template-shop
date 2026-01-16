@props(['route' => ''])

<div class="mx-auto relative z-20">
    <form action="{{ $route }}" method="GET">
        <div class="flex flex-wrap -m-1">
            {{ $slot }}

            <div class="p-1 w-full lg:w-1/2">
                <x-primary-button>Поиск</x-primary-button>
            </div>
        </div>
    </form>
</div>

