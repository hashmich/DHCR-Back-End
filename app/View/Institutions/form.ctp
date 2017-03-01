<?php
unset($crudFieldlist['Institution.parent_id']);
unset($crudFieldlist['Institution.country_id']);
unset($crudFieldlist['Institution.course_count']);
$crudFieldlist['Institution.can_have_course']['formoptions']['type'] = 'hidden';

if(!empty($title_for_layout)) echo '<h2>'.$title_for_layout.'</h2>';
echo $this->element('layout/actions', array(), array('plugin' => 'Cakeclient'));

echo $this->element('crud/form', array('crudFieldlist' => $crudFieldlist), array('plugin' => 'Cakeclient'));

?>



<?php
$this->Html->css('/leaflet/leaflet.css', array('inline' => false));
$this->Html->script('/leaflet/leaflet.js', array('inline' => false));
?>





<?php $this->append('script_bottom'); ?>

jQuery(document).ready(function() {
	
	var inputLon = $('#InstitutionLon');
	var inputLat = $('#InstitutionLat');
	
	var markup = [];
	
	var locationMap, feature;
	
	initializeMap();
	
	function initializeMap() {
		var wrapper = document.createElement("div");
		wrapper.className = "input required";
		
		var label = document.createElement("label");
		label.appendChild(document.createTextNode("Location"));
		
		var map = document.createElement("div");
		map.id = "locationPicker";
		map.setAttribute("title", "Click on the map to enable mousewheel zoom (disables on mouseout).");
		// classname becomes overwritten by leaflet
		//map.classname = "locationPicker";
		
		wrapper.appendChild(label);
		wrapper.appendChild(map);
		
		inputLat.parent().hide();
		inputLon.parent().hide();
		$(wrapper).insertAfter(inputLat.parent());
		
		
		locationMap = L.map('locationPicker', {scrollWheelZoom: false});
		locationMap.setView([50.000, 10.189551], 4);
		L.tileLayer('https://api.mapbox.com/styles/v1/hashmich/ciqhed3uq001ae6niop4onov3/tiles/256/{z}/{x}/{y}?access_token=<?php echo Configure::read('App.mapApiKey'); ?>').addTo(locationMap);
		locationMap.on('click', function() {
			locationMap.scrollWheelZoom.enable();
		});
		locationMap.on('mouseout', function() {
			locationMap.scrollWheelZoom.disable();
		});
		
		var zoom = $(".leaflet-control-zoom");
		var punch = document.createElement("div");
		punch.className = "punch";
		punch.appendChild(document.createElement("a"));
		$(punch).insertAfter(zoom);
		
		
		if(getLatLon() != null) {
			L.marker(getLatLon()).addTo(locationMap);
			locationMap.panTo(getLatLon());
		}
	}
	
	function getMapSelection() {
		
	}
	
	function getLatLon() {
		var latlon = null;
		if(inputLon.value != "" && inputLat.value != "")
			latlon = L.latLng(inputLat.value, inputLon.value);
		return latlon;
	}
});
<?php $this->end(); ?>