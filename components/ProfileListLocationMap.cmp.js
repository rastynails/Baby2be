var GoogleMap = function( elementId )
{
    var self = this;

    var geocoder;
    var map;
    var marker = [];
    var infowindow = [];
    var infowindowState = [];
    var closeImageUrl;

    var mapElementId = elementId;
    
    this.initialize = function(options, url)
    {
        var params = options;
        closeImageUrl = url;
        if( !params )
        {
            var latlng = new google.maps.LatLng(0, 0);

            params = {
                zoom: 9,
                center: latlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                disableDefaultUI: false,
                draggable: false,
                mapTypeControl: false,
                overviewMapControl: false,
                panControl: false,
                rotateControl: false,
                scaleControl: false,
                scrollwheel: false,
                streetViewControl: false,
                zoomControl:false
            };
        }

        map = new google.maps.Map(document.getElementById(mapElementId), params);
        
        //geocoder = new google.maps.Geocoder();
    }

    this.setCenter = function(lat, lon)
    {
        var latlng = new google.maps.LatLng(lat, lon);
        map.setCenter(latlng);
    }

    this.setZoom = function(zoom)
    {
        map.setZoom(zoom);
    }

    this.fitBounds = function(bounds)
    {
        map.fitBounds(bounds);
    }

    this.getBounds = function()
    {
        map.getBounds();
    }

    this.addPoint = function(lat, lon, title, windowContent, isOpen)
    {
        marker[lat + ' ' + lon] = new google.maps.Marker({
            map: map
            //draggable: false
            //optimized: true
        });

        var latlng = new google.maps.LatLng(lat, lon);
        marker[lat + ' ' + lon].setPosition(latlng);

        if ( title )
        {
            marker[lat + ' ' + lon].setTitle(title);
        }

        if ( windowContent )
        {
           infowindow[lat + ' ' + lon] = new InfoBubble({
                content: windowContent,
                shadowStyle: 1,
                padding: 5,
                backgroundColor: '#fff',
                borderRadius: 5,
                arrowSize: 10,
                borderWidth: 0,
                borderColor: '#fff',
                disableAutoPan: false,
                disableAnimation:true,
                hideCloseButton: false,
                arrowPosition: 50,
                arrowStyle: 0,
				minWidth: 170,
                minHeight: 60,
                closeButtonImageUrl: closeImageUrl
            });

            //infowindow[lat + ' ' + lon].setContent(windowContent);

            infowindowState[lat + ' ' + lon] = false;
            if ( isOpen )
            {
                infowindow[lat + ' ' + lon].open(map, marker[lat + ' ' + lon]);
                infowindowState[lat + ' ' + lon] = true;
            }

            google.maps.event.addListener(marker[lat + ' ' + lon], 'click', function() {
                if( infowindowState[lat + ' ' + lon] )
                {
                    infowindow[lat + ' ' + lon].close();
                    infowindowState[lat + ' ' + lon] = false;
                }
                else
                {
                    infowindow[lat + ' ' + lon].open(map, marker[lat + ' ' + lon]);
                    infowindowState[lat + ' ' + lon] = true;
                }
            });
            
            google.maps.event.addListener(infowindow[lat + ' ' + lon], 'closeclick', function() {
                if( infowindowState[lat + ' ' + lon] )
                {
                    infowindow[lat + ' ' + lon].close();
                    infowindowState[lat + ' ' + lon] = false;
                }
            });
        }
    }

    this.resize = function()
    {
        var bounds = map.getBounds();
        google.maps.event.trigger(map, 'resize');
        map.fitBounds(bounds);
    }
}