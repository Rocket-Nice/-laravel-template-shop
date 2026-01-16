@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>

  <div class="border-b">
    <div class="mx-auto px-2 sm:px-3 lg:px-4">
      <nav class="-mb-px flex flex-col sm:flex-row sm:justify-start space-y-2 sm:space-y-0 sm:space-x-4"
           aria-label="Tabs" role="tablist">
        <button type="button"
                class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                id="tab-1" aria-selected="true" role="tab" aria-controls="tab-1-content">Общие данные
        </button>
        <button type="button"
                class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                id="tab-2" aria-selected="true" role="tab" aria-controls="tab-2-content">Бонусы
        </button>
        <a href="{{ route('admin.orders.index', ['user_id' => $user->id]) }}"
           class="whitespace-nowrap no-underline py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500">Заказы
          пользователя <i class="fas fa-external-link-alt"></i></a>

        @if(auth()->user()->hasPermissionTo('Просмотр пазлов для акции') && $puzzleImages->count())
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-3" aria-selected="true" role="tab" aria-controls="tab-3-content">Пазлы
          </button>
        @endif

      </nav>
    </div>
  </div>

  <div class="mx-auto px-2 sm:px-3 lg:px-4 py-6" id="tab-content">
    <div id="tab-1-content" role="tabpanel">
      <form action="{{ route('admin.users.update', $user->id) }}" method="post" id="user-eidt">
        @csrf
        @method('PUT')
        <div class="sm:w-[50%]">
          <div class="form-group">
            <x-input-label for="name" :value="__('Имя')"/>
            <x-text-input type="text" name="name" id="name" value="{{ old('name') ?? $user->name }}"
                          class="mt-1 block w-full" required/>
          </div>
          <div class="form-group">
            <x-input-label for="email" :value="__('Email')"/>
            <x-text-input type="text" name="email" id="email" value="{{ old('email') ?? $user->email }}"
                          class="mt-1 block w-full" required/>
          </div>
          <div class="form-group">
            <x-input-label for="phone" :value="__('Телефон')"/>
            <x-text-input type="text" name="phone" id="phone" value="{{ old('phone') ?? $user->phone }}"
                          class="mt-1 block w-full" required/>
          </div>
          <div class="form-group">
            <x-input-label for="birthday" :value="__('Дата рождения')"/>
            <x-text-input type="text" name="birthday" id="birthday" value="{{ old('birthday') ?? $user->birthday?->format('d.m.Y') }}"
                          class="mt-1 datepicker w-full" data-minDate="false" data-timepicker="0"/>
          </div>

          <div class="form-group">
            <x-input-label for="password" :value="__('Пароль')"/>
            <x-text-input type="text" name="password" id="password" value="{{ old('password') }}"
                          class="mt-1 block w-full"/>
          </div>
          @if(auth()->user()->hasRole('admin'))
            <div class="form-group">
              <x-input-label for="role" :value="__('Роль')"/>
              <select id="role" name="role" class="form-control w-full">
                <option value="">Выбрать</option>
                @foreach($roles as $role)
                  <option value="{{ $role->id }}" @if($user->hasRole($role->name))
                    {!! 'selected' !!}
                    @endif>{{ $role->name }}</option>
                @endforeach
              </select>
            </div>
            @if($permissions->count())
              <div class="form-group">
                <x-input-label for="permissions" :value="__('Разрешения')"/>
                <select id="permissions" name="permissions[]" multiple class="multipleSelect form-control w-full">
                  <option value="">Выбрать</option>
                  @foreach($permissions as $permission)
                    <option value="{{ $permission->id }}" @if($user->hasPermissionTo($permission->name))
                      {!! 'selected' !!}
                      @endif>{{ $permission->name }}</option>
                  @endforeach
                </select>
                <div class="hint">Разрешения, которые не выданы ролью</div>
              </div>
            @endif
          @endif
          @if(auth()->id()==1)
            @if($user->tokens->isNotEmpty())
              <div class="form-group">
                <x-input-label :value="__('API токен')"/>
                <x-text-input type="text" value="**********************************"
                              class="mt-1 block w-full bg-gray-200 text-gray-500" disabled/>
              </div>
            @endif
            <div class="form-group">
              <a href="#" class="button button-success"
                 onclick="console.log(document.getElementById('createApiToken'));if(confirm('Создать API токен для пользователя {{ $user->name }}?'))document.getElementById('createApiToken').submit();return false;">Создать
                новый API токен</a>
            </div>
          @endif
        </div>
      </form>
    </div>
    <div id="tab-2-content" role="tabpanel">
      <div class="flex flex-wrap -m-1">
        <div class="w-1/2 p-1">
          <div class="mb-4">
            Бонусы {{ formatPrice($user->getBonuses()) }}
          </div>
          <div class="mb-6">
            <div class="flex space-x-2">
              @if($user->getBonuses() > 0)
                <a href="javascript:;" data-fancybox data-src="#sub-bonuses" class="m-1 button button-danger">Списать бонусы</a>
              @endif
              <a href="javascript:;" data-fancybox data-src="#add-bonuses" class="m-1 button button-success">Начислить бонусы</a>
            </div>
            <div class="hidden">
            @if($user->getBonuses() > 0)
              <div id="sub-bonuses" style="display: none;">
                <form action="{{ route('admin.users.bonuses.sub', $user->id) }}" method="post" class="p-4">
                  <h3 class="font-bold mb-4">Списать бонусы со счета</h3>
                  <div class="form-group">
                    <x-input-label for="bonuses" :value="__('Количество бонусов')"/>
                    <x-text-input type="text" name="bonuses" id="bonuses" value=""
                                  class="mt-1 block w-full numeric-field" data-max-value="{{ $user->getBonuses() }}" required/>
                  </div>
                  <div class="form-group">
                    <x-input-label for="comment" :value="__('Комментарий')" />
                    <x-textarea name="comment" id="comment" class="mt-1 block w-full"></x-textarea>
                  </div>
                  <x-primary-button>Списать бонусы</x-primary-button>
                </form>
              </div>

            @endif
            <div id="add-bonuses" style="display: none;">
              <form action="{{ route('admin.users.bonuses.add', $user->id) }}" method="post" class="p-4">
                <h3 class="font-bold mb-4">Начислить бонусы на счет</h3>
                <div class="form-group">
                  <x-input-label for="bonuses2" :value="__('Количество бонусов')"/>
                  <x-text-input type="text" name="bonuses" id="bonuses2" value="" data-max-value="100000" class="mt-1 block w-full numeric-field" required/>
                </div>
                <div class="form-group">
                  <x-input-label for="comment2" :value="__('Комментарий')" />
                  <x-textarea name="comment" id="comment2" class="mt-1 block w-full"></x-textarea>
                </div>
                <x-primary-button>Начислить бонусы</x-primary-button>
              </form>
            </div>
            </div>

          </div>
          @if($user->bonus_transactions)
            <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
              <tbody>
              @foreach($user->bonus_transactions()->orderBy('created_at', 'desc')->get() as $transaction)
                <tr>
                  <td class="border p-2 text-left w-1/4">{{ $transaction->created_at->format('d.m.Y H:i:s') }}</td>
                  <td class="border p-2 text-right w-1/4">{{ formatPrice($transaction->amount) }}</td>
                  <td class="border p-2 text-right w-1/4">{{ $transaction->comment }}</td>
                  <td class="border p-2 text-right w-1/4">{{ $transaction->createdBy?->email }}</td>
                </tr>
              @endforeach
              </tbody>
            </table>
          @endif
        </div>
        <div class="w-1/2 p-1">
          <div class="mb-4">
            Супербонусы {{ formatPrice($user->getSuperBonuses()) }}💎
          </div>

          <div class="mb-6">
            <div class="flex space-x-2">
              @if($user->getSuperBonuses() > 0)
                <a href="javascript:;" data-fancybox data-src="#sub-super-bonuses" class="m-1 button button-danger">Списать 💎 бонусы</a>
              @endif
              <a href="javascript:;" data-fancybox data-src="#add-super-bonuses" class="m-1 button button-success">Начислить 💎 бонусы</a>
            </div>

            <div class="hidden">
            @if($user->getSuperBonuses() > 0)
              <div id="sub-super-bonuses" style="display: none;">
                <form action="{{ route('admin.users.bonuses.sub', $user->id) }}" method="post" class="p-4">
                  <input type="hidden" name="super" value="1">
                  <h3 class="font-bold mb-4">Списать 💎 бонусы со счета</h3>
                  <div class="form-group">
                    <x-input-label for="super-bonuses" :value="__('Количество бонусов')"/>
                    <x-text-input type="text" name="bonuses" id="super-bonuses" value=""
                                  class="mt-1 block w-full numeric-field" data-max-value="{{ $user->getSuperBonuses() }}" required/>
                  </div>
                  <div class="form-group">
                    <x-input-label for="super-comment" :value="__('Комментарий')" />
                    <x-textarea name="comment" id="super-comment" class="mt-1 block w-full"></x-textarea>
                  </div>
                  <x-primary-button>Списать бонусы</x-primary-button>
                </form>
              </div>
            @endif
            <div id="add-super-bonuses" style="display: none;">
              <form action="{{ route('admin.users.bonuses.add', $user->id) }}" method="post" class="p-4">
                <input type="hidden" name="super" value="1">
                <h3 class="font-bold mb-4">Начислить 💎 бонусы на счет</h3>
                <div class="form-group">
                  <x-input-label for="super-bonuses2" :value="__('Количество бонусов')"/>
                  <x-text-input type="text" name="bonuses" id="super-bonuses2" value="" data-max-value="100000" class="mt-1 block w-full numeric-field" required/>
                </div>
                <div class="form-group">
                  <x-input-label for="super-comment2" :value="__('Комментарий')" />
                  <x-textarea name="comment" id="super-comment2" class="mt-1 block w-full"></x-textarea>
                </div>
                <x-primary-button>Начислить бонусы</x-primary-button>
              </form>
            </div>
            </div>
          </div>
          @if($user->super_bonus_transactions)
            <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
              <tbody>
              @foreach($user->super_bonus_transactions()->orderBy('created_at', 'desc')->get() as $transaction)
                <tr>
                  <td class="border p-2 text-left w-1/3">{{ $transaction->created_at?->format('d.m.Y H:i:s') }}</td>
                  <td class="border p-2 text-right w-1/3">{{ formatPrice($transaction->amount) }}💎</td>
                  <td class="border p-2 text-right w-1/3">{{ $transaction->comment }}</td>
                </tr>
              @endforeach
              </tbody>
            </table>
          @endif
        </div>
      </div>
    </div>

    @if(auth()->user()->hasPermissionTo('Просмотр пазлов для акции') && $puzzleImages->count())
      <div id="tab-3-content" role="tabpanel">
        <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
          <thead>
          <tr>
            <th class="bg-gray-100 border p-2"></th>
            <th class="bg-gray-100 border p-2">Дата</th>
            <th class="bg-gray-100 border p-2">Результат</th>
          </tr>
          </thead>
          <tbody>
          @foreach($puzzleImages as $puzzleImage)
            <tr>
              <td class="border p-2" style="width: 10%">
                <a href="{{ storageToAsset($puzzleImage->image_path) }}" data-fancybox="comment-{{ $puzzleImage->id }}" class="image inline-block rounded border border-myGray">
                  <img src="{{ storageToAsset($puzzleImage->thumb_path) }}" alt="" class="block w-[100px] h-[100px] rounded">
                </a>
              </td>
              <td class="border p-2">{{ \Carbon\Carbon::parse($puzzleImage->created_at)->format('d.m.Y H:i:s') }}</td>
              <td class="border p-2">{{ $puzzleImage->result_message }}</td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
  <div class="px-3 sm:px-4 lg:px-6 py-6 flex justify-end">
    <x-primary-button type="submit" form="user-eidt">Сохранить</x-primary-button>
  </div>
  <form action="{{ route('admin.users.createApiToken', $user->id) }}" id="createApiToken" method="POST">
    @csrf
  </form>
</x-admin-layout>
