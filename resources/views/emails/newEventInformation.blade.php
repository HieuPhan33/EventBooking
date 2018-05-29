<h2> Friendly notification </h2>
<h4> Hi , {{$username->name}}</h4>
<a> There is new event <strong>{{$event->title}}</strong> that you may be interested in</a><br>
<p>{{$event->time}}</p>
<p>{{$event->location}}</p>
<a>Hurry up you may miss the chance to join in this great event</a><br>
<?php
	$url = "http://localhost:8000/events/".$event->id."/student";
?>
<a href="{{$url}}">Follow this link to book in the event</a>
