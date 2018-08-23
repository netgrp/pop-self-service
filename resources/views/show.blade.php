@extends ('layouts.master')

@section ('title')
Selvbetjening - nulstil kodeord
@endsection


@section ('content')

<div id="app">
	<reset-password userinfo="{{ json_encode($userinfo) }}"></reset-password>
	<noscript>Siden virker ikke uden JavaScript</noscript>
</div>

<script src="/js/app.js"></script>

@endsection