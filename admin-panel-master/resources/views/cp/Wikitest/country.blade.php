@extends('cp.cp_tk')

@section('content_title') Какой стране принадлежит (country, P17) @endsection


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
@include('cp.formTemplates.text', $param = [
    'label' => 'Введите идентификатор элемента с префиксом Q:',
    'name' => 'item',
    'value' => '',
    'old_value' => old('item'),
    'placeholder' => 'Q7525',
    'class' => 'form-control',
    'id' => 'column_item',
    'minlength' => 1,
    'maxlength' => 150,
    'autocomplete' => 'off',
    'required' => true,
    'errors' => $errors->has('item') ? $errors->getMessages()['item'] : null,
])
    </div>
</div>


<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
    <div class="box-body">
      <span class="btn btn-success" id="send">Отправить</a>
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

$('body').on('click', '#send', function() {
    sendRequest();
    return false;
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
           method: 'getCountry',
           q: $('#column_item').val()
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
           method: 'getCountry',
           q: $('#column_item').val()
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