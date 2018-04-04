<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<base href="{{ url('/') }}">
		
        <title>St. Jorisdag | Scouting IJsselgroep</title>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css" integrity="sha384-BGv6NDAkuWxBoOcrFAufJ34dwDag61ithadL8KVUIL6w+qJaIOxImfBJpSG7LbtM" crossorigin="anonymous">
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">
		<link href="css/app.css?{{ filemtime(public_path('css/app.css')) }}" rel="stylesheet" type="text/css">
    </head>
    <body>
		@if(session()->has('admin'))
		<div class="admin-menu">
			<a href="/"><i class="fa fa-home"></i> Home</a>
			<a href="/users"><i class="fa fa-users"></i> Teams</a>
			<a href="/locations/list"><i class="fa fa-table"></i> Locaties</a>
			<a href="/locations"><i class="fa fa-map"></i> Grondstof kaart</a>
			<a href="/teams"><i class="fa fa-map"></i> Team kaart</a>
		</div>
		@endif
        @yield('content')
		<script src="http://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js" integrity="sha384-c5+Ne1Ji5HwOtH6KSZMGFax7KcQo2mQsaHZBhEPPgyI+Ems8R0fBlJ0YC4dOAiJ5" crossorigin="anonymous"></script>
		<script src="js/settings.js"></script>
		<script>
			@if (session('title'))
				$.alert({
					theme: 'supervan',
					title: '{{ session('title') }}',
					content: '{{ session('message') }}',
					buttons: {
						Prima: function(){
						}
					}
				});
			@endif
			@if (session('error'))
				$.alert({
					theme: 'supervan',
					title: '{{ session('error') }}',
					content: '{{ session('message') }}',
					buttons: {
						Jammer: function(){
						}
					}
				});
			@endif
		</script>

		@yield('js')
    </body>
</html>
