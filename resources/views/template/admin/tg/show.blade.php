@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
      <a href="{{ route('admin.tg_notifications.index') }}" class="button button-secondary mb-2 md:mb-0">Назад</a>
    @endif
  </x-slot>
  <div class="mx-auto">
    <div id="chatContainer" class="flex-1 p-0 justify-between flex flex-col">
      <div id="messages"
           class="flex flex-1 flex-col space-y-4 p-3 overflow-y-auto custom-scrollbar">
        @foreach($messages->sortBy('time') as $message)
          <div class="chat-message" id="message-{{ $message->id }}">
            <div class="flex items-end @if($message->outgoing_message) justify-end  @endif">
              <div class="flex flex-col space-y-2 text-xs max-w-md mx-2 @if($message->outgoing_message) order-1 @else order-2 @endif items-end">
                <div><span
                    class="px-4 py-2 rounded-lg inline-block @if($message->outgoing_message) @if(!$message->delivered) bg-gray-500 @else bg-blue-600 @endif rounded-br-none text-white @else bg-gray-300 text-gray-600 rounded-bl-none @endif ">{!! $message->text !!}</span>
                </div>
              </div>
              <img src="@if($message->outgoing_message){{ asset('apple-touch-icon-144x144.png' )}}@else{{ $tgChat->image }}@endif" alt="@if($message->outgoing_message){{ config('app.name') }}@else{{ $tgChat->getChatName() }}@endif" title="{{ $message->time ? $message->time->format('d.m.Y H:i:s') : '' }}" class="w-8 h-8 rounded-full @if($message->outgoing_message) order-2 @else order-1 @endif">
            </div>
          </div>
        @endforeach


      </div>
      <form action="{{ route('admin.tg_notifications.send', $tgChat->id) }}" method="post" id="sendMessage" @if(!$tgChat->active) disabled @endif>
        @csrf
        <div class="p:2 sm:p-6 border-t-2 border-gray-200 px-4 pt-4 mb-2 sm:mb-0">
          <div class="relative flex">
            <textarea name="message" id="message" placeholder="Введите сообщение" class="resize-none custom-scrollbar w-full focus:outline-none focus:placeholder-gray-400 text-gray-600 placeholder-gray-600 pl-2 bg-gray-200 rounded-md py-3" required></textarea>

            <div class="items-center inset-y-0 flex">
              {{--              <button type="button"--}}
              {{--                      class="inline-flex items-center justify-center rounded-full h-8 w-8 sm:h-10 sm:w-10 transition duration-500 ease-in-out text-gray-500 hover:bg-gray-300 focus:outline-none">--}}
              {{--                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"--}}
              {{--                     stroke="currentColor"--}}
              {{--                     class="h-5 w-5 sm:h-6 sm:w-6 text-gray-600">--}}
              {{--                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"--}}
              {{--                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>--}}
              {{--                </svg>--}}
              {{--              </button>--}}
              {{--              <button type="button"--}}
              {{--                      class="inline-flex items-center justify-center rounded-full h-8 w-8 sm:h-10 sm:w-10 transition duration-500 ease-in-out text-gray-500 hover:bg-gray-300 focus:outline-none">--}}
              {{--                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"--}}
              {{--                     stroke="currentColor"--}}
              {{--                     class="h-5 w-5 sm:h-6 sm:w-6 text-gray-600">--}}
              {{--                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"--}}
              {{--                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>--}}
              {{--                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"--}}
              {{--                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>--}}
              {{--                </svg>--}}
              {{--              </button>--}}
              <button type="submit"
                      class="ml-1 inline-flex items-center justify-center rounded-lg p-2 sm:p-3 transition duration-500 ease-in-out text-white bg-blue-500 hover:bg-blue-400 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                     class="h-6 w-6 transform rotate-90">
                  <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </form>

    </div>
  </div>
  <div class="bg-gray-500 order-1 order-2"></div>
  <input type="hidden" name="page" id="loader-page" value="{{ $messages->currentPage() + 1 }}">
  <script>
    function resizeChat(){
      const mainContainer = document.querySelector('.bg-white.rounded.shadow-sm.m-4.p-4.mt-0')

      const rect = mainContainer.getBoundingClientRect();

      const offsetTop = rect.top + window.scrollY;
      mainContainer.style.height = (window.innerHeight - offsetTop)+'px'

      // Получаем все вычисленные стили для этого элемента
      const style = window.getComputedStyle(mainContainer);

      // Получаем вертикальные отступы
      const paddingTop = style.getPropertyValue('padding-top');
      const paddingBottom = style.getPropertyValue('padding-bottom');

      const chatContainer = document.getElementById('chatContainer')
      chatContainer.style.height = (window.innerHeight - offsetTop - parseInt(paddingBottom, 10) - parseInt(paddingTop, 10))+'px'
    }
    resizeChat()

    const messagesContainer = document.getElementById('messages')
    messagesContainer.scrollTop = messagesContainer.scrollHeight
    window.addEventListener('resize', resizeChat)

    // ленивая загрузка собщений
    var loading = false;
    const page = document.getElementById('loader-page')
    var url = @json(route('admin.tg_notifications.messages', $tgChat->id)); // Укажите ваш URL

    function loadProducts(){
      if(loading){
        return false;
      }
      loading = true; // Установите флаг загрузки перед отправкой запроса

      var data = {
        page: page.value
      };
      window.ajax.get(url, data, function(response) {
        const messages = response.data

        messages.forEach((message)=>{
          if(!document.getElementById('message-'+response.message.id)){
            const messageItem = createMessageItem(message)
            const scrollTopBefore = messagesContainer.scrollTop;
            const scrollHeightBefore = messagesContainer.scrollHeight;
            messagesContainer.prepend(messageItem)
            // const height = messageItem.offsetHeight;
            const heightAdded = messagesContainer.scrollHeight - scrollHeightBefore;
            messagesContainer.scrollTop = scrollTopBefore + heightAdded;
          }
        })
        page.value = response.current_page + 1

        if(response.current_page >= response.last_page) {
          return true;
        }
        loading = false; // Снимите флаг загрузки после получения ответа
      });
    }
    function listenNewMessages(){
      var data = {
        page: 1
      };
      window.ajax.get(url, data, function(response) {
        const messages = response.data

        messages.forEach((message)=>{
          if(!document.getElementById('message-'+message.id)){
            const messageItem = createMessageItem(message)
            const scrollTopBefore = messagesContainer.scrollTop;
            const scrollHeightBefore = messagesContainer.scrollHeight;
            messagesContainer.appendChild(messageItem)
            messagesContainer.scrollTop = messagesContainer.scrollHeight
          }else if(message.outgoing_message&&document.getElementById('message-'+message.id).querySelector('.bg-gray-500.rounded-br-none')){
            const messageItem = createMessageItem(message)
            document.getElementById('message-'+message.id).innerHTML = '';
            document.getElementById('message-'+message.id).appendChild(messageItem.querySelector('div'))
          }
        })
        setTimeout(function(){
          listenNewMessages()
        },2000)
      });
    }
    setTimeout(function(){
      listenNewMessages()
    },2000)
    function createMessageItem(message){
      const messageItem = document.createElement('div')
      messageItem.classList.add('chat-message')
      messageItem.id = 'message-'+message.id
      var messageHtml;

      // создаем карточки

      if(message.outgoing_message){
        var color = 'bg-blue-600'
        if(!message.delivered){
          color = 'bg-gray-500'
        }
        messageHtml = `<div class="flex items-end justify-end">
                <div class="flex flex-col space-y-2 text-xs max-w-md mx-2 order-1 items-end">
                  <div><span
                      class="px-4 py-2 rounded-lg inline-block rounded-br-none ${color} text-white ">${message.text}</span>
                  </div>
                </div>
                <img src="${message.image}" alt="${message.name}" title="${message.time}" class="w-8 h-8 rounded-full order-2">
              </div>`
      }else{
        messageHtml = `<div class="flex items-end">
                <div class="flex flex-col space-y-2 text-xs max-w-md mx-2 order-2 items-start">
                  <div><span
                      class="px-4 py-2 rounded-lg inline-block rounded-bl-none bg-gray-300 text-gray-600">${message.text}</span>
                  </div>
                </div>
                <img src="${message.image}" alt="${message.name}" title="${message.time}" class="w-8 h-8 rounded-full order-1">
              </div>`
      }

      messageItem.innerHTML = messageHtml
      return messageItem
    }
    messagesContainer.addEventListener('scroll', () => {
      // Проверяем, достиг ли пользователь верха блока
      if (messagesContainer.scrollTop === 0) {
        loadProducts()
      }
    });

    const sendMessageForm = document.getElementById('sendMessage')
    sendMessageForm.addEventListener('submit', function(e){
      e.preventDefault()
      var url = sendMessageForm.action; // Укажите ваш URL
      var field = sendMessageForm.querySelector('#message')
      var data = {
        message: field.value
      };
      window.ajax.post(url, data, function(response) {
        if(response.message && !document.getElementById('message-'+response.message.id)){
          const messageItem = createMessageItem(response.message)
          messagesContainer.appendChild(messageItem)
          field.value = ''
          messagesContainer.scrollTop = messagesContainer.scrollHeight
        }

      })
    })
    document.getElementById('message').addEventListener('keydown', function(event) {
      if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        sendMessageForm.querySelector('[type="submit"]').click();
      }
    });
  </script>
</x-admin-layout>
