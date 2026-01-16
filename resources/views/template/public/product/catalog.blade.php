@section('title', $seo['title'] ?? config('app.name'))
<x-app-layout>

  <div class="rotate-180"></div>
  <div class="sm:px-4 md:px-8 lg:px-14 xl:px-16">
{{--    h-[180px] sm:h-[200px] md:h-[231px] lg:h-[298px] xl:h-[378px]--}}
    @if(isset($category->options['categoryImage']['size']))
      <input type="hidden" data-id="mainImage" class="json-image"
             value="{{ e(json_encode($category->options['categoryImage']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover" data-img-class="block h-[234px] sm:h-[260px] md:h-[300px] lg:h-[387px] xl:h-[491px] w-full object-cover">
    @elseif(isset($content->image_data['mainImage']['size']))
      <input type="hidden" data-id="mainImage" class="json-image"
             value="{{ e(json_encode($content->image_data['mainImage']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover" data-img-class="block h-[234px] sm:h-[260px] md:h-[300px] lg:h-[387px] xl:h-[491px] w-full object-cover">
    @endif

  </div>
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-6 sm:py-9 md:py-12">
    <div class="flex justify-between items-center">
      <div class="w-6 hidden md:block"></div>
      <h1 class="flex-1 d-headline-1 m-headline-1 text-left md:text-center">{{ $category->title ?? $seo['title'] ?? 'Каталог' }}</h1>
      <div class="w-6">
        <button type="button" id="filter-toggle" class="block lg:hidden">
          <svg width="24" height="25" viewBox="0 0 24 25" fill="none" class="pointer-events-none"
               xmlns="http://www.w3.org/2000/svg">
            <path d="M17.5 22.5L17.5 16.5" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10"
                  stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M17.5 6.5L17.5 2.5" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10"
                  stroke-linecap="round" stroke-linejoin="round"/>
            <path
              d="M14 10.5C14 12.433 15.567 14 17.5 14C19.433 14 21 12.433 21 10.5C21 8.567 19.433 7 17.5 7C15.567 7 14 8.567 14 10.5Z"
              stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
              stroke-linejoin="round"/>
            <path d="M6.5 22.5L6.5 18.5" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10"
                  stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M6.5 8.5L6.5 2.5" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                  stroke-linejoin="round"/>
            <path
              d="M3 14.5C3 16.433 4.567 18 6.5 18C8.433 18 10 16.433 10 14.5C10 12.567 8.433 11 6.5 11C4.567 11 3 12.567 3 14.5Z"
              stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
              stroke-linejoin="round"/>
          </svg>
        </button>
      </div>
    </div>
  </div>
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16">
    <div id="wrapper" class="flex relative">
      <div id="leftMenu"
           class="hidden bg-myLightGray lg:block min-w-[260px] md:w-[24.936061%] relative top-0 border-r border-r-myGreen sm:mr-[15px] md:mr-[45px] lg:mr-[74px] xl:mr-[104px] 2xl:mr-[148px]">
        <div id="leftMenu-content" class="relative">
          <div id="filter" data-da="body,1,1023"
               class="pb-6 sm:pb-9 md:pb-12 space-y-6 px-6 z-20 fixed top-0 right-0 w-full max-w-[390px] h-screen bg-myLightGray shadow-xl transform translate-x-full transition-transform duration-300 overflow-y-auto lg:shadow-none lg:relative lg:top-auto lg:right-auto lg:overflow-y-visible lg:translate-x-0 lg:bg-transparent">
            <div class="flex justify-end p-9 px-3 lg:hidden">
              <button type="button" id="filter-close" class="block">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="pointer-events-none"
                     xmlns="http://www.w3.org/2000/svg">
                  <path d="M2 2L22 22M2 22L22 2" stroke="#2C2E35" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </button>
            </div>
{{--            <div>--}}
{{--              <div class="text-sm font-medium lh-none">Ваш город</div>--}}
{{--              <div class="relative group dropdown">--}}
{{--                <button class="nav-parent h-[38px] flex items-center w-full text-2xl">--}}
{{--                  <span>Волгоград</span>--}}
{{--                  <img src="{{ asset('/img/icons/arrow-bottom-big.svg') }}" alt="Категорииs"--}}
{{--                       class="nav-arrow ml-[6px] transform">--}}
{{--                </button>--}}
{{--                <div class="dropdown-content hidden rounded-lg space-y-3 text-xl mt-3 pl-8">--}}
{{--                  <a href="#" class="nav-link flex items-center font-medium lh-none">Волгоград</a>--}}
{{--                  <a href="#" class="nav-link flex items-center font-medium lh-none">Казань</a>--}}
{{--                  <a href="#" class="nav-link flex items-center font-medium lh-none">Краснодар</a>--}}
{{--                  <a href="#" class="nav-link flex items-center font-medium lh-none">Москва</a>--}}
{{--                  <a href="#" class="nav-link flex items-center font-medium lh-none">Нижний Новгород</a>--}}
{{--                  <a href="#" class="nav-link flex items-center font-medium lh-none">Новосибирск</a>--}}
{{--                  <a href="#" class="nav-link flex items-center font-medium lh-none">Самара</a>--}}
{{--                  <a href="#" class="nav-link flex items-center font-medium lh-none">Санкт-Петербург</a>--}}
{{--                  <a href="#" class="nav-link flex items-center font-medium lh-none">Тюмень</a>--}}
{{--                  <a href="#" class="nav-link flex items-center font-medium lh-none">Уфа</a>--}}
{{--                </div>--}}
{{--              </div>--}}
{{--            </div>--}}
{{--            <div class="flex items-center justify-between">--}}
{{--              <label for="express" class="block font-medium text-2xl">Экспресс-доставка</label>--}}
{{--              <x-public.checkbox id="express"/>--}}
{{--            </div>--}}
            <div class="flex items-center justify-between">
              <label for="discount" class="block font-medium text-2xl">Скидка</label>
              <x-public.checkbox name="discount" id="discount" :checked="request()->discount ? true : false" data-default="{{ request()->discount ? 1 : 0 }}"/>
            </div>
            <div class="flex items-center justify-between">
              <label for="in_stock" class="block font-medium text-2xl">В наличии</label>
              <x-public.checkbox name="in_stock" id="in_stock" :checked="request()->in_stock ? true : false" data-default="{{ request()->in_stock ? 1 : 0 }}"/>
            </div>
            <div>
              <div class="relative group dropdown">
                @if(isset($category))
                  <input type="hidden" name="category" value="{{ $category->id }}" data-default="{{ $category->id }}">
                @endif
                @if(request()->get('search'))
                  <input type="hidden" name="search" value="{{ request()->get('search') }}" data-default="{{ request()->get('search') }}">
                @endif
                @if(request()->get('preorder'))
                  <input type="hidden" name="preorder" value="{{ request()->get('preorder') }}" data-default="{{ request()->get('preorder') }}">
                @endif
                <button class="nav-parent h-[38px] flex items-center w-full text-2xl">
                  <span>Категория</span>
                  <img src="{{ asset('/img/icons/arrow-bottom-big.svg') }}" alt="Категории"
                       class="nav-arrow ml-[6px] transform">
                </button>
                <div class="dropdown-content hidden rounded-lg space-y-3 mt-3 pl-8 text-xl">
                  <a href="{{ route('product.catalog') }}">Все категории</a>
                  @foreach(\App\Models\Category::catalog()->get() as $cat)
                    <a href="{{ route('catalog.category', $cat->slug) }}" class="nav-link flex items-center font-medium lh-none">{{ $cat->title }}</a>
                  @endforeach
                </div>
              </div>
            </div>
{{--            <div>--}}
{{--              <div class="relative group dropdown">--}}
{{--                <button class="nav-parent h-[38px] flex items-center w-full text-2xl">--}}
{{--                  <span>Для кого</span>--}}
{{--                  <img src="{{ asset('/img/icons/arrow-bottom-big.svg') }}" alt="Для кого"--}}
{{--                       class="nav-arrow ml-[6px] transform">--}}
{{--                </button>--}}
{{--                <div class="dropdown-content hidden rounded-lg space-y-3 mt-3 pl-8 text-xl">--}}
{{--                  <a href="#" class="nav-link flex items-center font-medium lh-none">Для женщин</a>--}}
{{--                  <a href="#" class="nav-link flex items-center font-medium lh-none">Для детей</a>--}}
{{--                  <a href="#" class="nav-link flex items-center font-medium lh-none">Для всех</a>--}}
{{--                </div>--}}
{{--              </div>--}}
{{--            </div>--}}
            <div>
{{--              <div class="relative group dropdown">--}}
{{--                <button class="nav-parent h-[38px] flex items-center w-full text-2xl">--}}
{{--                  <span>Цена</span>--}}
{{--                  <img src="{{ asset('/img/icons/arrow-bottom-big.svg') }}" alt="Категорииs"--}}
{{--                       class="nav-arrow ml-[6px] transform @if(request()->minPrice || request()->maxPrice) rotate-180 @endif">--}}
{{--                </button>--}}
{{--                <div class="dropdown-content @if(!request()->minPrice && !request()->maxPrice) hidden @endif rounded-lg space-y-3 mt-3 pl-8">--}}
{{--                  <div class="flex items-center">--}}
{{--                    <label for="filter-price-from">От</label>--}}
{{--                    <input type="text" id="filter-price-from" name="minPrice"--}}
{{--                           class="numeric-field cormorantInfant bg-transparent ring-0 focus:ring-0 focus:border-b-myGreen border-0 border-b border-b-myGreen w-[65px] h-5 p-0 text-center mx-3 placeholder-myGray"--}}
{{--                           placeholder="{{ $minPrice }}" value="{{ request()->minPrice }}" data-default="{{ request()->minPrice }}">--}}
{{--                    <label for="filter-price-until">до</label>--}}
{{--                    <input type="text" id="filter-price-until" name="maxPrice"--}}
{{--                           class="numeric-field cormorantInfant bg-transparent ring-0 focus:ring-0 focus:border-b-myGreen border-0 border-b border-b-myGreen w-[65px] h-5 p-0 text-center mx-3 placeholder-myGray"--}}
{{--                           placeholder="{{ $maxPrice }}" value="{{ request()->maxPrice }}" data-default="{{ request()->maxPrice }}">--}}
{{--                  </div>--}}
{{--                </div>--}}
{{--              </div>--}}
            </div>
            <div class="text-center">
              <x-public.primary-button type="button" id="filterButton" class="w-full max-w-[260px]" style="display: none">Показать (N товаров)</x-public.primary-button>
            </div>
          </div>
        </div>
      </div>
      <!-- Main Content -->
      <div class="flex-1">
        <div class="flex justify-end mb-6" data-da="#filter,1,767">
          <select name="order_by" id="order_by" class="w-full lg:w-auto bg-transparent ring-0 focus:ring-0 border-black focus:border-black h-9 lh-none pl-6 py-0 w-[240px] subtitle-2">
            <option value="default">По популярности</option>
            <option value="price|asc">По возрастанию цены</option>
            <option value="price|desc">По убыванию цены</option>
            <option value="name|asc">От А до Я</option>
            <option value="name|desc">От Я до А</option>
          </select>
        </div>
{{--        <x-public.catalog class="flex flex-wrap -mx-2 md:-mx-4.5 -my-4.5" :filtres="['category_id' => $category->id ?? '', 'search' => request()->search ?? '', ...request()->toArray()]"/>--}}
        <div class="flex flex-wrap -mx-2 md:-mx-3" id="catalog">
          @forelse($products as $product)
            <x-public.product-item id="{{ $product->id }}" class="w-1/2 md:w-1/3 lg:w-1/2 xl:w-1/3 px-2 md:px-3 py-2 md:py-3 flex flex-col justify-between" :product="$product"/>
          @empty

            <div class="text-center p-12 d-headline-4 m-headline-3 uppercase text-gray-400 w-full">Здесь пусто</div>

          @endforelse
        </div>
      </div>
    </div>
    <div class="flex justify-center mt-12">
      <x-public.primary-button type="button" id="loadMore" class="md:h-13 md:w-full md:max-w-[285px] mx-auto">Показать еще</x-public.primary-button>
      <input type="hidden" name="page" id="loader-page" value="{{ $products->currentPage() + 1 }}">
      <script>
        window.loadRoute = @json(route('product.loadProducts'))
      </script>
    </div>
  </div>

{{--  <script>--}}
{{--    new Swiper('.guests-swiper', {--}}
{{--      // // Optional parameters--}}
{{--      // direction: 'vertical',--}}
{{--      // loop: true,--}}
{{--      slidesPerView: "auto",--}}
{{--      preloadImages: false,--}}
{{--      lazy: true,--}}
{{--      cssMode: true,--}}
{{--      mousewheel: true,--}}
{{--      // And if we need scrollbar--}}
{{--      // scrollbar: {--}}
{{--      //   el: '#products-01 .swiper-scrollbar',--}}
{{--      // },--}}
{{--      pagination: {--}}
{{--        el: ".swiper-pagination",--}}
{{--      },--}}
{{--    });--}}
{{--  </script>--}}
  <script>
    // chat gpt Sticky Menu TailwindJS

    // document.addEventListener('DOMContentLoaded', function () {
    //   const leftMenu = document.getElementById('leftMenu');
    //   const leftMenuContent = document.getElementById('leftMenu-content');
    //   const wrapper = document.getElementById('wrapper');
    //
    //   window.addEventListener('scroll', handleScroll);
    //
    //   function handleScroll() {
    //     const wrapperHeight = wrapper.offsetHeight;
    //     const menuHeight = leftMenu.offsetHeight;
    //     const contentHeight = leftMenuContent.offsetHeight;
    //
    //     const wrapperTop = wrapper.getBoundingClientRect().top;
    //     const wrapperBottom = wrapper.getBoundingClientRect().bottom;
    //     const menuBottom = leftMenu.getBoundingClientRect().bottom;
    //
    //     if (wrapperHeight <= window.innerHeight) return;
    //
    //     // 2.2
    //     if (contentHeight <= window.innerHeight) {
    //       if (window.scrollY >= -wrapperTop) {
    //         leftMenuContent.style.position = 'fixed';
    //         leftMenuContent.style.top = '0px';
    //       }
    //       if (window.innerHeight + window.scrollY >= wrapperBottom) {
    //         leftMenuContent.style.position = 'absolute';
    //         leftMenuContent.style.bottom = '0px';
    //         leftMenuContent.style.top = 'auto';
    //       }
    //     }
    //     // 2.3
    //     else {
    //       if (menuBottom - contentHeight <= window.innerHeight) {
    //         leftMenuContent.style.position = 'fixed';
    //         leftMenuContent.style.bottom = '0px';
    //         leftMenuContent.style.top = 'auto';
    //       } else {
    //         leftMenuContent.style.position = 'fixed';
    //         leftMenuContent.style.top = '0px';
    //         leftMenuContent.style.bottom = 'auto';
    //       }
    //       if (window.scrollY <= -wrapperTop) {
    //         leftMenuContent.style.position = 'absolute';
    //         leftMenuContent.style.top = '0px';
    //         leftMenuContent.style.bottom = 'auto';
    //       }
    //     }
    //   }
    // });
  </script>
</x-app-layout>
