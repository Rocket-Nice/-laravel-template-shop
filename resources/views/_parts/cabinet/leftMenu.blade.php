<div class="d-text-body m-text-body">
  @if(auth()->user()->hasPermissionTo('Доступ к админпанели'))
  <div>
    <a href="{{ route('admin.page.index') }}">Админпанель</a>
  </div>
  @endif
  <div>
    <a href="https://t.me/dermatolog_lm_bot" target="_blank" class="leading-none block">Консультация врача-дерматолога</a>
  </div>
  <div>
    <a href="{{ route('cabinet.order.index') }}">Мои заказы</a>
  </div>
  <div>
    <a href="{{ route('cabinet.profile.index') }}">Мои данные</a>
  </div>
    @if(auth()->user()->promocodes()->exists())

      <div>
        <a href="{{ route('cabinet.discounts') }}">Мои скидки</a>
      </div>
    @endif
  <div>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
      @csrf
    </form>
    <a onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Выход</a>
  </div>
</div>
