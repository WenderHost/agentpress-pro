(function($){
	var geofinish = 0;

	// Setup loading overlay
	var loadingParent = $('.map-container .loading').parent();
	$('.map-container .loading').width( loadingParent.width() ).css( 'z-index', 1 );

	$(document).ready( function ($) {
		function initTableTop() {
			Tabletop.init( {
				key: wpvars.key,
				callback: function(data,tabletop){
					writeTable(data);
				},
				simpleSheet: true,
				debug: false
			} )
		}
		initTableTop();

		var columns = [
			{'data': 'city', 'title': 'Property' },
			{'data': 'square-feet', 'title': 'Square Feet' },
			{'data': 'state', 'title': 'State' }
		];

		function writeTable(data){
			$('#past-developments').DataTable({
				data: data,
				order: [ [ 3, 'asc'], [ 2, 'asc'] ],
				lengthMenu: [10,20,50,100],
				dom: 'pfrti',
				processing: true,
				columnDefs: [
					{ name: 'Property', data: 'Property', targets: 0 },
					{ name: 'Square Feet', data: 'Square Feet', type: 'num-fmt', targets: 1 },
					{ name: 'City', data: 'City', targets: 2 },
					{ name: 'State', data: 'State', targets: 3 }
				],
				rowCallback: function(row,data,dataIndex){
					//console.log(data);
					$('#past-developments-map').append('<div class="marker" data-city="' + data.City + '" data-state="' + data.State + '">' + data.Property + ', ' + data['Square Feet'] + 'ft<sup>2</sup><br>' + data.City + ', ' + data.State + '</div>');
					//console.log( data.Property + ' - ' + data.City + ', ' + data.State );
				},
				drawCallback: function(settings){
					// Build Google Map
					var developmentsMap = $('#past-developments-map');
					render_map( developmentsMap );
				}
			});
		};

		// Build Google Map
		//var developmentsMap = $('#past-developments-map');
		//render_map( developmentsMap );
	});

	/*
	*  render_map
	*
	*  This function will render a Google Map onto the selected jQuery element
	*
	*  @type	function
	*  @date	8/11/2013
	*  @since	4.3.0
	*
	*  @param	$el (jQuery element)
	*  @return	n/a
	*/

	function render_map( $el ) {

		// Show loading overlay
		$('.map-container .loading').css( 'z-index', 1 );

		// var
		var $markers = $el.find('.marker');

		// vars
		var args = {
			zoom		: 10,
			center		: new google.maps.LatLng(35.924209,-84.090641),
			mapTypeId	: google.maps.MapTypeId.ROADMAP,
			scrollwheel	: false
		};

		// create map
		var map = new google.maps.Map( $el[0], args);

		// add a markers reference
		map.markers = [];
		map.markers_count = $markers.length;

		// add markers
		//*
		$markers.each(function(i){
	    	if( 10 <= geofinish ){
	    		center_map(map);
	    		return false;
	    	}
	    	add_marker( $(this), map );
		});
		/**/
	}

	/*
	*  center_map
	*
	*  This function will center the map, showing all markers attached to this map
	*
	*  @type	function
	*  @date	8/11/2013
	*  @since	4.3.0
	*
	*  @param	map (Google Map object)
	*  @return	n/a
	*/

	function center_map( map ) {
		geofinish = 0; // reset counter for finished geocoding cycles
		console.log("\n" + 'Centering map...');

		// vars
		var bounds = new google.maps.LatLngBounds();

		// loop through all markers and create bounds
		$.each( map.markers, function( i, marker ){

			var latlng = new google.maps.LatLng( marker.position.lat(), marker.position.lng() );
			bounds.extend( latlng );

		});

		// only 1 marker?
		if( map.markers.length == 1 )
		{
			// set center of map
		    map.setCenter( bounds.getCenter() );
		    map.setZoom( 12 );
		}
		else
		{
			// fit to bounds
			map.fitBounds( bounds );
		}
		$('.map-container .loading').css( 'z-index', -1 );
	}

	/*
	*  add_marker
	*
	*  This function will add a marker to the selected Google Map
	*
	*  @type	function
	*  @date	8/11/2013
	*  @since	4.3.0
	*
	*  @param	$marker (jQuery element)
	*  @param	map (Google Map object)
	*  @return	n/a
	*/

	function add_marker( $marker, map ) {

		var geocoder = new google.maps.Geocoder();

		if( typeof $marker.address !== 'undefined' ){
			var address = $marker.address;
		} else {
			var address = $marker.attr('data-city') + ', ' + $marker.attr( 'data-state' );
		}

		geocoder.geocode({'address': address}, function(results,status){

			if( status == google.maps.GeocoderStatus.OK ){

				var marker = new google.maps.Marker({
					position: 	results[0].geometry.location,
					map: 		map
				});

				// add to array
				var addingMarker = map.markers.push( marker );
				$.when(addingMarker).done(function(){
					geofinish++;
					if( geofinish == map.markers_count ){
						center_map(map);
					}
				});

				// if marker contains HTML, add it to an infoWindow
				if( $marker.html() )
				{
					// create info window
					var infowindow = new google.maps.InfoWindow({
						content		: $marker.html()
					});

					// show info window when marker is clicked
					google.maps.event.addListener(marker, 'click', function() {
						infowindow.open( map, marker );
					});
				}

			} else {
				max = 2500;
				min = 1500;
				retry = Math.floor( Math.random() * (max - min) ) + min;
				console.log('Geocode for `' + address + '` was not successful. Reason: ' + status + "\nRetrying in " + retry + "ms..." );
				$marker.address = address;
				if( 'OVER_QUERY_LIMIT' == status ){
					window.setTimeout(function(){
						add_marker( $marker, map );
					},retry);
				}
			}
		});
	}

})(jQuery);