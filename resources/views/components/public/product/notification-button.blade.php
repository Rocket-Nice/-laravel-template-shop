<div class="text-center text-xl bg-gray-200 text-gray-500 h-11 flex justify-center items-center">
  @if(auth()->check())
    @if(!$notification)
      <button onclick="window.productNotification(this, '{{ $slug }}', 'set')">Узнать о поступлении</button>
    @else
      <button onclick="window.productNotification(this, '{{ $slug }}', 'remove')">Сообщим о поступлении</button>
    @endif
  @else
    <a href="javascript:;" class="outline-none" data-src="#authForm" onclick="window.productNotificationBeforeAuth('{{ $slug }}')" data-fancybox-no-close-btn>Узнать о поступлении</a>
  @endif
</div>
