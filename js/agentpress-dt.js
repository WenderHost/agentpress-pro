(function($){
	var defaultMapCenter = {lat: 35.924209, lng: -84.090641 };
	var developmentsMap = $('#past-developments-map');

	// Setup loading overlay
	var loadingParent = $('.map-container .loading').parent();
	$('.map-container .loading').width( loadingParent.width() ).css( 'z-index', 1 );

	$(document).ready( function ($) {
		/*
		function initTableTop() {
			Tabletop.init( {
				key: wpvars.key,
				callback: function(data,tabletop){
					if( true == wpvars.render_table ){
						renderTable(data);
					} else {
						data.forEach(function(element, index){
							console.log( element );
							$('#past-developments-map').append('<div class="marker" data-city="' + element.City + '" data-state="' + element.State + '" data-lat="' + element.Latitude + '" data-lng="' + element.Longitude + '">' + element.Property + ', ' + element['Square Feet'] + 'ft<sup>2</sup><br>' + element.City + ', ' + element.State + '</div>');
						});
						render_map( developmentsMap );
					}
				},
				simpleSheet: true,
				debug: false
			} )
		}
		initTableTop();
		*/
		console.log('👋 JSON URL: ', wpvars.json_url);
		$.get( wpvars.json_url, function( data ){
			console.log(data);
			if( true == wpvars.render_table ){
				renderTable(data);
			} else {
				data.forEach(function(element, index){
					console.log( element );
					$('#past-developments-map').append('<div class="marker" data-city="' + element.City + '" data-state="' + element.State + '" data-lat="' + element.Latitude + '" data-lng="' + element.Longitude + '">' + element.Property + ', ' + element['Square Feet'] + 'ft<sup>2</sup><br>' + element.City + ', ' + element.State + '</div>');
				});
				render_map( developmentsMap );
			}
		});

		var columns = [
			{'data': 'city', 'title': 'Property' },
			{'data': 'square-feet', 'title': 'Square Feet' },
			{'data': 'state', 'title': 'State' }
		];

		function renderTable(data){
			$('#past-developments').DataTable({
				data: data,
				order: [ [ 3, 'asc'], [ 2, 'asc'], [ 0, 'asc' ] ],
				lengthMenu: [10,20,50,100],
				paging: false,
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
					$('#past-developments-map').append('<div class="marker" data-city="' + data.City + '" data-state="' + data.State + '" data-lat="' + data.Latitude + '" data-lng="' + data.Longitude + '">' + data.Property + ', ' + data['Square Feet'] + 'ft<sup>2</sup><br>' + data.City + ', ' + data.State + '</div>');
				},
				drawCallback: function(settings){
					// Build Google Map
					render_map( developmentsMap );
				}
			});
		};
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
			center		: defaultMapCenter,
			mapTypeId	: google.maps.MapTypeId.ROADMAP,
			scrollwheel	: false
		};

		// create map
		var map = new google.maps.Map( $el[0], args);

		// add a markers reference
		map.markers = [];

		// add markers
		$markers.each(function(i){
	    	add_marker( $(this), map );
		});

		center_map(map);
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

		console.log("\n" + 'Centering map for ' + map.markers.length + ' markers...');

		// vars
		var bounds = new google.maps.LatLngBounds();

		// loop through all markers and create bounds
		$.each( map.markers, function( i, marker ){

			var latlng = new google.maps.LatLng( marker.position.lat(), marker.position.lng() );
			bounds.extend( latlng );

		});

		if( map.markers.length == 1 ){
			// set center of map
		    map.setCenter( bounds.getCenter() );
		    map.setZoom( 12 );
		} else if( map.markers.length == 0){
			map.setCenter( defaultMapCenter );
		} else {
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

		var markerLatLng = {lat: parseFloat( $marker.attr( 'data-lat' ) ), lng: parseFloat( $marker.attr( 'data-lng' ) ) };
		console.log(markerLatLng);
		var marker = new google.maps.Marker({
			position: markerLatLng,
			map: map
		});
		map.markers.push( marker );

		// if marker contains HTML, add it to an infoWindow
		if( $marker.html() ){
			// create info window
			var infowindow = new google.maps.InfoWindow({
				content		: $marker.html()
			});

			// show info window when marker is clicked
			google.maps.event.addListener(marker, 'click', function() {

				infowindow.open( map, marker );

			});
		}
	}

})(jQuery);