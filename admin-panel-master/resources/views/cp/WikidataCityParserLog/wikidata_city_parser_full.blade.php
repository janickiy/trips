@extends('cp.cp_tk')

@section('content_title') Парсинг городов из WikiData (Полная версия) @endsection

@section('content')

<!---- CONTENT: ---->
<div class="row">

    <div class="col-md-4">
        
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
    <div class="box-body">
        
    <span class="btn btn-success" id="start">Начать парсинг</span>
    <span class="" id="working" style="display: none;">Идет загрузка...</span>
    <div class="overlay" style="display: none;" id="start-spinner">
        <i class="fa fa-refresh fa-spin"></i>
    </div>
        
    </div>

</div> 

    </div>
    
    <div class="col-md-8">
    
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
    <div class="box-body">
        
<table class="table table-striped">
    <tbody><tr>
      <th>Информация:</th>
      <th>Детали:</th>
      <th style="width: 130px"></th>
    </tr>
     
    <tr>
        <td>Загружено городов, общее кол-во:</td>
        <td><span id="downloaded-count"></span></td>
        <td><span id="cities-count"></span></td>
    </tr>
     
    <tr>
        <td>Требуется модерация:</td>
        <td><span id="on-moderation-count"></span></td>
        <td></td>
    </tr>

    <tr>
        <td>Сейчас в работе:</td>
        <td><span id="current-city"></span></td>
        <td><span id="current-start-date"></span></td>
    </tr>

</tbody></table>
        
    </div>

</div> 
    
    </div>
    
</div>
<!---- /CONTENT ---->



<!---- CONTENT: ---->
<div class="row">

<div class="col-xs-12">
        <div class="box">

<div class="box-body table-responsive no-padding">
    <table class="table table-striped tr-middle">
        <thead>
<tr><th>ID</th>
<th>city_id</th>
<th>Статус</th>
<th>Лог</th>
<th>old_population</th>
<th>Дельта</th>
<th>query_time</th>
<th>Обновлено</th>
</tr><tr>

        </tr></thead>
        
<tbody id="log-table">
</tbody>
        
    </table>
</div>

        </div>
    </div>
    
</div>
<!---- /CONTENT ---->


@endsection

@section('scripts')
<script>
var errors = JSON.parse('{!! json_encode($errors) !!}'); // console.log(errors);
var lastLogId = {{$lastLogId->id}};
var pegPage = 30;
getLogs();

$('body').on('click', '#start', function() {
    $('#start').toggle();
    $('#start-spinner').toggle();
    $('#working').toggle();

    getInfo();
    getCity();
    
    return false;
});

// Сколько осталось:
function getInfo() {
    $.ajax({
        type: 'get',
        url: '{{URL::to('/')}}/api/parser_cities_info'
    })
    .done (function (response, textStatus, xhr) { 
        $.each(response, function(key, value) {
            $('#' + key).text(value);
        });
    });
}


// Текущее задание:
function getCity() {
    $.ajax({
        type: 'get',
        url: '{{URL::to('/')}}/api/parser_get_city_in_work'
    })
    .done (function (response, textStatus, xhr) { 
    
        if(response.currentcity != null) {
            $('#current-city').text(response.currentcity.name);
            $('#current-start-date').text(response.currentstartdate);
            
            parseCity(response.currentcity.id); // console.log(response.currentcity);
        } else {
            $('#start').toggle();
            $('#start-spinner').toggle();
            $('#working').toggle();  
        }
        
    });
}


// Обновить данные города:
function parseCity(id) {
    $.ajax({
        type: 'get',
        url: '{{URL::to('/')}}/api/download_city_from_wiki_data?delta_longitude=0.1&delta_latitude=0.2&city_id=' + id
    })
    .done (function (response, textStatus, xhr) { 
        
        if(response.status == 200) {
            // Если парсинг завершился успешно, иду дальше:
            nextStep();
        } else {
            
            // В случае неудачи, останавливаю:
            $('#start').toggle();
            $('#start-spinner').toggle();
            $('#working').toggle(); 
        }
        
    }).fail(function (response) { 

        // В случае неудачи, останавливаю:
        $('#start').toggle();
        $('#start-spinner').toggle();
        $('#working').toggle(); 

    })
}

function nextStep() {
    getInfo();
    getCity();
}

// Отображаю логи:
function getLogs() {

    $.ajax({
        type: 'post',
        data: {
            lastLogId: lastLogId,
            pegPage: pegPage
        },
        url: '{{URL::to('/')}}/api/get_wikidata_city_parser_logs'
    })
    .done (function (response, textStatus, xhr) { 
        if(response.length > 0) {
            $.each(response, function(key, value) {
                showLog(value);
            });
        }
    });
    

}

function showLog(obj) {
    
    lastLogId = obj.id;
    
    $('#log-table').prepend(`
        <tr class="log-row">
            <td>` + obj.id + `</td>
            <td><a href="{{URL::to('/')}}/cp/cities/edit/` + obj.city.id + `" target="_blank">` + obj.city.name + `</a></td>
            <td>` + obj.category_id + `</td>
            <td>` + errors[obj.category_id][obj.reason_id] + `</td>
            <td>` + obj.old_population + `</td>
            <td>` + obj.population_change + `</td>
            <td>` + obj.query_time + ` сек.</td>
            <td>` + obj.updated_at + `</td>
        </tr>
    `);
    
    var count = $(".log-row").length;
    if(count > pegPage) {
        $(".log-row")[count - 1].remove();
    }
    
}

setInterval(function() {
    getLogs();
}, 1000);

</script>
@endsection