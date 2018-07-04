@extends('base')
@section('content')
	@if(count($page_details) > 0)
    <div class="static-pages">
	    <h1>{{ ucfirst($page_details['title']) }}</h1>
	    <div class="static-content well">{{ nl2br(trim($page_details['content'])) }}</div>
    </div>
	@endif
@stop