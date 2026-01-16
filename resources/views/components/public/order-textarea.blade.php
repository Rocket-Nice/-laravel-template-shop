@props([
    'disabled' => false,
    'readonly' => false,
    'label' => false,
    'min' => null,
])

<div x-data="{ text: '' }" class="w-full">
    <textarea
      x-model="text"
      {{ $disabled ? 'disabled' : '' }}
      {{ $readonly ? 'readonly' : '' }}
      {!! $attributes->merge([
          'class' => 'block w-full border border-myGray bg-transparent placeholder-myGray
                      m-subtitle-2 d-subtitle-2 py-2.5 sm:py-1.75 px-3 leading-none
                      focus:ring-0 focus:border-black'
      ]) !!}
      @if($min) minlength="{{ $min }}" @endif
    >{{ $slot }}</textarea>

  @if($min)
    <div class="text-sm text-myGray text-right mt-1">
            <span
              x-text="text.length"
              :class="text.length >= {{ $min }} ? 'text-red-500' : 'text-myGray'"
            ></span>/<span>{{ $min }}</span>
    </div>
  @endif
</div>
