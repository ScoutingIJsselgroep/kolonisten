<div class="content">
	@foreach($locations as $location)
	<div style="float: left; width: 33.33%; margin: 0 0 90px 0;">
		<div style="text-align: center;"><img style="max-width: 100%;" src="/img/liander.png"></div>
		<div style="text-align: center;"><a href="l/{{ $location->code }}">{!! QrCode::size(380)->generate(url('l/' . $location->code)) !!}</a></div>
		<div style="text-align: center; font-family: Verdana, Geneva, sans-serif; font-size: 24px; color: #008bbd;">{{ $location->name }} <i>{{ \App\Models\Location::getElements()[$location->element] }}</i></div>
	</div>
	@endforeach
</div>