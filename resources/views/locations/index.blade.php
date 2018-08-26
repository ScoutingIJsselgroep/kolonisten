@extends('layout')

@section('content')
<div id="map"></div>
<form id="addForm" action="locations/add" method="post">
	{{ csrf_field() }}
	<div class="clearfix" id="qr"></div>
	<div class="clearfix">
		<label for="lat">Lat</label>
		<input type="text" name="lat" id="lat" readonly>
	</div>
	<div class="clearfix">
		<label for="lng">Lng</label>
		<input type="text" name="lng" id="lng" readonly>
	</div>
	<div class="clearfix">
		<label for="name">Naam</label>
		<input type="text" name="name" id="name">
	</div>
	<div class="clearfix">
		<label for="name">Beschikbaar</label>
		<input type="text" name="available" id="available" pattern="[0-9]{2}:[0-9]{2}:[0-9]{2}">
	</div>
	
	<div class="clearfix">
		<label for="element">Element</label>
		<select id="element" name="element">
			<option disabled selected>Kies een grondstof</option>
			@foreach(\App\Models\Location::getElements() as $element => $name)
				<option value="{{ $element }}">{{ $name }}</option>
			@endforeach
		</select>
	</div>
	<div>
		<button>Opslaan</button>
		<button id="remove">Verwijderen</button>
	</div>
</form>
@endsection

@section('js')
<script>
var map,
	addForm = $('#addForm').hide(),
	removeButton = $('#remove').hide();
function initMap() {
	map = new google.maps.Map(document.getElementById('map'), {
		disableDefaultUI: true,
		draggableCursor: 'default',
		zoom: 15,
		center: {lat: 52.201590457404976, lng: 6.200752258300781},
		styles: styles
	});
	
	var marker = null;
	map.addListener('click', function(e) {
		if(marker) {
			marker.setMap(null);
		}
		marker = new google.maps.Marker({
			position: e.latLng,
			map: map,
			icon: 'img/marker.png'
		});
		addForm.prop('action', 'locations/add');
		addForm.find('#qr').html('');
		addForm.find('#lat').val(e.latLng.lat());
		addForm.find('#lat').val(e.latLng.lat());
		addForm.find('#lng').val(e.latLng.lng());
		addForm.find('#available').val('20:00:00');
		addForm.find('#name').val('').focus();
		addForm.find('#element option').first().prop('selected', true);
		removeButton.hide();
		addForm.show();
		
		marker.setAnimation(google.maps.Animation.BOUNCE);
		
		$.each(locations, function(i,l) {
			if (l.marker.getAnimation() !== null) {
				l.marker.setAnimation(null);
			}
		});
	});
  
	var locations = [];
	var bounds = new google.maps.LatLngBounds();
	@foreach($locations as $location)
		locations.push({
			id: {{ $location->id }},
			lat: {{ $location->lat }},
			lng: {{ $location->lng }},
			name: '{{ $location->name }}',
			element: '{{ $location->element }}',
			available: '{{ $location->available }}',
			marker: new google.maps.Marker({
				position: {lat: {{ $location->lat }}, lng: {{ $location->lng }}},
				map: map,
				icon: '/locations/icon/{{ $location->id }}'
			}),
			qr: {!! json_encode('<a href="l/' . $location->code . '">' . QrCode::size(100)->generate(url('l/' . $location->code)) . '</a>') !!}
		});
		bounds.extend({lat: {{ $location->lat }}, lng: {{ $location->lng }}});
	@endforeach
	
	$.each(locations, function(i,l) {
		l.marker.addListener('click', function() {
			if(marker) {
				marker.setMap(null);
			}
			$.each(locations, function(i,l) {
				if (l.marker.getAnimation() !== null) {
					l.marker.setAnimation(null);
				}
			});
			l.marker.setAnimation(google.maps.Animation.BOUNCE);
        
			addForm.prop('action', 'locations/edit/'+ l.id);
			addForm.find('#qr').html(l.qr + l.id);
			addForm.find('#lat').val(l.lat);
			addForm.find('#lng').val(l.lng);
			addForm.find('#name').val(l.name).focus();
			addForm.find('#available').val(l.available);
			addForm.find('#element option[value=' + l.element + ']').prop('selected', true);
			addForm.show();
			
			removeButton.off();
			removeButton.on('click', function(e) {
				e.preventDefault();
				$.confirm({
					theme: 'supervan',
					title: 'Verwijderen',
					content: l.name + ' defintief verwijderen?',
					buttons: {
						Grapje: function(){
						},
						'Ja, verwijder': function(){
							location.href = 'locations/delete/' + l.id;
						}
					}
				});
			});
			removeButton.show();
        });
	});
	
	map.fitBounds(bounds);
}
</script>
<script async defr src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB1Ow7kTUabBE2su0Cq6TXJuYBLZLp1LTw&callback=initMap"></script>
@endsection
