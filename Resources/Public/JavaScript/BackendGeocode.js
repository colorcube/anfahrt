/**
 * This file is part of the "anfahrt" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */


/**
 * Backend forms map display and geo coding function
 *
 * @author René Fritz <r.fritz@colorcube.de>
 */



var map;
var marker;
var geocoder;

function anfahrt_map(latitude, longitude, address) {

    var myLatlng = new google.maps.LatLng(latitude, longitude);
    var mapOptions = {
        zoom: 2,
        center: myLatlng
    };

    map = new google.maps.Map(document.getElementById("map"), mapOptions);
    geocoder = new google.maps.Geocoder;

    setMarker(myLatlng, address);
}

function setMarker(myLatlng, title) {
    marker = new google.maps.Marker({
        position: myLatlng,
        map: map,
        draggable: true,
        title: title
    });

    updateCoordinates(marker.getPosition());

    google.maps.event.addListener(marker, "dragend", function () {
        updateCoordinates(marker.getPosition());
    });
}

function updateCoordinates(myLatlng) {
    document.getElementById("hidden_latitude").value = myLatlng.lat();
    document.getElementById("hidden_longitude").value = myLatlng.lng();

    map.setCenter(myLatlng);

    if (myLatlng.lat() || myLatlng.lng()) {
        if (map.getZoom() < 9) {
            map.setZoom(9);
        }
    } else {
        map.setZoom(2);
    }
}

function codeAddress(address) {
    geocoder.geocode({'address': address}, function (results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            map.setCenter(results[0].geometry.location);
            if (marker)
                marker.setMap(null);
            setMarker(results[0].geometry.location, address);

        } else {
            alert('Geocode was not successful for the following reason: ' + status);
        }
    });
}