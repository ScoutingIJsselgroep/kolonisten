@extends('layout')

@section('content')
<div class="content">
	<div class="home">
		<h1>Project 2C</h1>

		<p>Welkom bij Project 2C.</p>
		<p>Op 3 maart 2018 zullen we dit spel met de stam van scouting IJsselgroep gaan spelen.</p>
		<p>Bereid je voor:<br />
			<a href="https://play.google.com/store/apps/details?id=com.google.zxing.client.android" target="_blank">Download alvast een QR code scanner</a>.</p>
		<p>Tot over <span id="countdown">{{ \Carbon\Carbon::now()->diffForHumans(\Carbon\Carbon::create(2018, 3, 3, 19, 30, 0), true) }}</span>!</p>
		<h2>Jasper &amp; Dennis</h2>
		
		<div class="teams clearfix">
			<div class="team klok"><img src="img/klok.png"></div>
			<div class="team kroon"><img src="img/kroon.png"></div>
			<div class="team ster"><img src="img/ster.png"></div>
			<div class="team kompas"><img src="img/kompas.png"></div>
		</div>
	</div>
</div>
@endsection

@section('js')
<script>
var targetDate = new Date(2018, 2, 3, 19, 30, 0, 0).getTime(),
	countdown = function() {
		var now = new Date().getTime(),
			diff = targetDate - now,
			days = Math.floor(diff / (1000 * 60 * 60 * 24)),
			hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)),
			minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60)),
			seconds = Math.floor((diff % (1000 * 60)) / 1000),
			string = '';

		string += days == 1 ? '1 dag ' : (days + ' dagen ');
		string += hours + ' uur ';
		string += minutes == 1 ? '1 minuut ' : (minutes + ' minuten ');
		string += seconds == 1 ? '1 seconde' : (seconds + ' seconden');

		$('#countdown').html(string);

		if (diff == 0) {
			clearInterval(x);
			location.href = location.href;
		}
	},
	x = setInterval(countdown, 1000);
countdown();

$('.teams .klok').click(function() {
	$.alert({
		theme: 'supervan',
		title: 'Team klok',
		content: 'Ben jij het team dat als eerste het doel behaald?',
		buttons: {
			'Goed verhaal': function(){
			}
		}
	});
});
$('.teams .kroon').click(function() {
	$.alert({
		theme: 'supervan',
		title: 'Team kroon',
		content: 'Er is natuurlijk altijd een team waar de rest een beetje respect voor moet hebben!',
		buttons: {
			Zeker: function(){
			}
		}
	});
});
$('.teams .ster').click(function() {
	$.alert({
		theme: 'supervan',
		title: 'Team ster',
		content: 'Dit team is bij voorbaat al een ster in dit spel!',
		buttons: {
			Mooi: function(){
			}
		}
	});
});
$('.teams .kompas').click(function() {
	$.alert({
		theme: 'supervan',
		title: 'Team kompas',
		content: 'Weet jou team de kortste weg naar de finish?',
		buttons: {
			'Misschien': function(){
			}
		}
	});
});

</script>
@endsection
