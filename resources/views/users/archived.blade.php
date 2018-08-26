@extends('layout')

@section('content')
<a id="info" href="/"><i class="fa fa-reply" aria-hidden="true"></i></a>

<div class="content">
<div class="background">
<h1>Team {{ $user->name }}</h1>
@if($winner)
	@if($user->id == $winner->id)
	<p class="floating-img clearfix"><img src="img/trophy.png">Gefeliciteerd! Jullie zijn vandaag de winnaars van het spel, met als eerste twee duurzame energie bronnen in je netwerk!<br />
		Jullie mogen je "Kolonisten van Gorssel" noemen!</p>
	@else
	<p class="floating-img clearfix"><img src="img/failed.png">Helaas, team {{ $winner->name }} was jullie voor.<br />
		Team {{ $winner->name }} had als eerste twee duurzame bronnen in het netwerk!</p>
	@endif
@endif

@if($user->countGasplants())
<p class="floating-img clearfix"><img src="img/gasplant.png">Nu heb je ook een gascentrale in je netwerk, nog een klein stapje te gaan en dan kun je ook duurzame bronnen aansluiten.</p>
@endif

@if($user->countCoalplants())
<p class="floating-img clearfix"><img src="img/coalplant.png">Een kolencentrale in je netwerk. Leuk, maar nog lang niet duurzaam, het geeft wel weer wat energie om meer grondstoffen te verzamelen.</p>
@endif

@if($user->countFires())
<p class="floating-img clearfix"><img src="img/fire.png">Mooi! Jullie eerste vuur brandt, wat energie om meer grondstoffen te verzamelen.
	Maar het is natuurlijk wel super inefficient en ouderweds, als e genoeg grondstoffen hebt verzameld kun je een kolencentrale bouwen.</p>
@endif

<p>Onder in beeld zie je steeds je voorraad grondstoffen, daarnaast heeft jullie team al de volgende energiebronnen.</p>
<ul>
	<li>{{ $user->countScans()==1?'1 locatie gevonden':$user->countScans() . ' locaties gevonden' }}</li>
	<li>{{ $user->countFires()==1?'1 vuur ontstoken':$user->countFires() . ' vuren ontstoken' }}</li>
	<li>{{ $user->countCoalplants()==1?'1 kolencentrale aangesloten':$user->countCoalplants() . ' kolencentrales aangesloten' }}</li>
	<li>{{ $user->countGasplants()==1?'1 gascentrale aangesloten':$user->countGasplants() . ' gascentrales aangesloten' }}</li>
	<li>{{ $user->countSustainables()==1?'1 duurzame energiebron aangesloten':$user->countSustainables() . ' duurzame energiebronnen aangesloten' }}</li>
</ul>
</div>
</div>
@if($user)
<div class="elements clearfix">
@foreach(\App\Models\Location::getElements() as $element => $name)
<div class="element">
	<img src="img/{{ $element }}.png">
	<span class="amount" id="{{ $element }}">{{ $user->countElement($element) }}</span>
</div>
@endforeach
</div>
@endif
@endsection