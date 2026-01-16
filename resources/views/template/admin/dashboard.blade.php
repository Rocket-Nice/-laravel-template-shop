<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-semibold m-2">
            {{ __('Dashboard') }}
        </h1>
    </x-slot>

{{--    @if(auth()->id()==1)--}}
{{--        @php--}}
{{--        dd($settings)--}}
{{--         @endphp--}}
{{--    @endif--}}

    {{ __("You're logged in!") }}
</x-admin-layout>
