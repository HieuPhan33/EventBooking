@extends('layouts.app')
@section('content')
	{!! Form::open(['action' => 'AbsenceController@store', 'method' => 'POST']) !!}
		@if(count($users)>0)
			<a>Select persons who were absent from an event</a>
			@foreach($users as $user)
			<div class="checkbox">
				<label><input type="checkbox" name="absentee[]" value="{{$user->id}}">{{$user->name}}</label>
			</div>
			@endforeach
			<input type="hidden" name="event" value="{{$user->eventID}}">
			<div class="form-group row">
				{{Form::submit('Submit',['class'=>'btn btn-primary'])}}
			</div>
		@else
			<a>No user has registered this event yet</a>
		@endif

	</form>
	{!! Form::close() !!}
@endsection