<div class="content">
	@foreach($users as $user)
	<div style="float: left; width: 33.33%; margin: 0 0 110px 0;">
		<div style="text-align: center;"><img style="max-width: 100%;" src="{{ url('img/liander.png') }}"></div>
		<div style="text-align: center;"><a href="{{ url('u/' . $user->code) }}">{!! QrCode::size(380)->generate(url('u/' . $user->code)) !!}</a></div>
		<div style="text-align: center; font-family: Verdana, Geneva, sans-serif; font-size: 24px; color: #008bbd;">Team {{ $user->name }}<br /> </div>
	</div>
	@endforeach
</div>