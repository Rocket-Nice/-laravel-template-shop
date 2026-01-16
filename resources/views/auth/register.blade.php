<x-light-layout>
    <div class="max-w-[500px] mx-auto min-h-screen py-12 m-text-body d-text-body" >
        <form method="POST" action="{{ route('register') }}">
        @csrf
            <div class="mb-6">
                <x-public.order-input type="text" id="last_name" name="last_name" placeholder="Фамилия" value="{{ old('last_name') }}" required />
            </div>
            <div class="mb-6">
                <x-public.order-input type="text" id="first_name" name="first_name" placeholder="Имя" value="{{ old('first_name') }}" required />
            </div>
            <div class="mb-6">
                <x-public.order-input type="text" id="middle_name" name="middle_name" placeholder="Отчество" value="{{ old('middle_name') }}" />
            </div>
            <div class="mb-6">
                <x-public.order-input type="text" id="phone" name="phone" placeholder="Телефон" value="{{ old('phone') }}" required/>
            </div>
            <div class="mb-6">
                <x-public.order-input type="text" id="email" name="email" placeholder="Email" value="{{ old('email') }}" required/>
            </div>
            <div class="mb-6">
                <x-public.order-input type="text" id="email_confirmation" name="email_confirmation" placeholder="Повторите Email" value="{{ old('email_confirmation') }}" required/>
            </div>
            <div class="mb-6">
                <x-public.order-input type="text" id="birthday" name="birthday" class="birthday" placeholder="Дата рождения"  value="{{ old('birthday') }}"/>
            </div>
            <div class="mb-6">
                <x-public.order-input type="password" id="password" name="password" placeholder="Пароль" required autocomplete="new-password"/>
            </div>
            <div class="mb-6">
                <x-public.order-input type="password" id="password_confirmation" name="password_confirmation" placeholder="Повторите пароль" required/>
            </div>
            <div class="text-center">
                <x-public.primary-button type="submit" class="md:w-full max-w-[193px] md:max-w-[223px] lg:max-w-[253px] xl:max-w-[283px] md:text-2xl md:h-14">
                    Зарегистрироваться
                </x-public.primary-button>
            </div>
        </form>
    </div>
{{--    <form method="POST" action="{{ route('register') }}">--}}
{{--        @csrf--}}

{{--        <!-- Name -->--}}
{{--        <div>--}}
{{--            <x-input-label for="name" :value="__('Name')" />--}}
{{--            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />--}}
{{--            <x-input-error :messages="$errors->get('name')" class="mt-2" />--}}
{{--        </div>--}}

{{--        <!-- Email Address -->--}}
{{--        <div class="mt-4">--}}
{{--            <x-input-label for="email" :value="__('Email')" />--}}
{{--            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />--}}
{{--            <x-input-error :messages="$errors->get('email')" class="mt-2" />--}}
{{--        </div>--}}

{{--        <!-- Password -->--}}
{{--        <div class="mt-4">--}}
{{--            <x-input-label for="password" :value="__('Password')" />--}}

{{--            <x-text-input id="password" class="block mt-1 w-full"--}}
{{--                            type="password"--}}
{{--                            name="password"--}}
{{--                            required autocomplete="new-password" />--}}

{{--            <x-input-error :messages="$errors->get('password')" class="mt-2" />--}}
{{--        </div>--}}

{{--        <!-- Confirm Password -->--}}
{{--        <div class="mt-4">--}}
{{--            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />--}}

{{--            <x-text-input id="password_confirmation" class="block mt-1 w-full"--}}
{{--                            type="password"--}}
{{--                            name="password_confirmation" required autocomplete="new-password" />--}}

{{--            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />--}}
{{--        </div>--}}

{{--        <div class="flex items-center justify-end mt-4">--}}
{{--            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">--}}
{{--                {{ __('Already registered?') }}--}}
{{--            </a>--}}

{{--            <x-primary-button class="ml-4">--}}
{{--                {{ __('Register') }}--}}
{{--            </x-primary-button>--}}
{{--        </div>--}}
{{--    </form>--}}
</x-light-layout>
