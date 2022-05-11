@extends('cp.cp_tk')

@section('content_title') Загрузка названий стран @endsection


@section('content')
<div class="row">
    <div class="col-md-6 col-sm-6 col-xs-6">
    </div>
    <div class="col-md-6 col-sm-6 col-xs-6">
    </div>
</div>



<form id="wiki-form">
<!---- CONTENT: ---->
<div class="row">

    <div class="col-md-6">
        
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
    <div class="box-body">
@include('cp.formTemplates.number', $param = [
    'label' => 'С какой страницы начать:',
    'name' => 'page',
    'value' => $currentPage,
    'old_value' => old('page'),
    'placeholder' => 'С какой страницы начать',
    'class' => 'form-control',
    'id' => 'column_page',
    'min' => 1,
    'max' => 9999999,
    'step' => 1,
    'autocomplete' => 'off',
    'required' => true,
    'errors' => $errors->has('page') ? $errors->getMessages()['page'] : null,
])
    </div>
    
    <div class="box-footer">
        <span class="btn btn-success" id="save-page">Сохранить</a>
    </div>
</div> 
        
        
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
    <div class="box-body">
      <span class="btn btn-success" id="start-download">Начать загрузку</a>
    </div>
    <div class="overlay" style="display: none;" id="start-spinner">
        <i class="fa fa-refresh fa-spin"></i>
    </div>
</div>

<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
    <div class="box-body">

<table class="table table-striped">
    <tbody><tr>
      <th>Информация:</th>
      <th>Детали:</th>
      <th style="width: 40px"></th>
    </tr>
    
    <tr>
        <td>Количество объектов:</td>
        <td><span id="objects-count"></span></td> <!-- шт -->
        <td></td>
    </tr>
    
    <tr>
        <td>Загружено:</td>
        <td><span id="saved-count">0</span></td> <!-- шт -->
        <td></td><!-- Дата -->
    </tr>
    
    <tr>
        <td>Дубликатов:</td>
        <td><span id="dup-count">0</span></td> <!-- шт -->
        <td></td><!-- Дата -->
    </tr>
    
    
    <tr>
        <td>Страница:</td>
        <td><span id="current-page"></span></td> <!-- текущая -->
        <td><span id="pages-count"></span></td><!-- количество страниц -->
    </tr>
    
    <tr>
      <td>Прогресс:</td>
      <td>
        <div class="progress progress-xs">
          <div class="progress-bar progress-bar-green" style="width: 0%" id="progress-bar"></div>
        </div>
      </td>
      <td><span class="badge bg-green" id="progress-precentage">0%</span></td>
    </tr>

</tbody></table>
    
    </div>
</div>

<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
    <div class="box-header ui-sortable-handle">
        <h3 class="box-title">Ошибки:</h3>    
    </div>

<div class="box-body" id="errors">

</div>

</div>

    
    </div>
    
    <div class="col-md-6">

<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
    <div class="box-header ui-sortable-handle">
        <h3 class="box-title">Последний загруженный объект:</h3>    
    </div>

<div class="box-body" id="last-obj">

</div>

</div>
    
    
    </div>

    
</div>
<!---- /CONTENT ---->
</form>


<!---- CONTENT: ---->
<div class="row">

    <div class="col-md-12">
        

    </div>
    
</div>
<!---- /CONTENT ---->




@endsection

@section('scripts')
<script>
var pegPage = 10;

$('body').on('click', '#start-download', function() {
    $('#start-spinner').toggle();
    startDownload();
    return false;
});



function startDownload() {
    
    console.clear();
    console.log('Начинаю загрузку!');
    
    var objectsCount = 0;
    var pagesCount = 0;
    var currentPage = 0;
    var switcher = 0; // 0 - парсим, 1 - ждем, 2 - останавливаем парсер
    
    // Начинаю загрузку:
    $.ajax({
        type: 'post',
        data: {
            method: 'allCountriesCount'
        },
        url: '{{URL::to('/')}}/api/contries_count'
    })
    .done (function (response, textStatus, xhr) {
        
        var recordsCount = response.count.data.results.bindings[0].HowManyTriples.value;
        
        console.log('WikiData, количество стран: ' + recordsCount);
        $('#objects-count').text(recordsCount);
        objectsCount = recordsCount;

        // Запускаю программу каждую секунду:
        setInterval(function() {

            // Если парсер не остановлен:
            if(switcher != 2) {
        
                // Узнаю где парсер остановился:
                $.ajax({
                    type: 'post',
                    url: '{{URL::to('/')}}/api/contries_last_page'
                })
                .done (function (response, textStatus, xhr) {

                    currentPage = parseInt(response.current_page);
                    pagesCount = Math.ceil(objectsCount / pegPage);
                    
                    calcMetaData(currentPage, pagesCount, Math.ceil((currentPage / pagesCount) * 100));
                


                    // Если можно брать страницу:
                    if(switcher == 0) {
                        
                        // Если страниц больше нет:
                        if(currentPage >= pagesCount) {
                            switcher = 2;
                            $('#start-spinner').toggle();
                            console.log('Парсер скачал последнюю страницу: ' + pagesCount);
                        } else {
                        
                            // Режим ожидания:
                            switcher = 1;
                        
                            // Даю запрос на парсинг:
                            $.ajax({
                                type: 'post',
                                data: {
                                    page: currentPage,
                                    limit: pegPage,
                                    method: 'allCountries'
                                },
                                url: '{{URL::to('/')}}/api/contries_parse_page'
                            })
                            .done (function (response, textStatus, xhr) {

                                var items = response.data.results.bindings;
                                
                                if(items.length > 0) {
                                    $.each(items, function(i, obj) {
                                        addNewCountry(obj);
                                    });  
                                }
                                
                                // Даю добро на продолжение парсинга:
                                switcher = 0;
                                savePage(currentPage + 1);
                               
                            }).fail(function (response) {
                                
                                switcher = 2;
                                $('#start-spinner').toggle();
                                $('#errors').append('<p>' + response.responseJSON.exception + '</p>');
                                $('#errors').append('<p>' + response.responseJSON.message + '</p>');
                            }).then(function(response, textStatus, xhr) {
 //console.log(response);
                            });
  
                        }
                        
                    } else  {
                        // Пропускаю шаг: 
                    }
                    
                }); // <- Текущая страница для парсинга
            
            }
            
        }, 1000); // <- Итерации
        
    }).fail(function (response) {
        // Не удалось узнать количество:
    }).then(function(response, textStatus, xhr) { 
        
    });

}

function calcMetaData(currentPage, pagesCount, percantage) {

    // console.log('Текущая страница для загрузки: ' + currentPage);
    // console.log('Общее количество страниц для загрузки: ' + pagesCount);
    
    $("#progress-bar").css("width", percantage  + "%"); // set value
    $('#current-page').text(currentPage);
    $('#pages-count').text(pagesCount);
    $('#progress-precentage').text(percantage + '%');
   
}

function savePage(page) {
    console.log('сохраняю страницу: ' + page);
    $.ajax({
        type: 'post',
        data: {
            page: page
        },
        url: '{{URL::to('/')}}/api/contries_save_page'
    })
    .done (function (response, textStatus, xhr) {
        // Даю добро на продолжение парсинга:
        switcher = 0;
    });
}

$('body').on('click', '#save-page', function() {
    savePage($('#column_page').val());
    location.reload();
    return false;
});


function addNewCountry(obj) {

    console.log(obj.item.value, obj.name.value);

    $.ajax({
        type: 'post',
        data: {
            wiki_id: obj.item.value.split('/').pop(),
            wiki_link: obj.item.value,
            name_ru: obj.name.value,
            code: obj.code.value,
            moderated: 0
        },
        url: '{{URL::to('/')}}/api/add_new_country'
    })
    .done (function (response, textStatus, xhr) {
        
        var savedCount = parseInt($('#saved-count').text());
        var dupCount = parseInt($('#dup-count').text());
        
        if(response.duplicate == true) {
            $('#last-obj').prepend('<div class="last-objects text-danger">Пропускаю дубликат: ' +response.country.name_ru + '</div>' );
            $('#dup-count').text(dupCount + 1);
        }
        
        if(response.duplicate == false) {
            $('#last-obj').prepend('<div class="last-objects">Сохраняю: ' +response.country.name_ru + '</div>' );
            $('#saved-count').text(savedCount + 1);
        }
    
        var count = $(".last-objects").length;
        if(count > 30) {
            $(".last-objects")[count - 1].remove();
        }
    });
    
}


</script>
@endsection