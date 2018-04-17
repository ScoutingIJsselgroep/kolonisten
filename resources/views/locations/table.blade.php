@extends('layout')

@section('content')
<div class="content">
	<table>
		<thead>
			<tr>
				<th>#</th>
				<th>Naam</th>
				<th>Code</th>
				<th>Element</th>
				<th>Beschikbaar</th>
			</tr>
		</thead>
		<tbody>
			@foreach($locations as $i => $location)
			<tr>
				<td>{{ $i }}</td>
				<td>{{ $location->name }}</td>
				<td><a href="l/{{ $location->code }}">{!! QrCode::size(100)->generate(url('l/' . $location->code)) !!}</a></td>
				
				<td>{{ \App\Models\Location::getElements()[$location->element] }}</td>
				<td>{{ $location->available }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>
@endsection

@section('js')
@endsection
