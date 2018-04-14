@extends('layouts.app')

@section('content')
	@if($event->isBookmarked)
		<small role="button" class="btn btn-primary btn-sm" onClick="cancelBookmark(id)" id="{{$event->id}}"> Remove Bookmark</small>
	@else
		<small role="button" class="btn btn-primary btn-sm" onClick="addBookmark(id)" id="{{$event->id}}" > Bookmark Event </small>
	@endif
    <h1> {{$event->title}} </h1>
    <h2>Category :
    	<small>{{$event->category}}</small>
    </h2>
	<b>Description</b><br>
	<a>{{$event->description}}</a><br>
	<a>Date &nbsp; {{$event->time}}</a><br>
	<a>Location &nbsp; {{$event->location}}</a><br>
	@if($event->isBooked)
		<a role="button" class="btn btn-secondary" id="cancel{{$event->id}}"  href="{{action('BookingController@cancel',['eventID'=>$event->id])}}">Cancel</a>
	@else
		@if(auth()->user()->isBanned)
			<p class="text-danger"> You have been banned from Booking Event</p>
		@else
			@if($event->slotsLeft > 0)
				@if($event->price != 0)
					@if(session('isPromoted'))
						<p>Price <s>{{$event->price}} AUD</s> <large class="text-success">{{($event->price)*session('type')}} AUD</large>
					@else
						<p>Price {{$event->price}}</p>
					@endif
				<!-- Trigger the modal with a button -->
				<hr>
				<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#ticketModal">Buy ticket</button>
				<!-- Buy ticket -->
				<div id="ticketModal" class="modal fade" role="dialog">
				  <div class="modal-dialog">

				    <!-- Modal content-->
				    <div class="modal-content">
				      <div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
				        <h4 class="modal-title">Select number of ticket</h4>
				      </div>
				      <div class="modal-body">
				      	@if(!session('isPromoted') && !empty($codes))
				      		<a href="#enterpromo" data-toggle="modal">Enter Promotional Code</a>
				      	@endif
				      	<form id="checkoutForm" method="post" action="/events/{{$event->id}}/checkout">
				      		{{csrf_field()}}
				      	<p>Buy ticket  <strong>  {{$event->title}}</strong> 
				      		<a style="padding-left:5em">
					      	@if(session('isPromoted'))
					      		<input type="hidden" name="promoCode" value="{{session('code')}}">
				      			{{$price = $event->price * session('type')}}x1 +      		
				      		@endif
				      			{{$price = $event->price}}
				      			x
				      		</a>
				      		<select onchange="validateQuantity()" name="quantity">
				      			@if($event->slotsLeft>=5)
					      			@for($i = 0; $i <= 5; $i++)
					      				<option value="{{$i}}">{{$i}}</option>
					      			@endfor
					      		@else
					      			@for($i = 0; $i <= $event->slotsLeft ; $i++)
					      				<option value="{{$i}}">{{$i}}</option>
					      			@endfor
					      		@endif
				      		</select>
				      	</p>

				      	<input type="hidden" name="type" value="{{session('type')}}">
				      	@if(session('isPromoted'))
				      		<button id="checkoutButton" type="submit">Checkout</button>
				      	@else
				      		<button style="display:none;" id="checkoutButton" type="submit">Checkout</button>
				      	@endif
				      	
				      	</form>

				      </div>

				      <div class="modal-footer">
				        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				      </div>

				  </div>
				</div>
					@if(!empty($codes))
						<!-- Enter Promo Modal -->
						<div id="enterpromo" class="modal fade" role="dialog">
						  <div class="modal-dialog">

						    <!-- Modal content-->
						    <div class="modal-content">
						      <div class="modal-header">
						        <button type="button" class="close" data-dismiss="modal">&times;</button>
						        
						        <h4 class="modal-title">Enter promo code</h4>
						        
						      </div>
						      <div class="modal-body">
						      	<p class="text-danger" id="err"></p>
						      	<form method="post" onsubmit="return validatePromoCode({{ $codes}})" action="/events/{{$event->id}}/enterPromo">
						      		{{csrf_field()}}
						      		<div class="form-group">
						      			<label for="code">Promotional code </label>
						      			<input type="text" class="form-control" id="code" name="code">
						      		</div>
						      		<input type="hidden" name="type" id="type">
						      		<button type="submit" class="btn btn-default">Apply</button>
						      	</form>
						      </div>
						      
						      <div class="modal-footer">
						        <button type="button"  class="btn btn-default" data-dismiss="enterpromo">close</button>
						      </div>
						    </div>

						  </div>
						</div>
					@endif
				@else
					<a role="button" class="btn btn-info" id="book{{$event->id}}" href="{{action('BookingController@book',['eventID'=>$event->id])}}">Book In</a>
				@endif
			@else
				<p class="text-danger"> Closed , No slots left</p>
					@if($event->isEnqueued)
						<a role="button" class="btn btn-warning" href="{{action('BookingController@dequeue',['eventID'=>$event->id])}}">Leave the waiting queue list</a>
					@else
						<a role="button" class="btn btn-secondary" id="join{{$event->id}}" onClick="sendJoinQueueMail({{$event->id}})" href="{{action('BookingController@enqueue',['eventID'=>$event->id])}}">Join the waiting queue list</a>
					@endif
			@endif
		@endif
	@endif


@endsection