@extends('layout')

@section('content')
<div class="content">
	<form action="" method="post" class="full">
	<table>
		<thead>
			<tr>
				<th>Naam</th>
				<th>Code</th>
				<th>Lock</th>
				
				@foreach(\App\Models\Location::getElements() as $element => $name)
				<th class="right">{{ $name }}</th>
				@endforeach
				
				<th class="right">Scans</th>
				<th class="right">Vlaggen</th>
				<th class="right">Huizen</th>
				<th class="right">B&amp;B&#39;s</th>
				<th class="right">Cafe&#39;s</th>
			</tr>
		</thead>
		<tbody>
			@foreach($users as $user)
			<tr>
				<td>{{ $user->name }}</td>
				<td><a href="u/{{ $user->code }}">{!! QrCode::size(100)->generate(url('u/' . $user->code)) !!}</a></td>
				<td>
				@if($user->lock)
					Ja, <a href="users/unlock/{{ $user->id }}">afmelden</a>
				@else
					Nee
				@endif
				</td>
				
				@foreach(\App\Models\Location::getElements() as $element => $name)
				<td class="right">
					<div class="clearfix"><input type="number" class="scored" data-target="{{ $element }}_{{ $user->id }}" data-url="users/{{ $user->id }}/score/{{ $element }}/" name="{{ $element }}[{{ $user->id }}]" value="{{ $user->{$element} }}"></div>
					<div id="{{ $element }}_{{ $user->id }}">{{ $user->countElement($element) }}</div>
				</td>
				@endforeach
				
				<td class="right">{{ $user->countScans() }}</td>
				<td class="right">{{ $user->countFires() }}</td>
				<td class="right">{{ $user->countCoalenergys() }}</td>
				<td class="right">{{ $user->countGasenergys() }}</td>
				<td class="right">{{ $user->countSustainables() }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
	</form>
</div>
@endsection

@section('js')
<script>
$('.scored').change(function() {
	$.ajax({
		url: $(this).data('url') + $(this).val(),
		context: this
	}).done(function(data) {
		$('#' + $(this).data('target')).html(data.amount);
	});
});
</script>
@endsection

