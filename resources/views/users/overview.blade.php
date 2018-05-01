@extends('layout')

@section('content')
<div id="map" class="full"></div>

<svg id="refresh" width="40" height="40" viewbox="0 0 40 40">
	<path id="border" transform="translate(20, 20)"/>
	<path id="loader" transform="translate(20, 20) scale(.80)"/>
</svg>
<span id="henx">
	<img src="img/henx.png">
	<span class="amount">{{ $user->countHenx() }}</span>
</span>

<a id="info" href="info"><i class="fa fa-question" aria-hidden="true"></i></a>
<a id="teams" href="teams"><i class="fa fa-users" aria-hidden="true"></i></a>

<div class="elements clearfix">
@foreach(\App\Models\Location::getElements() as $element => $name)
<div class="element">
	<img src="img/{{ $element }}.png">
	<span class="amount{{ $user->hasElement($element) ? ' buyed' : '' }}" id="{{ $element }}"></span>
</div>
@endforeach
</div>
@endsection

@section('js')
<script>
var map,
	locations = [],
	henx = {{ $user->countHenx() }},
	stock = {},
	elements = {
		'unknown': 'Onbekend'
	},
	costs = {!! json_encode(\App\Models\UserLocation::$costs) !!};
@foreach(\App\Models\Location::getElements() as $element => $name)
elements.{{ $element }} = '{{ $name }}';
stock.{{ $element }} = {{ $user->hasElement($element) ? 'true' : 'false' }};
@endforeach

function initMap() {
	map = new google.maps.Map(document.getElementById('map'), {
		disableDefaultUI: true,
		draggableCursor: 'default',
		zoom: 15,
		center: {lat: 52.201590457404976, lng: 6.200752258300781},
		styles: styles
	});
	
	@foreach($locations as $location)
		@php
			if($location->cafe) {
				$step = 4;
			} else if($location->bb) {
				$step = 3;
			} else if($location->house) {
				$step = 2;
			} else if($location->flag) {
				$step = 1;
			} else if($location->scan) {
				$step = 0;
			} else {
				$step = 'null';
			}
		@endphp
		locations.push({
			id: {{ $location->id }},
			lat: {{ $location->lat }},
			lng: {{ $location->lng }},
			name: '{{ $location->name }}',
			element: '{{ $location->user_id ? $location->element : 'unknown' }}',
			step: {{ $step }},
			marker: new google.maps.Marker({
				position: {lat: {{ $location->lat }}, lng: {{ $location->lng }}},
				map: map,
				icon: '/users/icon/{{ $location->id . ($step !== null ? '/' . $step : '') }}'
			})
		});
	@endforeach
	
	function costList(make) {
		var list = []
		$.each(costs[make], function(element, amount) {
			if(amount) {
				list.push(amount + ' <img src="img/' + element + '.png">');
			}
		});
		return list.join(', ');
	}
	function costCheck(make) {
		var available = true;
		$.each(costs[make], function(element, amount) {
			if(amount && stock[element] < amount) {
				available = false;
			}
		});
		return available;
	}
	function stopAnimations() {
		$.each(locations, function(i,l) {
			if (l.marker.getAnimation() !== null) {
				l.marker.setAnimation(null);
			}
		});
	}
	
	$.each(locations, function(i,l) {
		l.marker.addListener('click', function() {
			stopAnimations();
			l.marker.setAnimation(google.maps.Animation.BOUNCE);
			
			if(l.step == 1 || (l.element != 'unknown' && stock[l.element])) {
				$.alert({
					theme: 'supervan',
					title: 'Je hebt al een ' + elements[l.element] + ' in je bezit',
					content: 'Beter geef je je Henx aan wat nuttigs uit.',
					buttons: {
						'Mooi, ik zat even niet op te letten': stopAnimations
					}
				});
			} else if(l.step === 0) {
				$.alert({
					theme: 'supervan',
					title: elements[l.element] + ' kopen?',
					content: 'Het kost: ' + costs[l.element] + ' Henx' +
							(costs[l.element] <= henx ? '' : '<div>Op dit moment heb je niet genoeg Henx, Je hebt er nu ' + henx + ' in de spaarpot. Vind meer locaties, en verdien zo extra Henx!</div>'),
					buttons: (costs[l.element] <= henx ? {
						'Ja, kopen dat ding': function(){
							location.href = '/users/buy/' + l.id;
						},
						'Nu nog niet': stopAnimations
					} : {
						'Ah, jammer': stopAnimations
					})
				});
			} else {
				$.alert({
					theme: 'supervan',
					title: 'Vind eerst de code van deze locatie',
					content: 'Heb je hem gevonden, en gescand, dan krijg je er meteen 2 Henx bij, en je kunt zien wat je hier kan kopen.',
					buttons: {
						'Okay, we gaan al zoeken': stopAnimations
					}
				});
			}
        });
	});
	
	function update() {
		$.getJSON(location.href, function(data) {
			stopAnimations();
			if(data.winner) {
				location.href = '/team'
			}
			var bounds = new google.maps.LatLngBounds();
			$.each(data.locations, function() {
				var newLocationData = this,
					found = false,
					posistion = new google.maps.LatLng(newLocationData.lat, newLocationData.lng);
				bounds.extend(posistion);
				$.each(locations, function(i,l) {
					if(newLocationData.id == l.id) {
						found = true;
						if(l.element != newLocationData.element) {
							l.element = newLocationData.element;
						}
						if(l.step != newLocationData.step) {
							l.step = newLocationData.step;
							l.marker.setIcon('/users/icon/' + l.id + (l.step !== null ? '/' + l.step : ''));
						}
					}
				});
				if(!found) {
					var marker = new google.maps.Marker({
						position: posistion,
						map: map,
						icon: '/users/icon/' + newLocationData.id
					});
					marker.setAnimation(google.maps.Animation.BOUNCE);
					locations.push({
						id: newLocationData.id,
						lat: newLocationData.lat,
						lng: newLocationData.lng,
						name: newLocationData.name,
						element: newLocationData.element,
						step: null,
						marker: marker
					});
				}
			});
			
			$.each(data.elements, function(element, newStock) {
				if(stock[element] != newStock) {
					stock[element] = newStock;
					$('#' + element + '.amount').html(newStock);
				}
			});
			
			map.fitBounds(bounds);
		});
	}

	var loader = document.getElementById('loader')
	  , border = document.getElementById('border')
	  , α = 360
	  , π = Math.PI
	  , t = 60/360*1000;

	(function draw() {
		α--;
		if(α == 0) {
			α = 360;
			// todo, check andere gebruikers en voorraad
			update();
		}

		var r = ( α * π / 180 )
		  , x = Math.sin( r ) * 20
		  , y = Math.cos( r ) * - 20
		  , mid = ( α > 180 ) ? 1 : 0
		  , anim = 'M 0 0 v -20 A 20 20 1 ' 
				 + mid + ' 1 ' 
				 +  x  + ' ' 
				 +  y  + ' z';

		loader.setAttribute( 'd', anim );
		border.setAttribute( 'd', anim );

		setTimeout(draw, t); // Redraw
	})();

	update();
	
	$('#refresh').click(function() {
		α = 360;
		// todo, check andere gebruikers en voorraad
		update();
	});
}
</script>
<script async defr src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAzr4rodwk_VNux23rZeTtjhnu4RBwlYtM&callback=initMap"></script>
@endsection
