@extends('layout')

@section('content')
<a id="info" href="/"><i class="fa fa-reply" aria-hidden="true"></i></a>

<div class="content">
<div class="background">
<h1>Team {{ $user->name }} <img src="img/{{ strtolower($user->name) }}.png"></h1>
@if($winner)
	@if($user->id == $winner->id)
	<p class="floating-img clearfix"><img src="img/trophy.png">Gefeliciteerd! Jullie zijn vandaag de winnaars van het spel, met als eerste twee caf&eacute;s in Gorssel.<br />
		Jullie mogen je "Kolonisten van Gorssel" noemen!</p>
	@else
	<p class="floating-img clearfix"><img src="img/failed.png">Helaas, team {{ $winner->name }} was jullie voor.<br />
		Team {{ $winner->name }} mag zich nu "Kolonist van Gorssel" noemen!</p>
	@endif
@endif

@if($user->countGasenergys())
<p class="floating-img clearfix"><img src="img/medal.png">Ga naar De Hoek om een om te kijken hoe je van je B&amp;B straks een caf&eacute; moet maken.</p>
@endif

@if($user->countCoalenergys())
<p class="floating-img clearfix"><img src="img/gift.png">Ga naar Gusto om een versnapering op te halen ter ere van je eerste huis.</p>
@endif

<p>Onder in beeld zie je steeds je voorraad grondstoffen, daarnaast heeft jullie team al de volgende energiebronnen.</p>
<ul>
	<li>{{ $user->countScans()==1?'1 locatie gevonden':$user->countScans() . ' locaties gevonden' }}</li>
	<li>{{ $user->countFires()==1?'1 vuur ontstoken':$user->countFires() . ' vuren ontstoken' }}</li>
	<li>{{ $user->countCoalenergys()==1?'1 kolencentrale aangesloten':$user->countCoalenergys() . ' kolencentrales aangesloten' }}</li>
	<li>{{ $user->countGasenergys()==1?'1 gascentrale aangesloten':$user->countGasenergys() . ' gascentrales aangesloten' }}</li>
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