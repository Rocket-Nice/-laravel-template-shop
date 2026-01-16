<!-- Sidenav -->
<div id="sidebar" class="overflow-hidden fixed min-h-full z-50 sm:relative w-full sm:w-[200px] sm:min-w-[200px] transition-all bg-slate-800 text-white px-2 min-h-screen">
    <div class="sidebarContent pb-[100px] h-screen overflow-y-auto py-3 pb-[300px]">
          <div class="mb-4 text-center">
            <a href="{{ route('admin.page.index') }}">
              <x-application-logo class="block mx-auto h-9 w-auto fill-current text-slate-200 w-full max-w-[200px]" />
            </a>
          </div>
      @if(auth()->user()->hasPermissionTo('Просмотр заказов'))
        <a href="{{ route('admin.orders.index') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30">
              <span class="mr-2 text-gray-300">
                <i class="fa-solid fa-shopping-basket w-5"></i>
              </span>
          <span class="text-xs">Заказы</span>
        </a>
      @endif
      @if(auth()->user()->hasPermissionTo('Отгрузка заказов'))
        <div class="w-full">
          <div class="relative group dropdown">
            <button class="nav-parent p-2 transition-colors rounded flex items-center w-full hover:bg-black/30">
              <span class="mr-2 text-gray-300 text-left">
                <i class="fa-solid fa-truck-loading w-5"></i>
              </span>
              <span class="text-xs">Отгрузка заказов</span>
              <svg class="h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M7 7l3 3 3-3" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"></path>
              </svg>
            </button>
            <div class="dropdown-content hidden rounded-lg">

              @if(auth()->user()->hasPermissionTo('Просмотр накладных'))
              <a href="{{ route('admin.invoices.index') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Накладные</a>
              @endif
              @if(auth()->user()->hasPermissionTo('Этикетки ШК'))
              <a href="{{ route('admin.tickets.index') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Этикетки ШК</a>
              @endif
                <a href="{{ route('admin.shipping.log') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Уведомления</a>
            </div>
          </div>
        </div>
      @endif
      @if(auth()->user()->hasPermissionTo('Просмотр товаров'))
        <div class="w-full">
          <div class="relative group dropdown">
            <button class="nav-parent p-2 transition-colors rounded flex items-center w-full hover:bg-black/30">
              <span class="mr-2 text-gray-300 text-left">
                <i class="fa-solid fa-boxes w-5"></i>
              </span>
              <span class="text-xs">Продукция</span>
              <svg class="h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M7 7l3 3 3-3" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"></path>
              </svg>
            </button>
            <div class="dropdown-content hidden rounded-lg">
              <a href="{{ route('admin.products.index') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Товары</a>
              @if(auth()->user()->hasPermissionTo('Управления артикулами'))
              <a href="{{ route('admin.product-skus.index') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Артикулы</a>
              @endif
              @if(auth()->user()->hasPermissionTo('Наличие продуктов'))
              <a href="{{ route('admin.products.quantity') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Наличие</a>
              @endif
              @if(auth()->user()->hasPermissionTo('Просмотр остатков'))
              <a href="{{ route('admin.products.statistic') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Остатки</a>
              @endif
              @if(auth()->user()->hasPermissionTo('Категории товаров'))
              <a href="{{ route('admin.categories.index') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Категории товаров</a>
              @endif
              @if(auth()->user()->hasPermissionTo('Типы товаров'))
              <a href="{{ route('admin.product_types.index') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Типы товаров</a>
              @endif
              @if(auth()->user()->hasPermissionTo('Управление отзывами'))
              <a href="{{ route('admin.products.reviews') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Отзывы о товарах</a>
              @endif
              @if(auth()->user()->hasPermissionTo('Уведомления о поступлении'))
              <a href="{{ route('admin.products.notifications') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Уведомления о поступлении</a>
              @endif
              @if(auth()->user()->hasPermissionTo('Просмотр данных для маркетплейсов'))
              <a href="{{ route('admin.products.marketplaces') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Маркетплейсы</a>
              @endif
            </div>
          </div>
        </div>
      @endif
      @if(auth()->user()->hasPermissionTo('Управление доставкой'))
        <div class="w-full">
          <div class="relative group dropdown">
            <button class="nav-parent p-2 transition-colors rounded flex items-center w-full hover:bg-black/30">
              <span class="mr-2 text-gray-300 text-left">
                <i class="fa-solid fa-truck w-5"></i>
              </span>
              <span class="text-xs">Доставка</span>
              <svg class="h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M7 7l3 3 3-3" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"></path>
              </svg>
            </button>
            <div class="dropdown-content hidden rounded-lg">
              <a href="{{ route('admin.shipping.countries.index') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Страны</a>
              <a href="{{ route('admin.shipping_methods.index') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Способы доставки</a>
            </div>
          </div>
        </div>
      @endif

      @if(auth()->user()->hasPermissionTo('Управление контентом'))
        <a href="{{ route('admin.content.index') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30">
          <span class="mr-2 text-gray-300">
            <i class="fa-solid fa-newspaper w-5"></i>
          </span>
          <span class="text-xs">Контент</span>
        </a>
      @endif
      @if(auth()->user()->hasPermissionTo('Управление страницами'))
        <a href="{{ route('admin.pages.index') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30">
          <span class="mr-2 text-gray-300">
            <i class="fa-solid fa-file-alt w-5"></i>
          </span>
          <span class="text-xs">Страницы</span>
        </a>
      @endif
      @if(auth()->user()->hasAnyPermission(['Просмотр промокодов', 'Просмотр подарочных сертификатов']))
        <div class="w-full">
          <div class="relative group dropdown">
            <button class="nav-parent p-2 transition-colors rounded flex items-center w-full hover:bg-black/30">
              <span class="mr-2 text-gray-300 text-left">
                <i class="fa-solid fa-tags w-5"></i>
              </span>
              <span class="text-xs">Промо</span>
              <svg class="h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M7 7l3 3 3-3" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"></path>
              </svg>
            </button>
            <div class="dropdown-content hidden rounded-lg">
              @if(auth()->user()->hasPermissionTo('Просмотр промокодов'))
                <a href="{{ route('admin.coupones.index') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Промокоды</a>
              @endif
              @if(auth()->user()->hasPermissionTo('Просмотр подарочных сертификатов'))
                <a href="{{ route('admin.vouchers.index') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Подарочные сертификаты</a>
              @endif
            </div>
          </div>
        </div>
      @endif
      @if(auth()->user()->hasPermissionTo('Доступ к СК'))
        <div class="w-full">
          <div class="relative group dropdown">
            <button class="nav-parent p-2 transition-colors rounded flex items-center w-full hover:bg-black/30">
              <span class="mr-2 text-gray-300 text-left">
                <i class="fa-solid fa-gifts w-5"></i>
              </span>
              <span class="text-xs">Счастливый купон</span>
              <svg class="h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M7 7l3 3 3-3" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"></path>
              </svg>
            </button>
            <div class="dropdown-content hidden rounded-lg">
              @if(auth()->user()->hasPermissionTo('Управление счастливым купоном'))
                <a href="{{ route('admin.happy_coupones.activePrizes') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Активные подарки</a>
              @endif
              @if(auth()->user()->hasPermissionTo('Просмотр купонов "СК"'))
                <a href="{{ route('admin.happy_coupones.index') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Купоны с подарками</a>
              @endif
              @if(auth()->user()->hasPermissionTo('Управление подарками'))
                <a href="{{ route('admin.prizes.index') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">База подарков</a>
              @endif
              @if(auth()->user()->hasPermissionTo('Управление партнерами'))
                <a href="{{ route('admin.partners.index') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Партнеры</a>
              @endif
              @if(auth()->user()->hasPermissionTo('Управление кодами магазинов для СК'))
                <a href="{{ route('admin.happy_coupones.stores') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Купоны для магазинов</a>
              @endif
            </div>
          </div>
        </div>
      @endif
      @if(auth()->user()->hasPermissionTo('Просмотр пазлов для акции'))
        <div class="w-full">
          <div class="relative group dropdown">
            <button class="nav-parent p-2 transition-colors rounded flex items-center w-full hover:bg-black/30">
              <span class="mr-2 text-gray-300 text-left">

                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-puzzle" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#fff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                  <path d="M4 7h3a1 1 0 0 0 1 -1v-1a2 2 0 0 1 4 0v1a1 1 0 0 0 1 1h3a1 1 0 0 1 1 1v3a1 1 0 0 0 1 1h1a2 2 0 0 1 0 4h-1a1 1 0 0 0 -1 1v3a1 1 0 0 1 -1 1h-3a1 1 0 0 1 -1 -1v-1a2 2 0 0 0 -4 0v1a1 1 0 0 1 -1 1h-3a1 1 0 0 1 -1 -1v-3a1 1 0 0 1 1 -1h1a2 2 0 0 0 0 -4h-1a1 1 0 0 1 -1 -1v-3a1 1 0 0 1 1 -1" />
                </svg>
              </span>
              <span class="text-xs">Пазлы</span>
              <svg class="h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M7 7l3 3 3-3" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"></path>
              </svg>
            </button>
            <div class="dropdown-content hidden rounded-lg">
                <a href="{{ route('admin.puzzles.index') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Подарки</a>
                <a href="{{ route('admin.puzzle_participants.index') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Загруженные изображения</a>
            </div>
          </div>
        </div>
      @endif
      @if(auth()->id()==1&&auth()->user()->hasPermissionTo('Управление категориями блога')||auth()->user()->hasPermissionTo('Управление статьями блога'))
        <div class="w-full">
          <div class="relative group dropdown">
            <button class="nav-parent p-2 transition-colors rounded flex items-center w-full hover:bg-black/30">
              <span class="mr-2 text-gray-300 text-left">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-article" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#fff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                  <path d="M3 4m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                  <path d="M7 8h10" />
                  <path d="M7 12h10" />
                  <path d="M7 16h10" />
                </svg>
              </span>
              <span class="text-xs">Новости</span>
              <svg class="h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M7 7l3 3 3-3" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"></path>
              </svg>
            </button>
            <div class="dropdown-content hidden rounded-lg">

              @if(auth()->user()->hasPermissionTo('Управление категориями блога'))
                <a href="{{ route('admin.blog.categories.index') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Разделы</a>
              @endif
              @if(auth()->user()->hasPermissionTo('Управление статьями блога'))
                <a href="{{ route('admin.blog.articles.index') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Публикации</a>
              @endif
            </div>
          </div>
        </div>
      @endif
      @if(auth()->user()->hasPermissionTo('Доступ к НПС'))
        <a href="{{ route('admin.nps.comments') }}" class="nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30">
              <span class="mr-2 text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-heart" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="#fff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                  <path d="M19.5 12.572l-7.5 7.428l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572" />
                </svg>
              </span>
          <span class="text-xs">НПС</span>
        </a>
      @endif
      @if(auth()->user()->hasPermissionTo('Доступ к кастомным формам'))
        <a href="{{ route('admin.custom-forms.data') }}" class="nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30">
              <span class="mr-2 text-gray-300">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M3 17l9 5l9 -5v-3l-9 5l-9 -5v-3l9 5l9 -5v-3l-9 5l-9 -5l9 -5l5.418 3.01" />
                </svg>
              </span>
          <span class="text-xs">Желания</span>
        </a>
      @endif
      @if(auth()->user()->hasPermissionTo('Пользователи'))
        <a href="{{ route('admin.users.index') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30">
              <span class="mr-2 text-gray-300">
                <i class="fa-solid fa-user w-5"></i>
              </span>
          <span class="text-xs">Пользователи</span>
        </a>
      @endif
      @if(auth()->user()->hasPermissionTo('Рассылка в телеграм')||auth()->user()->hasPermissionTo('Уведомления в телеграм'))
        <div class="w-full">
          <div class="relative group dropdown">
            <button class="nav-parent p-2 transition-colors rounded flex items-center w-full hover:bg-black/30">
              <span class="mr-2 text-gray-300 text-left">
                <i class="fa-solid fa-paper-plane w-5"></i>
              </span>
              <span class="text-xs">Коммуникации</span>
              <svg class="h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M7 7l3 3 3-3" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"></path>
              </svg>
            </button>
            <div class="dropdown-content hidden rounded-lg">
              @if(auth()->user()->hasPermissionTo('Рассылка в телеграм'))
                <a href="{{ route('admin.telegram_mailing.index') }}" class="nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Рассылки</a>
              @endif
              @if(auth()->user()->hasPermissionTo('Уведомления в телеграм'))
                <a href="{{ route('admin.tg_notifications.index') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Телеграм</a>
              @endif
            </div>
          </div>
        </div>
      @endif

      @if(auth()->user()->hasPermissionTo('Роли и разрешения'))
        <div class="w-full">
          <div class="relative group dropdown">
            <button class="nav-parent p-2 transition-colors rounded flex items-center w-full hover:bg-black/30">
              <span class="mr-2 text-gray-300 text-left">
                <i class="fa-solid fa-lock w-5"></i>
              </span>
              <span class="text-xs">Доступ</span>
              <svg class="h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M7 7l3 3 3-3" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"></path>
              </svg>
            </button>
            <div class="dropdown-content hidden rounded-lg">
              <a href="{{ route('admin.roles.index') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Роли</a>
              <a href="{{ route('admin.permissions.index') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Разрешения</a>
              <a href="{{ route('admin.users.admins') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Менеджеры</a>
            </div>
          </div>
        </div>
      @endif

      @if(auth()->id()!=314738&&auth()->user()->hasPermissionTo('Лог действий'))
        <a href="{{ route('admin.log.index') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30">
              <span class="mr-2 text-gray-300">
                <i class="fa-solid fa-clipboard-list w-5"></i>
              </span>
          <span class="text-xs">История действий</span>
        </a>
      @endif
      @if(auth()->user()->hasPermissionTo('Управление настройками'))
        <div class="w-full">
          <div class="relative group dropdown">
            <button class="nav-parent p-2 transition-colors rounded flex items-center w-full hover:bg-black/30">
              <span class="mr-2 text-gray-300 text-left">
                <i class="fa-solid fa-wrench w-5"></i>
              </span>
              <span class="text-xs">Настройки</span>
              <svg class="h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M7 7l3 3 3-3" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"></path>
              </svg>
            </button>
            <div class="dropdown-content hidden rounded-lg">
              <a href="{{ route('admin.search_queries.index') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Статистика поисковых запросов</a>
              <a href="{{ route('admin.settings') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Общие настройки</a>
              <a href="{{ route('admin.sytstem_settings.index') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Системные настройки</a>
              <a href="{{ route('admin.settings.info') }}" class="w-auto nav-link no-underline p-2 transition-colors rounded flex items-center hover:bg-black/30 text-xs">Нагрузка</a>
            </div>
          </div>
        </div>
      @endif
    </div>
    <div class="fixed z-10 bottom-0 left-0 px-2 py-4">
      <div class="text-slate-400 text-xs">
        {{ now()->year }} &copy; Le Mousse<br/>
{{--        <a href="#" class="underline hover:no-underline">Сообщить о проблеме</a>--}}
      </div>
    </div>
</div>
<script>
  document.querySelectorAll('.group').forEach(function(group) {
    group.querySelector('button').addEventListener('click', function() {
      group.querySelector('.nav-parent').classList.toggle('bg-black/20');
      group.querySelector('.dropdown-content').classList.toggle('hidden');
    });
  });
</script>
