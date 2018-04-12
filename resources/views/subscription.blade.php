@extends('layouts.app')

@section('content')
	{!! Form::open(['action' => 'SubscribingController@store', 'method' => 'POST']) !!}
			<a>Select categories you are keen on</a>
			@foreach($categories as $category)
			<div class="checkbox">
				<label><input type="checkbox" name="subscribe[]" value="{{$category->id}}">{{$category->name}}</label>
			</div>
			@endforeach
			<div class="form-group row">
				{{Form::submit('Submit',['class'=>'btn btn-primary'])}}
			</div>

	{!! Form::close() !!}

@endsection