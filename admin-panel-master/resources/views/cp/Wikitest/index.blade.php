@extends('cp.cp_tk')

@section('content_title') Wikitest @endsection


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
@include('cp.formTemplates.select', $param = [
    'label' => 'Выберите метод:',
    'name' => 'method',
    'values' => [
        'clear' => 'Выберите метод',
        'allCountries' => 'Список всех стран',
        'allCountrieWithCodes' => 'Список всех стран с кодами ISO',
        'countriensWithCapitals' => 'Всех страны с их столицами',
        'countriensWithCapitalsAndPopulation' => 'Страны с их столицами и населением',
        'allCapitals' => 'Все столицы',
        'countriensWithMillionaries' => 'Страны с их городами-милионниками',
        'germanCities' => 'Города Германии и население',
        'germany' => 'Города-милионники Германии и население',
        'whyNoBerlin' => 'Почему нет Берлина? (Запрос Валерия)',
        'whyNoBerlinORM' => 'Почему нет Берлина? (ORM)',
        'obamaChildren' => 'Дети Обамы',
        'LargestCitiesOfTheWorld' => 'Самые большие города мира',
        'russianTownCodes' => 'Список всех городов России с телефонными кодами',
    ],
    'selected' => '',
    'class' => 'form-control',
    'id' => 'column_method'
])
    </div>
</div>

<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
    <div class="box-body">
@include('cp.formTemplates.number', $param = [
    'label' => 'Страница:',
    'name' => 'minutes',
    'value' => 1,
    'old_value' => old('minutes'),
    'placeholder' => 'Страница',
    'class' => 'form-control',
    'id' => 'column_page',
    'min' => 1,
    'max' => 999999,
    'step' => 1
])
    </div>
</div>

<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
    <div class="box-body">
@include('cp.formTemplates.number', $param = [
    'label' => 'Количество:',
    'name' => 'minutes',
    'value' => 10,
    'old_value' => old('minutes'),
    'placeholder' => 'Количество',
    'class' => 'form-control',
    'id' => 'column_limit',
    'min' => 1,
    'max' => 999999,
    'step' => 1
])
    </div>
</div>

<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
    <div class="box-body">
      <span class="btn btn-success" id="again">Повторить запрос</a>
    </div>
</div>


      
    </div>
    
    <div class="col-md-6">

<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
    <div class="box-header ui-sortable-handle">
        <h3 class="box-title">SPARQL:</h3>    
    </div>

<div class="box-body">
    <pre id="sparql-data">
    </pre>
</div>

<div class="box-footer">
</div>

</div>
    
    </div>

    
</div>
<!---- /CONTENT ---->
</form>


<!---- CONTENT: ---->
<div class="row">

    <div class="col-md-12">
        
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
    <div class="box-header ui-sortable-handle">
        <h3 class="box-title">Метод: <span id="method_name"></span></h3>    
    </div>

<div class="box-body" id="wiki-data">
</div>

<div class="box-footer">
</div>

</div>

    </div>
    
</div>
<!---- /CONTENT ---->




@endsection

@section('scripts')
<script>
$('#column_method').change(function() {
    $('#column_limit').val(10);
    $('#column_page').val(1);
});

$('body').on('click', '#again', function() {
    sendRequest();
    return false;
});


$('#wiki-form').change(function() {
    sendRequest();
});

function sendRequest() {
    $('#wiki-data').text('');
    $('#wiki-data').append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');

    var method = $('#column_method').val();

    
    if(method == 'clear') {
        $('#sparql-data').text('');
        $('#method_name').text('');
        $('#wiki-data').text('');
    } else {

        getWikiData(method);
        getSparqlData(method);
    
        $('#method_name').text(method);
        
    }   
}

function getWikiData(method) {
    
    var table = '';
    
    $.ajax({
        type: 'post',
        url: '{{URL::to('/')}}/cp/prepared_queries',
        data: {
           method: method ,
           limit: $('#column_limit').val(),
           page: $('#column_page').val()
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .done (function (response, textStatus, xhr) { 
            if(xhr.status == 200) {
                if(response.status == 200) {
                    
                    table += '<table class="table table-striped"><tbody>';        
                    
                    $('#wiki-data').text('');

                    table += '<tr>';
                    
                    if(typeof(response.data.head) == 'object') {
                        $.each(response.data.head.vars, function(id, key) { //console.log(key);
                            table += '<th style="width: 10px">' + key + '</th>';
                        });
                    }
                    
                    table += '</tr>';

                    
                    $.each(response.data.results.bindings, function(i, item) { // console.log('bindings: ', item);
                        
                        table += '<tr>';
                        
                        if(typeof(response.data.head) == 'object') {
                            
                            $.each(response.data.head.vars, function(id, key) { 
                                
                  
                                if(typeof(item[key]) == 'object') {
                                    table += '<td>' + item[key].value + '</td>';
                                } else {
                                    table += '<td></td>';
                                }
                                
                            });
                            
                        }
                        
                        table += '</tr>';
                        
                    });
                    
                    table += '</tbody></table>';
                    
                    // console.log(table);
                    $('#wiki-data').append(table);
                    
                }
            } else {
                //
            }
        
    }).fail(function (response) {
        if(response.status == 500) {
            $('#wiki-data').text('');
            $('#wiki-data').append('Ошибка 500.');
        } else {
        }
    }).then(function(response, textStatus, xhr) { 
        if(xhr.status == 200) {
            if(response.status != 200) {
                $('#wiki-data').text('');
                $('#wiki-data').append('Произошла ошибка на стороне сервера WikiData. (Возможная причина: превышено время обработки запроса)');
            }
        } else {
            $('#wiki-data').text('');
            $('#wiki-data').append('Произошла ошибка на стороне сервера WikiData. (Возможная причина: превышено время обработки запроса)');
        }
  
        // console.log(response);
    });
    
}

function getSparqlData(method) {
    
    $.ajax({
        type: 'post',
        url: '{{URL::to('/')}}/cp/prepared_queries',
        data: {
           sparql: true, 
           method: method,
           limit: $('#column_limit').val(),
           page: $('#column_page').val()
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .done (function (response, textStatus, xhr) {
        if(xhr.status == 200) {
            $('#sparql-data').text('');
            $('#sparql-data').append(response.data);
        } else {
        }
    }).fail(function (response) {
        if(response.status == 500) {
            $('#sparql-data').text('');
            $('#sparql-data').append('<p>' + response.responseJSON.exception + '</p>');
            $('#sparql-data').append('<p>' + response.responseJSON.message + '</p>');
        } else {
        }

    }).then(function(response){ 
        // console.log(response);
    });
    
}


</script>
@endsection