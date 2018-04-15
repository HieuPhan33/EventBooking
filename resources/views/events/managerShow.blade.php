@extends('layouts.app')

@section('content')

    <div class="container">
    	<h1> {{$event->title}} </h1>
    	<div class="row">
	    	<div class="col-sm">
			    <h2>Category :
			    	<small>{{$event->category}}</small>
			    </h2>
				<b>Description</b><br>
				<a>{{$event->description}}</a><br>
				<a>Date &nbsp; {{$event->time}}</a><br>
				<a>Location &nbsp; {{$event->location}}</a><br>
				<table>
					<tr>
						<td>
							<form action="{{ url('/events', ['id' => $event->id]) }}" method="post">
							    <input type="hidden" name="_method" value="delete" />
							    <button type="submit" class="btn btn-warning">Delete Event</button>
							    {!! csrf_field() !!}
							</form>
						</td>
						<td>
							<a href="{{ URL::to('/events/'. $event->id .'/edit')}}" class="btn btn-success">Edit Event</a>
						</td>
					</tr>
				</table>
			</div>
			@if($event->isPromoted != null)
				<div class="col-">
					<button style="display:none" id="closeListBtn" onclick="closeList()">Close list</button>
					<button type="button" id="listBtn" onclick="getPromoCodes({{$event->id}})">List Promotional Codes </button>
					<div id="codesContainer" ></div>
				</div>
			@endif
		</div>
	</div>



@endsection