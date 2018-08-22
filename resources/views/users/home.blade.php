@extends('layout')

@section('content')
<div class="content">
	<div class="home">
		<h1>Liander the game</h1>

		<p>30 Augustus komt Liander Aannemerij op bezoek in Gorssel.</p>
		<p>Wat onder andere op het programma staat is dit interactive spel.</p>
		<p>Bereid je voor:<br />
			<a href="https://play.google.com/store/apps/details?id=com.google.zxing.client.android" target="_blank">Download alvast een QR code scanner</a>.</p>
		<p>Scan ter plaatse de QR code om je groep mee aan te melden, en doe mee!</p>
		<h2>Tot dan bij,<br />Scouting IJsselgroep</h2>
		
		<img src="{{ url('img/cone.png') }}" alt="">

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
