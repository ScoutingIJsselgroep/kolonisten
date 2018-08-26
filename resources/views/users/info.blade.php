@extends('layout')

@section('content')
<a id="info" href="/"><i class="fa fa-reply" aria-hidden="true"></i></a>

<div class="content">
<div class="background">
<h1>Speluitleg</h1>
<p>Met je team probeer je zo snel mogelijk 2 duurzame energiebronnen aan te sluiten op je netwerk!</p>
<p>Om een duurzame energiebron aan te sluiken moet je eerst een locatie vinden, en je hebt grondstroffen nodig om te bouwen.<br />
	Wanneer je een locatie hebt gevonden ontvang je twee grondstoffen die bij die locatie horen.</p>
<ol>
	<li>Als je vervolgens met 1 hout een vuur maakt onvang je binnen 20 minuten 3 nieuwe grondstoffen van die locatie.</li>
	<li>Als niemand je voor was kun je op een locatie waar je een vuur hebt een kolencentrale bouwen, hiervoor zijn 4 steenkool, 6 ijzer en 1 aluminium nodig. Binnen een kwartier levert dit je 6 extra van deze grondstof op.</li>
	<li>Je steenkoolcentrale kun je ombouwen tot een gascentrale, dit kost 2 ijzer, 3 aluminium, en 4 gas. Je ontvangt dan 8 extra van de grondstof binnen 10 minuten.</li>
	<li>Als de zaken goed lopen kun je op de plek van de gascentrale een duurzame energiebron bouwen, dit kost 10 ijzer, 8 aluminium en 6 silicium. Je ontvangt dan elke halve minuut nog een extra grondstof.</li>
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
		<th class="costs"><img src="img/wood.png"></th>
		<th class="costs"><img src="img/coal.png"></th>
		<th class="costs"><img src="img/fe.png"></th>
		<th class="costs"><img src="img/al.png"></th>
		<th class="costs"><img src="img/ch4.png"></th>
		<th class="costs"><img src="img/si.png"></th>
		<th class="right">&nbsp;</th>
	</tr>
	
	<tr>
		<td class="progress"><img src="img/p1.png"> Vuur</td>
		<td class="costs">1</td>
		<td class="costs">&nbsp;</td>
		<td class="costs">&nbsp;</td>
		<td class="costs">&nbsp;</td>
		<td class="costs">&nbsp;</td>
		<td class="costs">&nbsp;</td>
		<td class="right">+3 in 20 min</td>
	</tr>
	<tr>
		<td class="progress"><img src="img/p2.png"> Steenkool centrale</td>
		<td class="costs">&nbsp;</td>
		<td class="costs">4</td>
		<td class="costs">6</td>
		<td class="costs">1</td>
		<td class="costs">&nbsp;</td>
		<td class="costs">&nbsp;</td>
		<td class="right">+6 in 15 min</td>
	</tr>
	<tr>
		<td class="progress"><img src="img/p3.png"> Gas centrale</td>
		<td class="costs">&nbsp;</td>
		<td class="costs">&nbsp;</td>
		<td class="costs">2</td>
		<td class="costs">3</td>
		<td class="costs">4</td>
		<td class="costs">&nbsp;</td>
		<td class="right">+8 in 10 min</td>
	</tr>
	<tr>
		<td class="progress"><img src="img/p4.png"> Duurzame bronnen</td>
		<td class="costs">&nbsp;</td>
		<td class="costs">&nbsp;</td>
		<td class="costs">10</td>
		<td class="costs">8</td>
		<td class="costs">&nbsp;</td>
		<td class="costs">6</td>
		<td class="right">+10 in 5 min</td>
	</tr>
</table>

<p>Snap je het nu?</p>

<a href="/">Ja, weer verder spelen!</a>
</div>
</div>
@endsection