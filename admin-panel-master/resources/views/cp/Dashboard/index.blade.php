@extends('cp.cp_tk')

@section('content_title'){{''}}Dashboard{{''}}@endsection


@section('content')


<!-- ПОЛЬЗОВАТЕЛИ --->
<div class="row">
<div class="col-md-12">

    <div class="box box-success">


    <div class="box-header with-border">
        Количество активных пользователей
    </div>

    <div class="box-body no-padding">
        <div class="col-sm-2 col-xs-6">
            <div class="description-block border-right">
                <br>
                <h5 class="description-header" id="wss_now">...</h5>
                <span class="description-text">WSS сессии</span>
            </div>
        </div>

        <div class="col-sm-2 col-xs-6">
            <div class="description-block border-right">
                <br>
                <h5 class="description-header" id="dau30">...</h5>
                <span class="description-text">DAU 30 дней</span>
            </div>
        </div>
       
        <div class="col-sm-2 col-xs-6">
            <div class="description-block border-right">
                <br>
                <h5 class="description-header" id="mau30">...</h5>
                <span class="description-text">MAU 3 мес.</span>
            </div>
        </div>
       
    </div>
    </div>

</div>
</div>
<!-- /ПОЛЬЗОВАТЕЛИ --->



<!-- ПОЛЬЗОВАТЕЛИ --->
<div class="row">
<div class="col-md-12">

    <div class="box box-success">


    <div class="box-header with-border">
        Пользователи
    </div>

    <div class="box-body no-padding">
    <div class="col-sm-2 col-xs-6">
        <div class="description-block border-right">
            <br>
            <h5 class="description-header" id="user_registrations_all_absolute">...</h5>
            <span class="description-text">Всего</span>
        </div>
    </div>

    <div class="col-sm-2 col-xs-6">
        <div class="description-block border-right">
            <span class="description-percentage text-green" id="user_registrations_email_percentage">...</span>
            <h5 class="description-header" id="user_registrations_email_absolute">...</h5>
            <span class="description-text">e-mail</span>
        </div>
    </div>

    <div class="col-sm-2 col-xs-6">
        <div class="description-block border-right">
            <span class="description-percentage text-green" id="user_registrations_apple_percentage">...</span>
            <h5 class="description-header" id="user_registrations_apple_absolute">...</h5>
            <span class="description-text">apple</span>
        </div>
    </div>

    <div class="col-sm-2 col-xs-6">
        <div class="description-block border-right">
            <span class="description-percentage text-green" id="user_registrations_google_percentage">...</span>
            <h5 class="description-header" id="user_registrations_google_absolute">...</h5>
            <span class="description-text">google</span>
        </div>
    </div>

    <div class="col-sm-2 col-xs-6">
        <div class="description-block border-right">
            <span class="description-percentage text-green" id="user_registrations_facebook_percentage">...</span>
            <h5 class="description-header" id="user_registrations_facebook_absolute">...</h5>
            <span class="description-text">facebook</span>
        </div>
    </div>

    </div>
    </div>

</div>
</div>
<!-- /ПОЛЬЗОВАТЕЛИ --->

<!-- АРТЕФАКТЫ --->
<div class="row">
<div class="col-md-12">

    <div class="box box-success">


    <div class="box-header with-border">
        Артефакты
    </div>

    <div class="box-body no-padding">
    <div class="col-sm-2 col-xs-6">
        <div class="description-block border-right">
            <br>
            <h5 class="description-header" id="artifacts_all_absolute">...</h5>
            <span class="description-text">Всего</span>
        </div>
    </div>

    <div class="col-sm-2 col-xs-6">
        <div class="description-block border-right">
            <span class="description-percentage text-green" id="artifacts_trips_percentage">...</span>
            <h5 class="description-header" id="artifacts_trips_absolute">...</h5>
            <span class="description-text">Поездки</span>
        </div>
    </div>

    <div class="col-sm-2 col-xs-6">
        <div class="description-block border-right">
            <span class="description-percentage text-green" id="artifacts_towns_percentage">...</span>
            <h5 class="description-header" id="artifacts_towns_absolute">...</h5>
            <span class="description-text">Города</span>
        </div>
    </div>

    <div class="col-sm-2 col-xs-6">
        <div class="description-block border-right">
            <span class="description-percentage text-green" id="artifacts_files_percentage">...</span>
            <h5 class="description-header" id="artifacts_files_absolute">...</h5>
            <span class="description-text">Файлы</span>
        </div>
    </div>

    <div class="col-sm-2 col-xs-6">
        <div class="description-block border-right">
            <span class="description-percentage text-green" id="artifacts_note_photos_percentage">...</span>
            <h5 class="description-header" id="artifacts_note_photos_absolute">...</h5>
            <span class="description-text">Фото в заметках</span>
        </div>
    </div>

    </div>
    </div>

</div>
</div>
<!-- /АРТЕФАКТЫ --->


<!-- АРТЕФАКТЫ --->
<div class="row">
<div class="col-md-4">

    <div class="box box-success">


    <div class="box-header with-border">
        Среднее количество на 1 пользователя
    </div>

    <div class="box-body no-padding">
        <div class="col-sm-12 col-xs-12">
        <br>
        <p>
            Артефактов:<span id="artifacts_all_average"></span><br>
            Поездок: <span id="artifacts_trips_average"></span><br>
            Городов: <span id="artifacts_towns_average"></span><br>
        </p>
        </div>
    </div>

</div>
</div>


<div class="col-md-4">

    <div class="box box-success">


    <div class="box-header with-border">
        Файлы пользователей на сервере
    </div>

    <div class="box-body no-padding">
        <div class="col-sm-12 col-xs-12">
        <br>
        <p>
            Количество файлов: <span id="files_count"></span><br>
            Общий объем файлов: <span id="files_max_size"></span><br>
            Средний объем файла: <span id="files_average_size"></span><br>
        </p>
      
        </div>
    </div>

</div>
</div>



</div>
<!-- /АРТЕФАКТЫ --->



@endsection

@section('scripts')
<script>

$.ajax({
    type: 'get',
    url: '/api/dashboard_statistics',
    data: {},
    beforeSend: function(jqXHR, settings) {
        // ...
    }
}).done (function (response, textStatus, xhr) {
    if(xhr.status == 200) {
        console.log("statistics", response);
        
        $("#user_registrations_all_absolute").text(response.data.user_registrations.all.absolute);
        $("#user_registrations_email_absolute").text(response.data.user_registrations.email.absolute);
        $("#user_registrations_apple_absolute").text(response.data.user_registrations.apple.absolute);
        $("#user_registrations_google_absolute").text(response.data.user_registrations.google.absolute);
        $("#user_registrations_facebook_absolute").text(response.data.user_registrations.facebook.absolute);
        
        $("#user_registrations_all_percentage").text(response.data.user_registrations.all.percentage + "%");
        $("#user_registrations_email_percentage").text(response.data.user_registrations.email.percentage + "%");
        $("#user_registrations_apple_percentage").text(response.data.user_registrations.apple.percentage + "%");
        $("#user_registrations_google_percentage").text(response.data.user_registrations.google.percentage + "%");
        $("#user_registrations_facebook_percentage").text(response.data.user_registrations.facebook.percentage + "%");
        
        $("#artifacts_all_absolute").text(response.data.artifacts.all.absolute);
        $("#artifacts_trips_absolute").text(response.data.artifacts.trips.absolute);
        $("#artifacts_towns_absolute").text(response.data.artifacts.towns.absolute);
        $("#artifacts_files_absolute").text(response.data.artifacts.files.absolute);
        $("#artifacts_note_photos_absolute").text(response.data.artifacts.note_photos.absolute);
        
        $("#artifacts_all_percentage").text(response.data.artifacts.all.percentage + "%");
        $("#artifacts_trips_percentage").text(response.data.artifacts.trips.percentage + "%");
        $("#artifacts_towns_percentage").text(response.data.artifacts.towns.percentage + "%");
        $("#artifacts_files_percentage").text(response.data.artifacts.files.percentage + "%");
        $("#artifacts_note_photos_percentage").text(response.data.artifacts.note_photos.percentage + "%");
        
        $("#artifacts_all_average").text(response.data.artifacts.all.average);
        $("#artifacts_trips_average").text(response.data.artifacts.trips.average);
        $("#artifacts_towns_average").text(response.data.artifacts.towns.average);
        
    }
}).fail(function (response) {
    
}).then(function(response){ 
    getStorageStatistics();
}).always(function (response) {
    
});





function getStorageStatistics() 
{
    $.ajax({
        type: 'get',
        url: '<?php echo config('app.data_server_url', "1");?>/api/storage_statistics',
        data: {},
        beforeSend: function(jqXHR, settings) {
            // ...
        }
    }).done (function (response, textStatus, xhr) {
        if(xhr.status == 200) {
                $("#files_count").text(response.data.files_count + " шт.");
                $("#files_max_size").text(response.data.files_max_size + " MB");
                $("#files_average_size").text(response.data.files_average_size + " MB");
        }
    }).fail(function (response) {
        
    }).then(function(response){ 
        getDauMauStatistics();
    }).always(function (response) {
        
    }); 
}

function getDauMauStatistics() 
{
    $.ajax({
        type: 'get',
        url: '/api/trips_statistics/dau_mau',
        data: {
            "type": "current"
        },
        beforeSend: function(jqXHR, settings) {
            // ...
        }
    }).done (function (response, textStatus, xhr) {
        if(xhr.status == 200) {
            console.log("daumau", response);
            
            $("#dau30").text(response.data.average.dau);
            $("#mau30").text(response.data.average.mau);
        }
    }).fail(function (response) {
        
    }).then(function(response){ 
        
    }).always(function (response) {
        
    }); 
}


webSocket = new WebSocket("<?php echo config('websockets.endpoint')?>?admin=<?php echo config('websockets.admin_token')?>");



webSocket.onopen = function(e) 
{
    // Listen:
    webSocket.onmessage = function(message) {
        
        let obj = JSON.parse(message.data);
        //console.log("received massage:", JSON.parse(message.data));
        
        if(obj.event == "get_online_users") {
            // console.log("get_online_users", obj.data.user_sessions);
            
            $("#wss_now").text(Object.keys(obj.data.user_sessions).length);
        }

    }
    
    getUsersOnline();

    setInterval(getUsersOnline, 5000);
}  

webSocket.onerror  = function(e) 
{
    console.log("err", e);
    $("#wss_now").text("-");
}

function getUsersOnline() {
    
    
    if(webSocket.readyState == 1) {
        webSocket.send(JSON.stringify({
            "event": "get_online_users",
            "app_version": 1
        }));
    } else {
        console.log("ready state != 1");
        $("#wss_now").text("-");
    }
}

</script>
@endsection