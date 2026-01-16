<header class="p-3 flex justify-between items-center h-14 shadow bg-white">
  <button id="sidebarBtn" class="flex items-center justify-center w-8 h-8 text-slate-800 hover:bg-black/10" style="z-index: 100;">
    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-menu-2" width="44" height="44" viewBox="0 0 24 24" stroke-width="2" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
      <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
      <path d="M4 6l16 0" />
      <path d="M4 12l16 0" />
      <path d="M4 18l16 0" />
    </svg>
  </button>
  <div id="header-dropdown" class="relative inline-block text-left">
    <div class="dropdown-box relative inline-block text-left">
      <div>
        <button type="button" class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-slate-800 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dropdown-button" id="menu-button" aria-expanded="true" aria-haspopup="true">
          {{ auth()->user()->name ?? auth()->user()->email ?? '' }}
          <svg class="-mr-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
          </svg>
        </button>
      </div>

      <!--
        Dropdown menu, show/hide based on menu state.

        Entering: "transition ease-out duration-100"
          From: "transform opacity-0 scale-95"
          To: "transform opacity-100 scale-100"
        Leaving: "transition ease-in duration-75"
          From: "transform opacity-100 scale-100"
          To: "transform opacity-0 scale-95"
      -->
      <div class="hidden absolute right-0 z-10 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1" style="z-index: 9999">
        <div class="py-1" role="none">
          <!-- Active: "bg-gray-100 text-slate-800", Not Active: "text-gray-700" -->
          <a href="{{ route('page.index') }}" class="no-underline hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1">Магазин</a>
          <a href="{{ route('admin.users.edit', auth()->id()) }}" class="no-underline hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1">Профиль</a>
        </div>
        <div class="py-1" role="none">
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none" role="none">
          @csrf
          <button type="submit" class="hover:bg-gray-100 text-gray-700 block w-full px-4 py-2 text-left text-sm" role="menuitem" tabindex="-1">Выйти</button>
        </form>
        </div>
      </div>
    </div>
  </div>
</header>

