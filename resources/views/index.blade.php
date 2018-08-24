@extends ('layouts.master')

@section ('title')
Selvbetjening - nulstil kodeord - BETA version
@endsection


@section ('content')

<div id="app">
	<reset-request sitekey="{{ env('NOCAPTCHA_SITEKEY') }}"></reset-request>
	<noscript>Siden virker ikke uden JavaScript</noscript>
</div>

<script src="https://www.google.com/recaptcha/api.js?onload=vueRecaptchaApiLoaded&render=explicit" async defer></script>
<script src="/js/app.js"></script>

@endsection