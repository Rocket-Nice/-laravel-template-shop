@extends('layouts.admin_layout_light')

@section('content')
  <div class="card">
    <div class="card-header" id="action-box" style="display: none;">
      <div class="row align-items-center">
        <div class="col-lg-4 col-12">
          <select name="action" form="action" class="form-control" id="do_action">
            <option>Действие с выбранными</option>
            <optgroup label="Получить файл">
              <option value="group">Объединить</option>
            </optgroup>
            <optgroup label="Установить статус">
              <option value="print_true">Напечатан</option>
              <option value="print_false">Не напечатан</option>
            </optgroup>
          </select>
        </div>
        <div class="col-lg col d-flex justify-content-end">
          <button class="btn btn-primary" id="actioncell_submit" form="action">Применить</button>
        </div>
      </div>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped projects">
          <thead>
          <tr>
            <th style="width: 3%">
              <input type="checkbox" class="action" id="check_all">
            </th>
            <th style="width: 35%">
              Файл
            </th>
            <th style="width: 20%">
              Комментарий
            </th>
            <th class="text-center">
              Количество<br/>этикеток
            </th>
            <th class="text-center">
              Уникальных<br/>заказов
            </th>
            <th>
              Дата создания
            </th>
            <th>

            </th>
          </tr>
          </thead>
          <tbody>
          @foreach($tickets as $ticket)
            <tr class="file
                  @if($ticket->is_author)
                    {{ ' author' }}
                  @endif
                  @if($ticket->tickets->count()==0 && $ticket->count_pages > $ticket->orders()->count())
                    {{ ' bg-warning' }}
                  @endif"
            >
              <td>
                @if($ticket->id)
                  <input type="checkbox" name="ticket_ids[]" form="action" value="{{ $ticket->id }}" class="action" id="checkbox_{{ $ticket->id }}">
                @endif
              </td>
              <td>
                @if(isset($ticket->data['printed'])&&$ticket->data['printed'])
                {{ '✅ ' }}
                @endif
                  @if($ticket->tickets->count())
                    <a href="javascript:;" data-src="{!! route('admin.tickets.ticket_split', $ticket->id) !!}" data-fancybox data-type="iframe" class="badge badge-success" style="font-weight:normal;font-size: 1.1em;">{{ denum($ticket->tickets->count(), $string = ['%d файл','%d файла','%d файлов']) }}</a>
                  @endif
                  @if($ticket->parent)
                    <span class="badge badge-warning" style="font-weight:normal;font-size: 1.1em;">Объединен</span> <a href="{{ $ticket->file_path }}" target="_blank">{{ $ticket->file_name }}</a>
                  @else
                    <a href="{{ $ticket->file_path }}" target="_blank">{{ $ticket->file_name }}</a> ({{ $ticket->size }})
                  @endif

                  @if(isset($ticket->data['builder_file']))
                    <br/><a href="{{ url($ticket->data['builder_file']) }}" target="_blank" class="text-secondary" style="text-decoration: underline">Косметички</a>
                  @elseif(isset($ticket->file_creating)&&$ticket->file_creating)
                    <br/><span style="color:red">Файл с косметичкой формирется</span>
                  @endif
                  @if(isset($ticket->data['cart']))
                    <br/><a href="{{ url($ticket->data['cart']) }}" target="_blank" class="text-secondary" style="text-decoration: underline">состав заказов</a>
                  @endif
              </td>
              <td style="font-size: .8em" id="comment-{{ $ticket->id }}">{{ $ticket->data['comment'] ?? '' }}</td>
              <td class="text-center">{{ $ticket->count_pages }}</td>
              <td class="text-center">
                @if($ticket->tickets->count())
                  {{ $ticket->getOrders() }}
                @else
                  {{ $ticket->orders()->count() }}
                @endif
              </td>
              <td>{{ date('d.m.Y H:i:s', strtotime($ticket->created_at)) }}</td>
              <td>
                <a href="#" data-toggle="modal" data-target="#modal-default" onclick="setComment({{ $ticket->id }});"><i class="mx-2 text-secondary fas fa-comment-alt"></i></a>
                <a href="{{ route('admin.tickets.invoice', $ticket->id) }}" target="_blank"><i class="mx-2 text-secondary fas fa-file-alt"></i></a>
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <!-- /.card-body -->
  </div>
  <form action="{{ route('admin.tickets.printed') }}" id="action" method="POST">
    @csrf
    @method('PUT')
  </form>
  <div class="modal fade" id="modal-default">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Добавить комментарий</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="{{ route('admin.orders.tickets_comment') }}" method="post" id="ticket_comment">
            @csrf
            <input type="hidden" name="ticket_id" id="comment_ticket_id">
            <div class="form-group">
              <label>Комментарий</label>
              <textarea class="form-control" name="comment" rows="3" placeholder="..."></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer justify-content-end">
          <button type="submit" class="btn btn-primary" form="ticket_comment">Сохранить</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- /.modal -->
@endsection

@section('style')
  <link href="{{ asset('libs/@fancyapps/ui/dist/fancybox.css') }}" rel="stylesheet">
@endsection
@section('script')
  <script src="{{ asset('libs/@fancyapps/ui/dist/fancybox.umd.js') }}"></script>
  <script>
    function setComment(id){
      document.getElementById('comment_ticket_id').value = id;
    }
    $(document).ready(function(){
      $('#check_all').change(function(){
        if ($(this).is(':checked')){
          $('.action').prop('checked', true).trigger('change');
        }else{
          $('.action').prop('checked', false).trigger('change');
        }
      });
      $('body').on('change', '.action', function(){
        if ($('.action').is(':checked')){
          $('#action-box').show();
        }else{
          $('#action-box').hide();
        }
      });
    });
    $('#ticket_comment').submit(function(e){
      e.preventDefault();
      var url = $(this).attr('action'),
        id = $(this).find('[name="ticket_id"]').val();
      comment = $(this).find('[name="comment"]').val();
      data = $(this).serialize();
      $.ajax({
        type: 'POST',
        url: url,
        data: data,
        success: function(response) { //Если все нормально
          $(document).Toasts('create', {
            class: 'bg-success',
            title: 'Успех',
            body: 'Комментарий успешно добавлен к файлу'
          });
          $('#comment-'+id).text(comment);
          $('#modal-default').modal('toggle');
        }
      });
    });
  </script>
@endsection
