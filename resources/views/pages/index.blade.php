@extends('layouts.app')

@section('content')
    <h1> Event Booking System </h1>
    <form class="form-inline" action="/events/search">
	    <div class="form-group">
	    	<label for="searchID">Enter keywords</label>
	    	<input class="form-control" id="searchID" type="text" name="keywords">
	    </div>
	    <div class="form-group">
    		<label for="selectCat">Select event category</label>
    		<select class="form-control" id="selectCat" name="selectCat">
    			<option value=''>Any</option>
    			<option value='1'>Careers</option>
    			<option value='2'>IT</option>
    			<option value='3'>Business</option>
    			<option value='4'>Engineering</option>
    			<option value='5'>Community</option>
                <option value='6'>Media</option>
                <option value='7'>Sport</option>
    		</select>
	    </div>
	    <div class="form-group">
	    		<button id='submit' type="submit" class="btn btn-info">Find Events</button>
	    </div>
	</form>
    @if((Auth::user()->role) == 0)
        @include('events.manager')
    @elseif((Auth::user()->role) == 1)
        @include('events.host')
    @else
    	@include('events.student')
    @endif
@endsection

