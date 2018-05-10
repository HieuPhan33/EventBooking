@if(count($events)>0)
	@foreach($events as $event)
		<div class="card">
			<div class="card-header"><h3><a href="/events/{{$event->id}}/host">{{$event->title}}</a></h3></div>
				<div class="card-body">
			<a>Description</a><br>
			<a>{{$event->description}}</a><br>
			<a>Date &nbsp; {{$event->time}}</a><br>
			<a>Location &nbsp; {{$event->location}}</a><br>
			<br><br>
			</div>
		</div>
	@endforeach
	{{ $events->links() }}
@else
	<div class="well"> No Events Found </div>
@endif	
