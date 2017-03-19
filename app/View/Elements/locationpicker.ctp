
<?php
// It might be required to adapt the validation messages.
// $this->validationErrors[$modelName]['location'] = array("Please select a location");

$this->Html->css('/leaflet/leaflet.css', array('inline' => false));
$this->Html->script('/leaflet/leaflet.js', array('inline' => false));
?>

<?php $this->append('script_bottom'); ?>

var inputLon, inputLat, locationMap, pickedLocation, defaultLocation;

jQuery(document).ready(function() {
	
	inputLon = $('#InstitutionLon');
	inputLat = $('#InstitutionLat');
	
	initializeMap();
	
	function initializeMap() {
		
		var label = document.createElement("label");
		label.innerHTML = "Location";
		
		var overlay = document.createElement("div");
		overlay.id = "locationPicker";
		overlay.setAttribute("title", "Click on the map to enable mousewheel zoom (disables on mouseout).");
		var locationMap = document.createElement("div");
		locationMap.id = "locationPickerMap";
		
		
		var wrapper = document.createElement("div");
		wrapper.className = "input required";
		
		var hint = document.createElement("p");
		hint.innerHTML = "Zoom and pan the map until the crosshair is over the right place.<br>Click on the red marker icon to make your selection.";
		
		wrapper.appendChild(hint);
		wrapper.appendChild(label);
		wrapper.appendChild(locationMap);
		
		inputLat.parent().hide();
		inputLon.parent().hide();
		$(wrapper).insertAfter(inputLat.parent());
		
		
		locationMap = L.map('locationPickerMap', {scrollWheelZoom: false});
		locationMap.setView([50.000, 10.189551], 4);
		L.tileLayer('https://api.mapbox.com/styles/v1/hashmich/ciqhed3uq001ae6niop4onov3/tiles/256/{z}/{x}/{y}?access_token=<?php echo Configure::read('App.mapApiKey'); ?>').addTo(locationMap);
		locationMap.on('click', function() {
			locationMap.scrollWheelZoom.enable();
		});
		locationMap.on('mouseout', function() {
			locationMap.scrollWheelZoom.disable();
		});
		$("#locationPickerMap").append(overlay);
		
		pickedLocation = L.featureGroup();
		defaultLocation = L.featureGroup();
		pickedLocation.addTo(locationMap);
		defaultLocation.addTo(locationMap);
		
		var myMenu = L.Control.extend({
		 	options: {position: 'topleft'},
		 	onAdd: function (map) {
		    	this._div = L.DomUtil.create('div', 'punch');
		   		this._div.innerHTML = "<a></a>" ;
		  		L.DomEvent.on(this._div, "click", this._click )
		    	return this._div;          
		  },
		  _click: function(e){
		    	L.DomEvent.stop(e);
		    	getMapSelection(locationMap);
		    	setMarker(locationMap);
		}});
		locationMap.addControl(new myMenu());
		
		setMarker(locationMap);
	}
	
	function getMapSelection(map) {
		var y = $("#locationPicker").height() / 2;
		var x = $("#locationPicker").width() / 2;
		var point = L.point(x, y);	// x, y pixelcoordinates
		var latlon = map.containerPointToLatLng(point);
		
		setLatLon(latlon);
	}
	
	function getLatLon() {
		var latlon = null;
		if(inputLon.val() != "" && inputLat.val() != "") {
			latlon = new L.LatLng(inputLat.val(), inputLon.val());
		}
		return latlon;
	}
	
	function setLatLon(latlon) {
		inputLat.val(latlon.lat);
		inputLon.val(latlon.lng);
	}
	
	function setMarker(map) {
		var latlon = getLatLon();
		if(latlon != null) {
			pickedLocation.clearLayers();
			pickedLocation.addLayer(L.marker(latlon));
			map.panTo(latlon);
			map.setZoom(10);
		}
	}
});
<?php $this->end(); ?>