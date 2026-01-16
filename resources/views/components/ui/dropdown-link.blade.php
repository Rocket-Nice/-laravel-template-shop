
<a {{ $attributes->merge(['class' => 'text-dark-700 block px-4 py-2 text-sm hover:bg-dark-100 hover:text-dark-900']) }}
   role="menuitem" tabindex="-1"
   @mouseenter="onMouseEnter($event)"
   @mousemove="onMouseMove($event, 0)"
   @mouseleave="onMouseLeave($event)"
   @click="open = false; focusButton()">{{ $slot }}</a>
