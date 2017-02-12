<?php
/**
 * Copyright 2014 Hendrik Schmeer on behalf of DARIAH-EU, VCC2 and DARIAH-DE,
 * Credits to Erasmus University Rotterdam, University of Cologne, PIREH / University Paris 1
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

$located = array();
// get the markers
if(!empty($courses) AND is_array($courses)) {
	
	foreach($courses as $k => $record) {
		if(empty($record['Course']['lat']) OR empty($record['Course']['lon']))
			continue;
		$title = $record['Course']['name'];
		$content = '<h1>' . $title . '</h1>';
		$content .= '<p>' . $record['Institution']['name'] . ',</p>';
		$content .= '<p>Department: ' . $record['Course']['department'] . '.</p>';
		$content .= '<p>' . $this->Html->link('Details', array(
			'controller' => 'courses',
			'action' => 'view',
			$record['Course']['id']
		)) . '</p>';
		
		$marker = array();
		$marker['id'] = $record['Course']['id'];
		$marker['title'] = str_replace('"', '\\"', str_replace('\\', '\\\\', $title));
		$marker['content'] = str_replace('"', '\\"', str_replace('\\', '\\\\', $content));
		$marker['coordinates'] = array('lat' => $record['Course']['lat'], 'lon' => $record['Course']['lon']);
		$located[$k] = $marker;
	}
}
?>




// This accesses the leaflet map markers
var markers = [];
var mymap, cluster;

function initializeMap() {
	mymap = L.map('coursesMap', {scrollWheelZoom: false});
	mymap.setView([50.000, 10.189551], 4);
	L.tileLayer('https://api.mapbox.com/styles/v1/hashmich/ciqhed3uq001ae6niop4onov3/tiles/256/{z}/{x}/{y}?access_token=<?php echo Configure::read('App.mapApiKey'); ?>').addTo(mymap);
	mymap.on('click', function() {
		mymap.scrollWheelZoom.enable();
	});
	mymap.on('mouseout', function() {
		mymap.scrollWheelZoom.disable();
	});
	
	cluster = new L.MarkerClusterGroup({
		spiderfyOnMaxZoom: true,
		//disableClusteringAtZoom: 14,
		showCoverageOnHover: true,
		zoomToBoundsOnClick: true,
		maxClusterRadius: 30
	});
	var courses = JSON.parse('<?php echo json_encode($located, JSON_HEX_APOS); ?>');
	
	for(var k in courses) {
		var marker = L.marker([courses[k].coordinates.lat, courses[k].coordinates.lon], {title: courses[k].title});
		marker.bindPopup(courses[k].content);
		cluster.addLayer(marker);
		markers[k] = {
			marker: marker,
			id: courses[k].id
		};
	}
	
	mymap.addLayer(cluster);
	mymap.fitBounds(cluster.getBounds());
	mymap.zoomIn();
}

function openMarker(id) {
	for(var k in markers) {
		if(id == markers[k].id) {
			//mymap.options.maxZoom = 14;
			cluster.zoomToShowLayer(markers[k].marker, function() {
				markers[k].marker.openPopup();
			});
			//mymap.options.maxZoom = 18;
			//cluster.getVisibleParent(markers[k].marker).spiderfy();
			break;
		}
	}
}
function closeMarker(id) {
	for(var k in markers) {
		if(id == markers[k].id) {
			markers[k].marker.closePopup();
			mymap.fitBounds(cluster.getBounds());
			mymap.zoomIn();
			break;
		}
	}
}




