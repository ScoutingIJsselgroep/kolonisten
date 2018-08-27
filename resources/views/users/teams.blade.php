@extends('layout')

@section('content')
<a id="info" href="/"><i class="fa fa-reply" aria-hidden="true"></i></a>

<div class="content">
<div class="background">
	<h1>Klassement</h1>
	<p>Hoe sta je er voor ten op zichte van de andere teams?</p>
	<table class="teamscore">
		<thead>
			<tr>
				<th class="rotate"><div><span>Naam</span></div></th>
				@foreach(\App\Models\User::$targets as $target => $name)
				<th class="rotate small">
					<div><span>{{ $name }}</span></div>
					<img src="img/{{ $target }}.png">
				</th>
				@endforeach
			</tr>
		</thead>
		<tbody>
			@foreach($users as $user)
			<tr class="{{ $self->id == $user->id ? 'self':'' }}">
				<td>{{ $user->name }}</td>
				
				@foreach(\App\Models\User::$targets as $target => $name)
				<td class="small">{{ (int)$user->$target }}</td>
				@endforeach
			</tr>
			@endforeach
		</tbody>
	</table>
	<p>En hoeveel grondstoffen hebben de andere teams?</p>
	<table class="teamscore">
		<thead>
			<tr>
				<th class="rotate"><div><span>Naam</span></div></th>
				@foreach(\App\Models\Location::getElements() as $element => $name)
				<th class="rotate small">
					<div><span>{{ $name }}</span></div>
					<img src="img/{{ $element }}.png">
				</th>
				@endforeach
			</tr>
		</thead>
		<tbody>
			@foreach($users as $user)
			<tr class="{{ $self->id == $user->id ? 'self':'' }}">
				<td>{{ $user->name }}</td>
				
				@foreach(\App\Models\Location::getElements() as $element => $name)
				<td class="small">{{ $user->countElement($element) }}</td>
				@endforeach
			</tr>
			@endforeach
		</tbody>
	</table>
	<p><a href="/team">Bekijk ook jullie eigen score</a></p>
</div>
</div>
@endsection

