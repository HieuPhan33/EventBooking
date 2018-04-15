@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-sm">
				<div class="card">
					<div class="card bg-success text-white"><div class="card-header">Booked Events</div></div>
					<div class="card-body">
						@foreach($bookedEvents as $event)
							<a href="/events/{{$event->id}}/student"><strong>{{$event->title}}</strong></a>
							<a>{{$event->time}}</a><br>
						@endforeach
					</div>
				</div>
			</div>
			<div class="col-sm">
				<div class="card">
					<div class="card bg-info text-white"><div class="card-header">Bookmarked Events</div></div>
					<div class="card-body">
						@foreach($bookmarkedEvents as $event)
							<a href="/events/{{$event->id}}/student"> <strong>{{$event->title}}</strong></a>
							<a>{{$event->time}}</a><br>
						@endforeach
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection