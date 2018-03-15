@extends('layout')

@section('content')
<form id="addForm" action="/login" method="post">
	{{ csrf_field() }}
	<div class="clearfix">
		<label for="user">Naam</label>
		<input type="text" name="user" id="user" value="{{ old('user') }}">
	</div>
	<div class="clearfix">
		<label for="pass">Wachtwoord</label>
		<input type="password" name="pass" id="pass">
	</div>
	<div>
		<button>Inloggen</button>
	</div>
</form>
@endsection

@section('js')
@endsection
