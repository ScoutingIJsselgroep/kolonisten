@extends('layout')

@section('content')
<a id="info" href="/"><i class="fa fa-reply" aria-hidden="true"></i></a>

<div class="content">
<div class="background">
<h1>Speluitleg</h1>
<p>4 teams proberen Gorssel te koloniseren door het beginnen van 2 cafe&#39;s!</p>
<p>Om een cafe te kunnen beginnen moet je eerst een locatie vinden, en je hebt grondstroffen nodig om te bouwen.<br />
	Wanneer je een locatie hebt gevonden ontvang je twee grondstoffen die bij die locatie horen.</p>
<ol>
	<li>Als je vervolgens voor 1 hout een vlag plaatst onvang je elke 3 minuten een nieuwe grondstof van die locatie.</li>
	<li>Als niemand je voor was kun je op een locatie waar je vlag staat een huis bouwen, hiervoor zijn 8 steen, 6 hout en 2 koper nodig. Elke 2 minuten komt er dan een extra grondstof bij naast die je al voor de vlag kreeg.</li>
	<li>Je huis kun je uitbouwen tot een bed en breakfast, dit kost 6 steen, 4 hout, 3 koper en 3 graan. Je ontvangt dan elke minuut nog een extra grondstof.</li>
	<li>Als de zaken goed lopen kun je van de bed en breakfast een caf&eacute; maken, dit kost 6 steen, 2 hout, 8 koper, 5 graan, 5 gist en 5 hop. Je ontvangt dan elke halve minuut nog een extra grondstof.</li>
</ol>

<p>In het kort</p>
<table>
	<tr>
		<th>&nbsp;</th>
		<th colspan="6">Kosten</th>
		<th class="right"><i class="fa fa-line-chart" aria-hidden="true"></i></th>
	</tr>
	<tr>
		<th>&nbsp;</th>
		<th class="costs"><img src="img/stone.png"></th>
		<th class="costs"><img src="img/wood.png"></th>
		<th class="costs"><img src="img/copper.png"></th>
		<th class="costs"><img src="img/corn.png"></th>
		<th class="costs"><img src="img/yeast.png"></th>
		<th class="costs"><img src="img/hop.png"></th>
		<th class="right">&nbsp;</th>
	</tr>
	
	<tr>
		<td class="progress"><img src="img/p1.png"> Vlag</td>
		<td class="costs">&nbsp;</td>
		<td class="costs">1</td>
		<td class="costs">&nbsp;</td>
		<td class="costs">&nbsp;</td>
		<td class="costs">&nbsp;</td>
		<td class="costs">&nbsp;</td>
		<td class="right">3 min</td>
	</tr>
	<tr>
		<td class="progress"><img src="img/p2.png"> Huis</td>
		<td class="costs">8</td>
		<td class="costs">6</td>
		<td class="costs">2</td>
		<td class="costs">&nbsp;</td>
		<td class="costs">&nbsp;</td>
		<td class="costs">&nbsp;</td>
		<td class="right">2 min</td>
	</tr>
	<tr>
		<td class="progress"><img src="img/p3.png"> B&amp;B</td>
		<td class="costs">6</td>
		<td class="costs">4</td>
		<td class="costs">3</td>
		<td class="costs">3</td>
		<td class="costs">&nbsp;</td>
		<td class="costs">&nbsp;</td>
		<td class="right">1 min</td>
	</tr>
	<tr>
		<td class="progress"><img src="img/p4.png"> Caf&eacute;</td>
		<td class="costs">6</td>
		<td class="costs">2</td>
		<td class="costs">8</td>
		<td class="costs">5</td>
		<td class="costs">5</td>
		<td class="costs">5</td>
		<td class="right">30 sec</td>
	</tr>
</table>

<p>Snap je het nu?</p>

<a href="/">Ja, weer verder spelen!</a>
</div>
</div>
@endsection