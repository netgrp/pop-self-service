@extends ('layouts.master')

@section ('title')
Selvbetjening - nulstil kodeord - BETA version
@endsection


@section ('content')

<div id="app">
	<reset-password userinfo="{{ json_encode($userinfo) }}" token="{{ $pass->pass }}"></reset-password>
	<noscript>Siden virker ikke uden JavaScript</noscript>
</div>

<script src="/js/app.js"></script>

@endsection