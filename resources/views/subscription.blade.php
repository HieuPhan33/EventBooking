@extends('layouts.app')

@section('content')
	{!! Form::open(['action' => 'SubscribingController@store', 'method' => 'POST']) !!}
			<a>Select categories you are keen on</a>
			@foreach($categories as $category)
			<div class="checkbox">
				<!-- If user already selected this category => checked it -->
				@if($category->userID != null)
					<label><input type="checkbox" name="subscribe[]" value="{{$category->id}}" checked>{{$category->name}}</label>
				@else
				<!-- Else if user hasn't selected it => empty checkbox -->
					<label><input type="checkbox" name="subscribe[]" value="{{$category->id}}">{{$category->name}}</label>
				@endif
			</div>
			@endforeach
			<div class="form-group row">
				{{Form::submit('Submit',['class'=>'btn btn-primary'])}}
			</div>
		</div>

	{!! Form::close() !!}


@endsection