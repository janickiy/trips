@extends('cp.cp_tk')

@section('content_title')  @endsection
@section('content_breadcrumb')
@endsection

@section('content')
Добро пожаловать!
<!---
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">

<div class="box-header ui-sortable-handle">
    <h3 class="box-title">Сводная таблица: </h3>    
</div>

    <div class="box-body">

<table class="table table-striped">

<thead>
    <tr>
      <th style="width: 10px">Свойство</th>
      <th>[0,1000]</th>
      <th>[1001,10000]</th>
      <th>[10001,100000]</th>
      <th>[100001,1000000]</th>
      <th>[1000001,10000000]</th>
      <th>[10000001,100000000]</th>
    </tr>
</thead>

<tbody id="summary_table"></tbody>

</table>
<div class="overlay" id="table-loader">
  <i class="fa fa-refresh fa-spin"></i>
</div>
    </div>
</div>

<div class="box box-success" style="position: relative; left: 0px; top: 0px;">

<div class="box-header ui-sortable-handle">
    <h3 class="box-title">Информация:</h3>    
</div>

    <div class="box-body">

<ul>
    <li><b>name_en, wiki_entity, name_ru</b> - свойства, которые проверяются скриптом на пустоту</li>
    <li><b>[0,1000]</b> - указывает в каком диапазоне населения проиходит проверка</li>
    <li><b><span class="text-success">111</span></b> - зеленый цвет указывает у скольких записей присутствует свойство (данные есть)</li>
    <li><b><span class="text-danger">222</span></b> - красный цвет указывает у скольких записей отсутствует свойство (данных нет, NULL), в скобках процент от общего количества</li>
    <li><b><span class="">333</span></b> - черный цвет указывает общее количество записей в этом диапазоне населения</li>
</ul>
<p>Составление сводной таблицы занимает в среднем 5-10 секунд. Данных много, поэтому не спешите закрывать страничку. Если спиннер крутится слишком долго, попробуйте обновить страничку.</p>
    
    
    </div>
</div>
-->

@endsection

@section('scripts')
<script>
/*
var methods = ['wiki_entity', 'name_en', 'name_ru', 'cases'];
var steps = 4;

methods.forEach(function(element) {
    $.ajax({
        type: 'get',
        url: '{{URL::to('/')}}/api/summary_data',
        data: {
            method: element
        }
    })
    .done (function (response) {       
        $.each(response, function(name, obj) {
            console.log(obj);
            
            var td = '';
            
            $.each(obj, function(gap, array) {
                td += '<td><span class="text-success">' + array['good'] + '</span><br><span class="text-danger">' + array['bad'] + ' (' + percentage(array['bad'], array['total']) + '%)</span><br><span class="">' + array['total'] + '</span></td>';
            });
            
            $('#summary_table').append('<tr><td style="width: 10px">' + name + '</td>' + td +'</tr>');
            
        }); 
        
        steps = steps - 1;
        
        if(steps == 0) {
            $('#table-loader').hide();
        }
        
    });
});

function percentage(value, max) {
    return ((value / max) * 100).toFixed(2);
}
*/
</script>
@endsection