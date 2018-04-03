@extends('layout')

@section('content')
<div class="content">
	<div class="home">
		<h1>St. Jorisdag 2018</h1>

		<p>Rond 23 april vieren we bij scouting IJsselgroep <i>Sint Jorisdag</i>.</p>
		<p>Dit keer met dit interactive spel.</p>
		<p>Bereid je voor:<br />
			<a href="https://play.google.com/store/apps/details?id=com.google.zxing.client.android" target="_blank">Download alvast een QR code scanner</a>.</p>
		<p>Scan de QR code om je groep mee aan te melden, en doe mee!</p>
		<h2>Groeten,<br />De leiding van de Ijsselgroep</h2>

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
