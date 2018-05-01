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
				@foreach(\App\Models\Location::getElements() as $element => $name)
				<th class="rotate small"><div><span>{{ $name }}</span></div></th>
				@endforeach
				<th class="rotate"><div><span>Henx</span></div></th>
				<th class="rotate"><div><span>Draak</span></div></th>
			</tr>
		</thead>
		<tbody>
			@foreach($users as $user)
			<tr class="{{ $self->id == $user->id ? 'self':'' }}">
				<td>{{ $user->name }}</td>
				
				@foreach(\App\Models\Location::getElements() as $element => $name)
				<td class="small">
					<div><i class="fa {{ $user->hasElement($element) ? 'fa-check-circle-o' : 'fa-circle-o' }}" aria-hidden="true"></i></div>
				</td>
				@endforeach
				
				<td class="right" id="henx_{{ $user->id }}">{{ $user->countHenx() }}</td>
				<td class="right" id="henx_{{ $user->id }}">{{ $user->draak ? $user->draak->format('H:m') : '' }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
	</form>
</div>
</div>
@endsection

