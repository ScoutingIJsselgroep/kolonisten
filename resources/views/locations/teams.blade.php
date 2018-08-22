@extends('layout')

@section('content')
<div id="map" class="full"></div>

<a id="info" href="/"><i class="fa fa-reply" aria-hidden="true"></i></a>

@if($user)
<div class="elements clearfix">
@foreach(\App\Models\Location::getElements() as $element => $name)
<div class="element">
	<img src="img/{{ $element }}.png">
	<span class="amount" id="{{ $element }}">{{ $user->countElement($element) }}</span>
</div>
@endforeach
</div>
@endif
@endsection

@section('js')
<script>
var map,
	locations = [];

function initMap() {
	map = new google.maps.Map(document.getElementById('map'), {
		disableDefaultUI: true,
		draggableCursor: 'default',
		zoom: 15,
		center: {lat: 52.201590457404976, lng: 6.200752258300781},
		styles: styles
	});
	var bounds = new google.maps.LatLngBounds();
	@foreach($locations as $location)
		@php
			if($location->sustainable) {
				$step = 4;
			} else if($location->gasenergy) {
				$step = 3;
			} else if($location->coalenergy) {
				$step = 2;
			} else if($location->fire) {
				$step = 1;
			} else {
				$step = 0;
			}
		@endphp
		var posistion = {lat: {{ $location->lat }}, lng: {{ $location->lng }}};
		bounds.extend(posistion);
		locations.push({
			id: {{ $location->id }},
			lat: {{ $location->lat }},
			lng: {{ $location->lng }},
			name: '{{ $location->name }}',
			marker: new google.maps.Marker({
				position: posistion,
				map: map,
				icon: '/users/icon/{{ $location->id . '/' . $step }}/1'
			})
		});
		map.fitBounds(bounds);
	@endforeach
}
</script>
<script async defr src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB-N0QabvmkBev7w-YovJw2-C96NsNh5VQ&callback=initMap"></script>
@endsection
