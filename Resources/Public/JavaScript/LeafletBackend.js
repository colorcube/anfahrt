define(['jquery', 'TYPO3/CMS/Anfahrt/leaflet-core-1.7.0'], function ($) {
    'use strict';

    /*
    this code is a bit messy
    requirejs ist awkward (Javascript anyway)
    we're handling form fields here, geo coding, the map and workaround missing form functionality
     */

    var LeafletBackendAnfahrt = {
        setLatLon: null,
        setLatLonFieldValues: null,
        geoCodeFromInput: null,
        geoCodeFromAddress: null,
        marker: null,
        baseLayer: null,
        map: null
    };


    $(function () {


        /**
         * set a postion for the map marker and the coordinate form fields
         * used when coordinates from geo coding should be set
         * @param position Leaflet position object
         */
        function setLatLon(position) {
            LeafletBackendAnfahrt.marker.setLatLng(position);
            LeafletBackendAnfahrt.map.panTo(position);
            setLatLonFieldValues(position);
        }

        function setLatLonFieldValues(position) {
            if ($latFieldName) setFieldValue($latFieldName, position.lat);
            if ($lonFieldName) setFieldValue($lonFieldName, position.lng);
        }

        function getFieldValue(fieldName) {
            // const $input = $(':input[data-formengine-input-name="' + fieldName + '"]', $formEl);
            const $input = $(':input[name="' + fieldName + '"]');
            if ($input) {
                return $input.val();
            } else {
                console.error('field not found', fieldName);
            }
        }

        function setFieldValue(fieldName, value) {
            const $input = $(':input[data-formengine-input-name="' + fieldName + '"]');
            if ($input) {
                $input.val(value).trigger('change');
            } else {
                console.error('field not found', fieldName);
            }
        }

        function geoCoder(params) {
            var queryString = Object.keys(params).map(key => key + '=' + encodeURIComponent(params[key])).join('&');
            $.getJSON('http://nominatim.openstreetmap.org/search?format=json&limit=1&' + queryString, function (data) {
                if (data[0] && data[0].lat) {
                    setLatLon(L.latLng(data[0].lat, data[0].lon));
                }
            });
        }

        function geoCodeFromInput(input) {
            geoCoder({q: input});
        }

        function geoCodeFromAddress() {
            const addressFields = JSON.parse($element.attr('data-address-fields'));
            const fieldData = [];

            // Properties: address, street, housenumber, zip, city, country

            // TODO if 'address' is set ignore the others and make a 'q' query only

            for (let field in addressFields) {
                let data = getFieldValue(addressFields[field]);
                if (data) {
                    fieldData.push(data);
                }
            }

            /*
            TODO nominatim
             street=<housenumber> <streetname>
             city=<city>
             county=<county>
             state=<state>
             country=<country>
             postalcode=<postalcode>
               (experimentell) Alternatives Format für strukturierte Abfragen. Diese sind schneller und verbrauchen weniger Ressourcen.
               Nicht zusammen mit dem Query-Parameter q= verwenden.
             */

            geoCoder({q: fieldData.join(', ')});
        }

        const $element = $('#t3js-location-map-container');
        const tilesUrl = $element.attr('data-tiles');
        const tilesAttribution = $element.attr('data-attribution');

        const $latFieldName = $element.attr('data-lat-field');
        const $lonFieldName = $element.attr('data-lon-field');

        let latitude = getFieldValue($latFieldName);
        let longitude = getFieldValue($lonFieldName);

        // first might be a flexform tab pane, second the tt_content tab pane
        const $tabMapIsIn = $element.closest('.tab-pane');
        const $tabMapTabIsIn = $tabMapIsIn.parent().closest('.tab-pane');

        function initMap() {
            const map = L.map('t3js-location-map-container', {
                center: [latitude, longitude],
                zoom: 13
            });
            const baseLayer = L.tileLayer(tilesUrl, {
                attribution: tilesAttribution
            }).addTo(map);

            const marker = L.marker([latitude, longitude], {
                draggable: true
            }).addTo(map);

            marker.on('dragend', function (event) {
                const draggedMarker = event.target;
                const position = draggedMarker.getLatLng();
                setLatLonFieldValues(position);
            });
            map.on('click', function (event) {
                marker.setLatLng(event.latlng);
                setLatLonFieldValues(event.latlng);
            });

            LeafletBackendAnfahrt.marker = marker;
            LeafletBackendAnfahrt.baseLayer = baseLayer;
            LeafletBackendAnfahrt.map = map;
        }

        function checkVisibilityAndInitMap() {
            if (LeafletBackendAnfahrt.map) {
                return;
            }
            setTimeout(function () {
                let isActive = true;
                if ($tabMapTabIsIn) {
                    isActive = $tabMapTabIsIn.hasClass('active');
                }
                if (isActive && $tabMapIsIn) {
                    isActive = $tabMapIsIn.hasClass('active');
                }

                if (isActive) {
                    initMap();
                }
            }, 200);
        }

        // The map is not displayed in the right size when the tab (with map container in it) is opened.
        // Leaflet needs to be initialized when the map container becomes visible.
        // If there's a cleaner way, let me know.

        // check if the map is not visible
        let observeNeeded = ($tabMapIsIn && !$tabMapIsIn.hasClass('active')) ? true : false;
        observeNeeded = ($tabMapTabIsIn && !$tabMapTabIsIn.hasClass('active')) ? true : observeNeeded;
        if (observeNeeded) {
            $('.t3js-tabmenu-item').on('click', () => {
                checkVisibilityAndInitMap();
            });
        }


        LeafletBackendAnfahrt.setLatLon = setLatLon;
        LeafletBackendAnfahrt.setLatLonFieldValues = setLatLonFieldValues;
        LeafletBackendAnfahrt.geoCodeFromInput = geoCodeFromInput;
        LeafletBackendAnfahrt.geoCodeFromAddress = geoCodeFromAddress;

        // neded when map is visible on initial form draw
        checkVisibilityAndInitMap();
    });

    // we need to be able to call functions from outside - is there a better way?
    window.LeafletBackendAnfahrt = LeafletBackendAnfahrt;

    return LeafletBackendAnfahrt;
});
