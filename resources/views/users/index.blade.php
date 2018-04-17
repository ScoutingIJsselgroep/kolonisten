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
				<th colspan="2">Henx</th>
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
					<div class="clearfix"></div>
					<div><i class="fa {{ $user->hasElement($element) ? 'fa-check-circle-o' : 'fa-circle-o' }}" aria-hidden="true"></i></div>
				</td>
				@endforeach
				
				<td class="right">{{ $user->countScans() }}</td>
				<td class="right" id="henx_{{ $user->id }}">{{ $user->countHenx() }}</td>
				<td><input type="number" class="scored" data-target="henx_{{ $user->id }}" data-url="users/{{ $user->id }}/henx/" name="henx[{{ $user->id }}]" value="{{ $user->henx }}"></td>
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

