<x-dropdown_menu :title="'Другие отчеты'">
  <x-slot name="content">
    <div class="py-1" role="none">
      <a href="{{ route('admin.reports.index')  }}"
         class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem"
         tabindex="-1">Суммы заказов</a>
      <a href="{{ route('admin.reports.statuses')  }}"
         class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem"
         tabindex="-1">Статусы заказов</a>
      <a href="{{ route('admin.reports.shipping')  }}"
         class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem"
         tabindex="-1">Способы доставки</a>
      <a href="{{ route('admin.reports.products')  }}"
         class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem"
         tabindex="-1">Топ-продукты</a>
      <a href="{{ route('admin.reports.new-users')  }}"
         class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem"
         tabindex="-1">Новые клиенты</a>
      <a href="{{ route('admin.reports.average-check')  }}"
         class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem"
         tabindex="-1">Средний чек</a>
      <a href="{{ route('admin.reports.finished-orders')  }}"
         class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem"
         tabindex="-1">Завершенные заказы</a>
      <a href="{{ route('admin.reports.links')  }}"
         class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem"
         tabindex="-1">Специальные ссылки</a>
    </div>
  </x-slot>
</x-dropdown_menu>
