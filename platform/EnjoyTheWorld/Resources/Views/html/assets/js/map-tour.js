/*
Author       : Dreamguys
Template Name: DreamsEstate - Bootstrap Template
Version      : 1.0
*/

google.maps.visualRefresh = true;
var slider, infowindow = null;
var bounds = new google.maps.LatLngBounds();
var map, current = 0;
var locations =[{
	"id":01,
	"lat":53.470692,
	"lng":-2.220328,
	"grid_name":"Rainbow Mountain Valley",
	"grid_address":"Ciutat Vella, Barcelona",
	"grid_day":"4 Day, 3 Night",
	"grid_rate":"$500",
	"image":'assets/img/tours/tours-07.jpg',
	"grid_star":"5.0"
	}, 
	{
	"id":02,
	"lat":53.469189,
	"lng":-2.199262,
	"grid_name":"Mystic Falls",
	"grid_address":"Princes Street, Edinburgh",
	"grid_day":"3 Day, 2 Night",
	"grid_rate":"$600",
	"image":'assets/img/tours/tours-08.jpg',
	"grid_star":"4.5"
	},
	{
	"id":03,
	"lat":53.468665,
	"lng":-2.189269,
	"grid_name":"Crystal Lake",
	"grid_address":"Deansgate, Manchester",
	"grid_day":"5 Day, 4 Night",
	"grid_rate":"$300",
	"image":'assets/img/tours/tours-09.jpg',
	"grid_star":"4.9"
	},
	];

var icons = {
  
};

function show() {
    infowindow.close();
  if (!map.slide) {
    return;
  }
    var next, marker;
    if (locations.length == 0 ) {
       return
     } else if (locations.length == 1 ) {
       next = 0;
     }
    if (locations.length >1) {
      do {
        next = Math.floor (Math.random()*locations.length);
      } while (next == current)
    }
    current = next;
    marker = locations[next];
    setInfo(marker);
    infowindow.open(map, marker);
}

function initialize() {
    var bounds = new google.maps.LatLngBounds();
    var mapOptions = {
        zoom: 14,
		center: new google.maps.LatLng(53.470692, -2.220328),
        scrollwheel: false,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
		
    };
  
     map = new google.maps.Map(document.getElementById('map'), mapOptions);
    map.slide = true;

    setMarkers(map, locations);
    infowindow = new google.maps.InfoWindow({
        content: "loading..."
    });
    google.maps.event.addListener(infowindow, 'closeclick',function(){
       infowindow.close();
    });
    slider = window.setTimeout(show, 3000);
}

function setInfo(marker) {
  var content = 
	'<div class="card">'+
		'<div class="card-img">'+
			'<a href="javascript:void(0)" class="property-img">'+
				'<img class="img-fluid w-100" alt="img" src="' + marker.image + '">'+
			'</a>'+
		'</div>'+
		'<div class="card-body">'+
			'<h5 class="title mb-2">'+
				'<a href="javascript:void(0)" tabindex="-1">' + marker.grid_name + '</a> '+
			'</h5>'+
			'<p class="mb-3"><i class="isax isax-location"></i> ' + marker.grid_address + '</p>'+
			'<div class="d-flex align-items-center justify-content-between">' +
			'<div class="d-flex align-items-center">'+'<h4 class="text-primary border-end pe-2 me-2">' + marker.grid_rate +'</h4>' +
			'<p>' + marker.grid_day +'</p>'+'</div>'+
			'<span class="badge badge-warning text-dark">' + marker.grid_star +'</span>'
			'</div>'
		'</div>'+
	'</div>';
  infowindow.setContent(content);;
}

function setMarkers(map, markers) {
  for (var i = 0; i < markers.length; i++) {
    var item = markers[i];
    var latlng = new google.maps.LatLng(item.lat, item.lng);
    var marker = new google.maps.Marker({
        position: latlng,
        map: map,
        grid_name: item.grid_name,
        grid_address: item.grid_address,
        grid_day: item.grid_day,
        grid_rate: item.grid_rate,
        grid_star: item.grid_star,
        animation: google.maps.Animation.DROP,
        icon: icons[item.icons],
        image: item.image
        });
        bounds.extend(marker.position);
        markers[i] = marker;
        google.maps.event.addListener(marker, "click", function () {
            setInfo(this);
            infowindow.open(map, this);
            window.clearTimeout(slider);
        });
    }
    map.fitBounds(bounds);
  google.maps.event.addListener(map, 'zoom_changed', function() {
    if (map.zoom > 16) map.slide = false;
  });
}

google.maps.event.addDomListener(window, 'load', initialize);