<x-light-layout>
  <style>
    .tab-btn-active {
      color: #000;
    }
  </style>
  <div class="max-w-[500px] mx-auto min-h-screen py-12 m-text-body d-text-body" >
    <div class="mx-auto px-2 sm:px-3 lg:px-4">
      <nav class="-mb-px flex justify-between space-y-2 sm:space-y-0 sm:space-x-4"
           aria-label="Tabs" role="tablist">
        <button type="button"
                class="whitespace-nowrap py-2 sm:py-4 px-1 font-medium focus:outline-none text-myGray active:text-black"
                id="tab-1" aria-selected="true" role="tab" aria-controls="tab-1-content">Вход
        </button>
        <button type="button"
                class="whitespace-nowrap py-2 sm:py-4 px-1 font-medium focus:outline-none text-myGray active:text-black"
                id="tab-2" aria-selected="true" role="tab" aria-controls="tab-2-content">Создать аккаунт
        </button>

      </nav>
    </div>
    <div class="mx-auto px-2 sm:px-3 lg:px-4 py-6" id="tab-content">
      <div id="tab-1-content" role="tabpanel">
        <form method="POST" action="{{ route('happy_coupon.store', ['store-coupon' => request()->get('store-coupon')]) }}">
          @csrf
          <input type="hidden" name="login" value="1">
          <div class="mb-6">
            <x-public.order-input type="text" id="email" name="email" placeholder="Email" value="" required autofocus autocomplete="username"/>
          </div>
          <div class="mb-6">
            <x-public.order-input type="password" id="password" name="password" placeholder="Пароль" required autocomplete="current-password"/>
          </div>
          <div class="text-center">
            <x-public.primary-button type="submit" class="md:w-full max-w-[193px] md:max-w-[223px] lg:max-w-[253px] xl:max-w-[283px] md:text-2xl md:h-14">
              Войти
            </x-public.primary-button>
          </div>
          <div class="mt-6 text-center">
            <a href="{{ route('password.request') }}">Забыли пароль?</a>
          </div>
        </form>
      </div>
      <div id="tab-2-content" role="tabpanel">
        <form method="POST" action="{{ route('happy_coupon.store', ['store-coupon' => request()->get('store-coupon')]) }}">
          @csrf
          <input type="hidden" name="register" value="1">
          <div class="mb-6">
            <x-public.order-input type="text" id="last_name" name="last_name" placeholder="Фамилия" value="" required />
          </div>
          <div class="mb-6">
            <x-public.order-input type="text" id="first_name" name="first_name" placeholder="Имя" value="" required />
          </div>
          <div class="mb-6">
            <x-public.order-input type="text" id="middle_name" name="middle_name" placeholder="Отчество" value="" />
          </div>
          <div class="mb-6">
            <x-public.order-input type="text" id="phone" name="phone" placeholder="Телефон" value="" required/>
          </div>
          <div class="mb-6">
            <x-public.order-input type="text" id="email" name="email" placeholder="Email" value="" required/>
          </div>
          <div class="mb-6">
            <x-public.order-input type="text" id="email_confirmation" name="email_confirmation" placeholder="Повторите Email" value="" required/>
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
    </div>

  </div>
  <script>

    const tabs = document.querySelectorAll('[role="tab"]');
    const tabList = document.querySelector('[role="tablist"]');
    const tabContent = document.getElementById('tab-content');

    function activateTab(tab) {
      // Deactivate all tabs
      tabs.forEach((t) => {
        t.setAttribute('aria-selected', 'false');
        t.classList.remove('tab-btn-active');
        t.classList.add('text-gray-500');
      });

      // Activate the clicked tab
      tab.setAttribute('aria-selected', 'true');
      tab.classList.remove('text-gray-500');
      tab.classList.add('tab-btn-active');

      // Hide all tab content
      const tabPanels = tabContent.querySelectorAll('[role="tabpanel"]');
      tabPanels.forEach((panel) => {
        panel.hidden = true;
      });

      // Show the content panel for the clicked tab
      const tabPanel = document.getElementById(tab.getAttribute('aria-controls'));
      tabPanel.hidden = false;
    }

    if (tabs.length){
      // Add event listeners to each tab
      tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
          activateTab(tab);

          // Update URL
          const tabId = tab.getAttribute('id');
          history.pushState({}, '', `#${tabId}`);
        });
      });

      // Check if there's a hash in the URL that matches a tab id
      if (location.hash && document.querySelector(location.hash)) {
        const tabFromURL = document.querySelector(location.hash);
        activateTab(tabFromURL);
      } else {
        // Set up initial tab state
        if(tabList.querySelector('[aria-selected="true"]')) {
          tabList.querySelector('[aria-selected="true"]').click();
        }else{
          tabList.querySelector('[role="tab"]:first-child').click();
        }
      }
    }

    // Handle the popstate event for the browser's back/forward buttons
    window.addEventListener('popstate', function() {
      if (location.hash && document.querySelector(location.hash)) {
        const tabFromURL = document.querySelector(location.hash);
        activateTab(tabFromURL);
      }
    });
  </script>
</x-light-layout>
