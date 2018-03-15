@extends('layout')

@section('content')
<div class="content">
	<div class="home">
		<h1>Project 2C</h1>

		<p>Welkom bij Project 2C.</p>
		<p>Op 3 maart 2018 zullen we dit spel met de stam van scouting IJsselgroep gaan spelen.</p>
		<p>Bereid je voor:<br />
			<a href="https://play.google.com/store/apps/details?id=com.google.zxing.client.android" target="_blank">Download alvast een QR code scanner</a>.</p>
		<p>Scan de QR code om je groep mee aan te melden, en doe mee!</p>
		<h2>Jasper &amp; Dennis</h2>

		<div class="elements clearfix">
		@foreach(\App\Models\Location::getElements() as $element => $name)
		<div class="element">
			<img src="img/{{ $element }}.png">
		</div>
		@endforeach
		</div>
	</div>
</div>
@endsection
