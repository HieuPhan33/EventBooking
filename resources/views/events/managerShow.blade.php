@extends('layouts.app')

@section('content')
    <h1> {{$event->title}} </h1>
    <h2>Category :
    	<small>{{$event->category}}</small>
    </h2>
	<b>Description</b><br>
	<a>{{$event->description}}</a><br>
	<a>Date &nbsp; {{$event->time}}</a><br>
	<a>Location &nbsp; {{$event->location}}</a><br>
	<form action="{{ url('/events', ['id' => $event->id]) }}" method="post">
	    <input type="hidden" name="_method" value="delete" />
	    <button type="submit" class="btn btn-warning">Delete Event</button>
	    {!! csrf_field() !!}
	</form>


@endsection