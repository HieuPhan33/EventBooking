<h2> Friendly notification </h2>
<h4> Hi , {{$event->username}}</h4>
<a> {{$event->title}} is now opened to book</a><br>
<a>Hurry up you may miss the chance to join in this great event</a><br>
<a href="{{url('/events/'.$event->id.'/student')}}">Follow this link to book in the event</a>
