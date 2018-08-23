@extends ('layouts.master')

@section ('title')
Selvbetjening - nulstil kodeord
@endsection


@section ('content')

<div id="app">
	<reset-request></reset-request>
	<noscript>Siden virker ikke uden JavaScript</noscript>
</div>

<script src="/js/app.js"></script>

@endsection