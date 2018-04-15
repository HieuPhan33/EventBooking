<h2> Friendly notification </h2>
<h4> Hi , {{$event->username}}</h4>
<p> A new interesting event {{$promoCode->title}} is now opened to book</p>
<a>Hurry up you may miss the chance to join in this great event</a><br>
<a href="{{url('/events/'.$event->id.'/student')}}">Follow this link to book in the event</a>
