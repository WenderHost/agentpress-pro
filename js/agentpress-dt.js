jQuery(document).ready( function ($) {
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
			order: [ [ 2, 'asc'],[ 0, 'asc' ] ],
			lengthMenu: [10,20,50,100],
			processing: true,
			columnDefs: [
				{ name: 'Property', data: 'Property', targets: 0 },
				{ name: 'Square Feet', data: 'Square Feet', type: 'num-fmt', targets: 1 },
				{ name: 'State', data: 'State', targets: 2 }
			]
		});
	};
});