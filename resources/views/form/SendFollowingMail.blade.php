@extends('layouts.app')
@section('content')
	{!! Form::open(['action' => 'MailController@sendFollowingUpMail', 'method' => 'POST', 'files'=>'true']) !!}
		<div class="form-group">
			<label for='title'>Title: </label>
			<input type='text' class="form-control" id="title" name='title'>
		</div>
		<div class="form-group">
			<label for='message'>Message</label>
			<textarea class="form-control" id="message" name='message'></textarea>
		</div>
		<div class="form-group">
			<label for='uploaded_file'>Select A File To Upload:</label>
			{{Form::file('uploaded_file')}}
		</div>
		<?php
			$emailList = $contacts[0]->email;
			$nameList = $contacts[0]->name;
			for($i = 1 ; $i < count($contacts); $i++){
				$emailList = $emailList.','.$contacts[$i]->email;
				$nameList = $nameList.','.$contacts[$i]->name;
			}
		?>
		<input type="hidden" name="emails[]" value="{{$emailList}}">
		<input type="hidden" name="names[]" value="{{$nameList}}">
		<input type="hidden" name="event" value="{{$contacts[0]->event}}">
		<button type="submit" class="btn btn-primary" id="submitButton" onClick="disable(id)">Submit</button>
	{!! Form::close() !!}
@endsection