@extends('layout')

@section('content')
<a id="info" href="/"><i class="fa fa-reply" aria-hidden="true"></i></a>

<div class="content">
<div class="background">
<h1>Speluitleg</h1>
<p>Probeer met je team als eerste de benodigde spullen te bemachtigen en de draak te vinden!</p>
<p>Om veilig op weg te kunnen naar de draak moet je eers een wortel (voor het paard), 
	een helm, een schild, een zwaard, een harnas en tot slot een paard kopen.</p>
<p>In de buurt van waar de markers op de kaart staan vind je een QR code.
	Heb je de gevonden, scan je hem, en je verdiend daarmee Henx, ook kom je er achter wat je op die locatie kan kopen. 
	Heb je een locatie gescand, dan komen de twee dichtstbijzijnde locaties bij op de kaart (tot je alle locaties ontgrendeld hebt), en kun je die ook gaan zoeken.</p>

<table>
	<tr>
		<th colspan="2">Benodigd</th>
		<th>Kosten</th>
	</tr>
	<tr>
		<th class="costs"><img src="img/wortel.png"></th>
		<td>Wortel voor het paard</td>
		<th class="right">1</th>
	</tr>
	<tr>
		<th class="costs"><img src="img/helm.png"></th>
		<td>Helm voor als je van het paard valt</td>
		<th class="right">2</th>
	</tr>
	<tr>
		<th class="costs"><img src="img/schild.png"></th>
		<td>Schild tegen vuurspugen van de draak</td>
		<th class="right">3</th>
	</tr>
	<tr>
		<th class="costs"><img src="img/zwaard.png"></th>
		<td>Zwaard om de draak mee te lijf te gaan</td>
		<th class="right">4</th>
	</tr>
	<tr>
		<th class="costs"><img src="img/harnas.png"></th>
		<td>Harnas tegen opspattende schubben van de draak</td>
		<th class="right">5</th>
	</tr>
	<tr>
		<th class="costs"><img src="img/paard.png"></th>
		<td>Paard omdat er geen openbaar vervoer is naar de draak</td>
		<th class="right">6</th>
	</tr>
</table>

<p>Snap je het nu?</p>

<a href="/">Ja, weer verder spelen!</a>
</div>
</div>
@endsection