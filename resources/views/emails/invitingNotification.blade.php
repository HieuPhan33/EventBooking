<h2> Friendly notification </h2>
<h4> Hi , {{$event->username}}</h4>
<p> A new interesting event {{$event->title}} is now opened to book</p>
<a>Hurry up you may miss the chance to join in this great event</a><br>
<?php
	$url = "http://localhost:8000/events/".$event->id."/student";
?>
<a href="{{$url}}">Follow this link to book in the event</a>
