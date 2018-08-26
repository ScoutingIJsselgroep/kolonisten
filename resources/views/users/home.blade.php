@extends('layout')

@section('content')
<div class="content">
	<div class="home">
		<h1>Liander Aannemerij – Sluiten en uitnutten van contracten</h1>
		
		<p>Op 30 augustus 2018 heeft MT-Aannemerij en Inkoop Aannemerij een gecombineerd uitje, waarin actie, gezelligheid en uitdaging worden gecombineerd.
			We gaan hierbij kennis maken met de wondere wereld van... Scouting.</p>
		<p>Scouting staat voor uitdaging! Scouting biedt leuke en spannende activiteiten waarmee meiden en jongens worden uitgedaagd zich persoonlijk te ontwikkelen.</p>
		<p>Om al vast in de stemming te komen hebben jullie een puzzel gekregen die op twee manieren tot de juiste oplossing 
			(het vinden van de locatie waar de activiteit start) kon leiden.</p>
		
		<h2>Rebus</h2>

		<p>We verzamelen Bij <img src="{{ url('img/janssen.png') }}" alt="Janssen" style="width: 30px;" />&#8209;s&nbsp;&amp;&nbsp;<img src="{{ url('img/janssen.png') }}" alt="Janssen" style="width: 30px;" />&#8209;s,
			maar dan &eacute;&eacute;n verder.<br />
			Tevens kwam later de tip van 'Proef, Geniet &amp; Ontspan'.<br />
			'Bij Jansen & Jansen' is een restaurant aan de Kwekerijweg 4 te Gorssel met het motto 'Proef, Geniet & Ontspan', 
			&eacute;&eacute;n verder is <a href="https://goo.gl/maps/3J2KwtWhPPz" target="_blank">Kwekerijweg 5 (adres van Scouting IJsselgroep)</a>.</p>

		<h2>Kaartpuzzel</h2>

		<p>Door de circkel uit te knippen, hier een kegel van te maken, waarbij de letters op elkaar worden gedraaid en deze te plaatsen op het grondvlak op het bij 
				geleverde kaartje, kon van A via B de juiste locatie op de kaart worden gevonden.<br />
			<img src="{{ url('img/kaart-puzzel.png') }}" alt="Janssen" style="max-width: 100%;" /></p>

		<p>Hier dienen jullie je om 15.00u te melden.</p>
		<p>Op het programma staat een interactief spel, waarbij we in de omgeving grondstoffen verzamelen om bij te dragen aan de energietransitie.
			Het uiteindelijke doel is om zo veel mogelijk duurzame energie op te wekken.</p>
		<p>Het is van belang dat je beschikt over een opgeladen telefoon die is voorzien van een QR code scanner en mobiel internet. 
			Denk tevens aan vrijtijdskleding waarin je lekker kan bewegen.</p>

		<p><a href="https://play.google.com/store/apps/details?id=com.google.zxing.client.android" target="_blank">Download alvast een QR code scanner</a>.</p>
		<h2>Tot dan bij Scouting IJsselgroep in Gorssel</h2>
		
		<p>
			<a href="https://goo.gl/maps/3J2KwtWhPPz" target="_blank">
				Scouting IJsselgroep<br />
				Kwekerijweg 5<br />
				7213 AX Gorssel
			</a>
		</p>
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
