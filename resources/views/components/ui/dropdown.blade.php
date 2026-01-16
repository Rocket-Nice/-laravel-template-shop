
<div x-data="menu()" x-init="init()" @keydown.escape.stop="open = false; focusButton()"
     @click.away="onClickAway($event)" class="relative inline-block text-left">
    <div>
        <button type="button"
                class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-dark-900 shadow-sm ring-1 ring-inset ring-dark-300 hover:bg-dark-50"
                id="menu-button" x-ref="button" @click="onButtonClick()" @keyup.space.prevent="onButtonEnter()"
                @keydown.enter.prevent="onButtonEnter()" aria-expanded="true" aria-haspopup="true"
                x-bind:aria-expanded="open.toString()" @keydown.arrow-up.prevent="onArrowUp()"
                @keydown.arrow-down.prevent="onArrowDown()">
            {{ $trigger }}
            <svg class="-mr-1 h-5 w-5 text-dark-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd"
                      d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                      clip-rule="evenodd"></path>
            </svg>
        </button>
    </div>
    <div x-show="open" x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute z-10 w-56 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
         x-bind:class="dropdownPosition" x-ref="menuItems"
         x-description="Dropdown menu, show/hide based on menu state." role="menu" aria-orientation="vertical"
         aria-labelledby="menu-button" tabindex="-1" @keydown.arrow-up.prevent="onArrowUp()"
         @keydown.arrow-down.prevent="onArrowDown()" @keydown.tab="open = false"
         @keydown.enter.prevent="open = false; focusButton()" @keyup.space.prevent="open = false; focusButton()">
        <div class="py-1" role="none">
            {{ $slot }}
        </div>
    </div>
</div>
