@extends('cp.cp_tk')

@section('content_title') Карта городов @endsection

@section('content_breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li class="active">Карта городов:</li>
</ol>
@endsection


@section('content') 
  
  <!-- <?php echo config('services.google_map_token'); ?> -->
  <!-- <?php echo config('services.yandex_map_token'); ?> -->
  

    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script src="https://unpkg.com/@google/markerclustererplus@4.0.1/dist/markerclustererplus.min.js"></script>

<!---- CONTENT: ---->
    <div id="map"></div>
<!---- /CONTENT ---->



@endsection

@section('scripts')
<script>

var oneKey = false;

function redirect(id) {
    if ($(id).length) {
        if(!oneKey) {
            document.location.href = $(id).attr('href');
            oneKey = true;
        }
    }
}

key('right', function(event, handler){
    redirect('#next_page');
});

key('left', function(event, handler){
    redirect('#prev_page');
});

key('n', function(event, handler){
    redirect('#add_new');
});

const locations = [
@forelse($cities as $city)
{ 
    id: {{$city['id']}}, coordinates: {lat: {{$city['latitude']}}, lng: {{$city['longitude']}}},  title: "{{$city['name_ru']}}", has_iata: "{{$city['has_iata']}}", wiki_entity: "{{$city['wiki_entity']}}"
},
@empty
@endforelse
  
];

</script>

    <!-- Async script executes immediately and must be after any DOM elements used in callback. -->
    <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyArjX3tE4D_sX_GJQvwjOZ0CaSZUov7qoU&callback=initMap&libraries=&v=weekly&channel=2"
      async
    ></script>
    
    
    <script>
    
/**
*    Example: https://developers.google.com/maps/documentation/javascript/examples/marker-clustering#maps_marker_clustering-javascript
*/  
   
var prev_infowindow = false; 
   
function initMap() {
    const map = new google.maps.Map(document.getElementById("map"), {
        zoom: 4,
        center: { lat: 55.668393, lng: 37.554656 }, // 55.755833, 37.617222
    });
    
    const labels = "";
    
    const markers = locations.map((location, i) => {
    
        let icon = "/images/location_green.png";
        let labelColor = "#7dd11f";
        
        if(location.has_iata == "1") {
            icon = "/images/location_yellow.png"; 
            labelColor = "#f8c94a";
        } else {
            icon = "/images/location_green.png";
            labelColor = "#7dd11f";
        }
    
          const infowindow = new google.maps.InfoWindow({
            content: location.title + ' <a href="/cp/cities/edit/' + location.id + '" target="_blank">(ред.)</a><br> <a href="https://www.wikidata.org/wiki/' + location.wiki_entity + '" target="_blank">' + location.wiki_entity + '</a>',
            maxWidth: 200,
          });
    
    
        const marker = new google.maps.Marker({ 
            position: location.coordinates,
            title: location.title,
              icon: {
                labelOrigin: new google.maps.Point(16,25), // from top left corner
                url: icon
              },
            
            label: { color: labelColor, /*fontWeight: 'bold',*/ fontSize: '14px', text: location.title }
        }); 
        
        
        
          marker.addListener("click", () => {
              
            if( prev_infowindow ) {
               prev_infowindow.close();
            } 
              
            prev_infowindow = infowindow;
              
            infowindow.open({
              anchor: marker,
              map,
              shouldFocus: false,
            });
          });
    
        return marker;
    });
  
  
  
    // Add a marker clusterer to manage the markers.
    new MarkerClusterer(map, markers, {
    imagePath:
      "https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m",
    });
}



/*
const locations = [
  { lat: -31.56391, lng: 147.154312 },
  { lat: -33.718234, lng: 150.363181 },
  { lat: -33.727111, lng: 150.371124 },
  { lat: -33.848588, lng: 151.209834 },
  { lat: -33.851702, lng: 151.216968 },
  { lat: -34.671264, lng: 150.863657 },
  { lat: -35.304724, lng: 148.662905 },
  { lat: -36.817685, lng: 175.699196 },
  { lat: -36.828611, lng: 175.790222 },
  { lat: -37.75, lng: 145.116667 },
  { lat: -37.759859, lng: 145.128708 },
  { lat: -37.765015, lng: 145.133858 },
  { lat: -37.770104, lng: 145.143299 },
  { lat: -37.7737, lng: 145.145187 },
  { lat: -37.774785, lng: 145.137978 },
  { lat: -37.819616, lng: 144.968119 },
  { lat: -38.330766, lng: 144.695692 },
  { lat: -39.927193, lng: 175.053218 },
  { lat: -41.330162, lng: 174.865694 },
  { lat: -42.734358, lng: 147.439506 },
  { lat: -42.734358, lng: 147.501315 },
  { lat: -42.735258, lng: 147.438 },
  { lat: -43.999792, lng: 170.463352 },
];*/

    </script>
    
    <style>
/* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
#map {
  /*height: 100%;*/
  min-height: 600px;
  max-height: 600px;
}

/* Optional: Makes the sample page fill the window. */
html,
body {
  height: 100%;
  margin: 0;
  padding: 0;
}
    </style>
@endsection
