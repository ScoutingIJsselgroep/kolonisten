@extends('layout')

@section('content')
<div id="map" class="full"></div>

<svg id="refresh" width="40" height="40" viewbox="0 0 40 40">
	<path id="border" transform="translate(20, 20)"/>
	<path id="loader" transform="translate(20, 20) scale(.80)"/>
</svg>

<a id="info" href="info"><i class="fa fa-question" aria-hidden="true"></i></a>
<a id="teams" href="teams"><i class="fa fa-users" aria-hidden="true"></i></a>

<div class="elements clearfix">
@foreach(\App\Models\Location::getElements() as $element => $name)
<div class="element">
	<img src="img/{{ $element }}.png">
	<span class="amount" id="{{ $element }}">{{ $user->countElement($element) }}</span>
</div>
@endforeach
</div>
@endsection

@section('js')
<script>
var map,
	locations = [],
	stock = {},
	elements = {
		'unknown': 'Onbekend'
	},
	costs = {!! json_encode(\App\Models\UserLocation::$costs) !!};
@foreach(\App\Models\Location::getElements() as $element => $name)
elements.{{ $element }} = '{{ $name }}';
stock.{{ $element }} = {{ $user->countElement($element) }};
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
			if($location->sustainable) {
				$step = 4;
			} else if($location->gasplant) {
				$step = 3;
			} else if($location->coalplant) {
				$step = 2;
			} else if($location->fire) {
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
			
			if(l.step == 4) {
				$.alert({
					theme: 'supervan',
					title: 'Deze locatie is helmaal uitgebouwd',
					content: 'Met deze duurzame energiebron krijg je er binnen 5 minuten 10 ' + elements[l.element] + ' bij.',
					buttons: {
						'Mooi, kom maar op met dat spul': stopAnimations
					}
				});
			} else if(l.step === 3) {
				$.alert({
					theme: 'supervan',
					title: 'Duurzame energiebron plaatsen op deze locatie?',
					content: 'Duurzame energiebron krijg je er binnen 5 minuten 10 ' + elements[l.element] + ' bij.' +
							'<div>Het uitbouwen kost:</div><div class="costs">' + costList('sustainable') + '</div>' +
							(costCheck('sustainable') ? '' : 'Op dit moment heb je niet genoeg grondstoffen. Probeer het later nog eens, of zoek meer locaties om daar grondstroffen te vinden.'),
					buttons: (costCheck('sustainable') ? {
						'Ja, lijkt me super': function(){
							location.href = '/users/build/' + l.id + '/sustainable';
						},
						'Nu nog niet': stopAnimations
					} : {
						'Ah, jammer': stopAnimations
					})
				});
			} else if(l.step === 2) {
				$.alert({
					theme: 'supervan',
					title: 'Gascentrale maken van deze locatie?',
					content: 'Met een gascentrale krijg je er binnen 10 minuten 8 ' + elements[l.element] + ' bij.' +
							'<div>Het verbouwen kost:</div><div class="costs">' + costList('gasplant') + '</div>' +
							(costCheck('gasplant') ? '' : 'Op dit moment heb je niet genoeg grondstoffen. Probeer het later nog eens, of zoek meer locaties om daar grondstroffen te vinden.'),
					buttons: (costCheck('gasplant') ? {
						'Ja, bouw maar uit': function(){
							location.href = '/users/build/' + l.id + '/gasplant';
						},
						'Nu nog niet': stopAnimations
					} : {
						'Ah, jammer': stopAnimations
					})
				});
			} else if(l.step === 1) {
				$.alert({
					theme: 'supervan',
					title: 'Kolencentrale plaatsen op deze locatie?',
					content: 'Met een kolencentrale krijg je er binnen 15 minuten 6 ' + elements[l.element] + ' bij.' +
							'<div>Het bouwen kost:</div><div class="costs">' + costList('coalplant') + '</div>' +
							(costCheck('coalplant') ? '' : 'Op dit moment heb je niet genoeg grondstoffen. Probeer het later nog eens, of zoek meer locaties om daar grondstroffen te vinden.'),
					buttons: (costCheck('coalplant') ? {
						'Ja, hier wil ik een kolencentrale': function(){
							location.href = '/users/build/' + l.id + '/coalplant';
						},
						'Nu nog niet': stopAnimations
					} : {
						'Ah, jammer': stopAnimations
					})
				});
			} else if(l.step === 0) {
				$.alert({
					theme: 'supervan',
					title: 'Vuur maken op deze locatie?',
					content: 'Met een vuur krijg er binnen 20 minuten 3 ' + elements[l.element] + ' bij.' +
							'<div>Je hebt er voor nodig:</div><div class="costs">' + costList('fire') + '</div>' +
							(costCheck('fire') ? '' : 'Op dit moment heb je niet genoeg grondstoffen. Probeer het later nog eens, of zoek meer locaties om daar grondstroffen te vinden.'),
					buttons: (costCheck('fire') ? {
						'Ja, ontsteken maar': function(){
							location.href = '/users/build/' + l.id + '/fire';
						},
						'Nu nog niet': stopAnimations
					} : {
						'Ah, jammer': stopAnimations
					})
				});
			} else {
				$.alert({
					theme: 'supervan',
					title: 'Vind eerst de code op deze locatie',
					content: 'Heb je hem gevonden, en gescand, dan krijg je er meteen 2 grondstoffen bij, en kun je een vuur maken als je genoeg hout hebt.',
					buttons: {
						'Okay, we gaan al zoeken': stopAnimations
					}
				});
			}
        });
	});
	
	var me = false;
	function showPosition(position) {
		me = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
		new google.maps.Marker({
			position: me,
			map: map,
			icon: {
				url: '{{ url('img/me.png') }}',
				size: new google.maps.Size(20, 20),
				origin: new google.maps.Point(0, 0),
				anchor: new google.maps.Point(10, 10)
			}
		});
		var bounds = new google.maps.LatLngBounds();
		bounds.extend(me);
		$.each(locations, function() {
			posistion = new google.maps.LatLng(this.lat, this.lng);
			bounds.extend(posistion);
		});
		map.fitBounds(bounds);
	}
	
	if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
    } else {
        alert('Kan helaas je locatie niet bepalen');
    }
	
	function update() {
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(showPosition);
		}
		$.getJSON(location.href, function(data) {
			stopAnimations();
			if(data.winner) {
				location.href = '/team'
			}
			var bounds = new google.maps.LatLngBounds();
			bounds.extend(new google.maps.LatLng(52.19994815375433, 6.215185941339087));
			if(me) {
				bounds.extend(me);
			}
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
<script async defr src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB1Ow7kTUabBE2su0Cq6TXJuYBLZLp1LTw&callback=initMap"></script>
@endsection
