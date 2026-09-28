function initMap() {
    var directionsService = new google.maps.DirectionsService;
    var directionsDisplay = new google.maps.DirectionsRenderer;
	
    var mapOptions = {
        zoom: 7,
        center: new google.maps.LatLng(50.8504500, 4.3487800),
        styles: [{
            "featureType": "administrative",
            "elementType": "labels.text.fill",
            "stylers": [{
                "color": "#444444"
            }]
        }, {
            "featureType": "landscape",
            "elementType": "all",
            "stylers": [{
                "color": "#f2f2f2"
            }]
        }, {
            "featureType": "poi",
            "elementType": "all",
            "stylers": [{
                "visibility": "off"
            }]
        }, {
            "featureType": "road",
            "elementType": "all",
            "stylers": [{
                "saturation": -100
            }, {
                "lightness": 45
            }]
        }, {
            "featureType": "road.highway",
            "elementType": "all",
            "stylers": [{
                "visibility": "simplified"
            }]
        }, {
            "featureType": "road.highway",
            "elementType": "geometry.fill",
            "stylers": [{
                "color": "#fbc318"
            }, {
                "visibility": "on"
            }]
        }, {
            "featureType": "road.highway",
            "elementType": "geometry.stroke",
            "stylers": [{
                "saturation": "0"
            }, {
                "visibility": "simplified"
            }, {
                "lightness": "0"
            }]
        }, {
            "featureType": "road.arterial",
            "elementType": "labels.icon",
            "stylers": [{
                "visibility": "off"
            }]
        }, {
            "featureType": "transit",
            "elementType": "all",
            "stylers": [{
                "visibility": "off"
            }]
        }, {
            "featureType": "water",
            "elementType": "all",
            "stylers": [{
                "color": "#e2101c"
            }, {
                "visibility": "on"
            }]
        }, {
            "featureType": "water",
            "elementType": "geometry.fill",
            "stylers": [{
                "saturation": "-100"
            }, {
                "visibility": "on"
            }, {
                "hue": "#ff0000"
            }]
        }]
    };
	
    var mapElement = document.getElementById('map');
    var map = new google.maps.Map(mapElement, mapOptions);

    directionsDisplay.setMap(map);

    calculateAndDisplayRoute(directionsService, directionsDisplay);
}




































		
function calculateAndDisplayRoute(directionsService, directionsDisplay) {
    directionsService.route({
    origin: document.getElementById('mapShow_address_departure').value,
    destination: document.getElementById('mapShow_address_arrival').value,
    travelMode: 'DRIVING'
    }, function(response, status) {
    if (status === 'OK') {
        directionsDisplay.setDirections(response);
    }
    });
}
