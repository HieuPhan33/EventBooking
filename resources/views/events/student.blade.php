@if(count($events)>0)
	@foreach($events as $event)
		<div class="card">
		<div class="card-header"><h4><a href="/events/{{$event->id}}/student">{{$event->title}}</a></h4></div>
			<div class="card-body">
			@if($event->isBookmarked)
				<small role="button" class="btn btn-primary btn-sm" onClick="cancelBookmark(id)" id="{{$event->id}}"> Remove Bookmark</small>
			@else
				<small role="button" class="btn btn-primary btn-sm" onClick="addBookmark(id)" id="{{$event->id}}" > Bookmark Event </small>
			@endif
			<p>Description</p>
			<p>{{$event->description}}</p>
			@if($event->isBooked)
				<p class="text-success">Booked</p>
			@else
				@if($event->slotsLeft > 3)
					<p class="text-success">Open to book</p>
				@elseif($event->slotsLeft > 0)
					<p class="text-success">Open to book</p>
					<p class="text-warning"> Only {{$event->slotsLeft}} slot(s) left , hurry up </p>
				@else
					<p class="text-danger"> Closed , No slots left</p>
					@if($event->isEnqueued)
						<a role="button" class="btn btn-warning" href="{{action('BookingController@dequeue',['eventID'=>$event->id])}}">Leave the waiting queue list</a>					
					@else
						<a role="button" class="btn btn-secondary" href="{{action('BookingController@enqueue',['eventID'=>$event->id])}}">Join the waiting queue list</a>
					@endif
				@endif
			@endif
			<p>Date &nbsp; {{$event->time}}</p>
			<p>Location &nbsp; {{$event->location}}</p>
			@if($event->price != 0)
				<p>Price {{$event->price}}</p>
			@endif
		</div>
		</div>
					<br><br>
	@endforeach
		{{ $events->links() }}
@else
	<div class="well"> No Events Found </div>
@endif