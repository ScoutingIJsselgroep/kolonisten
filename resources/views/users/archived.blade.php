@extends('layout')

@section('content')
<a id="info" href="/"><i class="fa fa-reply" aria-hidden="true"></i></a>

<div class="content">
<div class="background">
<h1>Team {{ $user->name }}</h1>
@if($winner)
	@if($user->id == $winner->id)
	<p class="floating-img clearfix"><img src="img/trophy.png">Gefeliciteerd! Jullie zijn vandaag de winnaars van het spel, jullie hebben als eerst de draak verslagen!</p>
	@else
	<p class="floating-img clearfix"><img src="img/failed.png">Helaas, team {{ $winner->name }} was jullie voor. Maar jullie kunnen nog een plek in het klassement halen!</p>
	@endif
@endif

<!--
@if($user->countHenx())
<p class="floating-img clearfix"><img src="img/medal.png">Gefeliciteerd, jullie hebben als eerst de draak verslagen!</p>
@endif

@if($user->countHenx())
<p class="floating-img clearfix"><img src="img/gift.png">Gefeliciteerd, jullie hebben als eerst de draak verslagen!</p>
@endif
-->

<p>Onder in beeld zie je steeds je benodigde spullen, met aangevinkt welke je al hebt gekocht. Daarnaast hebben jullie onderstaande al bereikt.</p>
<ul>
	<li>{{ $user->countScans()==1?'1 locatie gevonden':$user->countScans() . ' locaties gevonden' }}</li>
	<li>{{ $user->countHenx() . ' henx in de spaarpot' }}</li>
</ul>
</div>
</div>
@if($user)
<div class="elements clearfix">
@foreach(\App\Models\Location::getElements() as $element => $name)
<div class="element">
	<img src="img/{{ $element }}.png">
	<span class="amount{{ $user->hasElement($element) ? ' buyed' : '' }}" id="{{ $element }}"></span>
</div>
@endforeach
</div>
@endif
@endsection