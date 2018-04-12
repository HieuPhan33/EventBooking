@if(count($events)>0)
	@foreach($events as $event)
		<div class="well">
			<h3><a href="/events/{{$event->id}}/host">{{$event->title}}</a></h3>
			<a>Description</a><br>
			<a>{{$event->description}}</a><br>
			<a>Date &nbsp; {{$event->time}}</a><br>
			<a>Location &nbsp; {{$event->location}}</a><br>
			<br><br>
		</div>
	@endforeach
	{{ $events->links() }}
@else
	<div class="well"> No Events Found </div>
@endif	
