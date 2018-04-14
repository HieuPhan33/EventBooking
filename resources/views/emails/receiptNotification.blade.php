<h2> Friendly notification </h2>
<h4> Hi {{$event->username}}</h4>
<p> You have purchased tickets for following event <strong>{{$event->title}}</strong></p>
<a> Location {{$event->location}}</a><br>
<a> Time {{$event->time}}</a>
<h5>Here is the receipt information</h5>
<p>Total Order {{$event->total}}</p>
<p>Quantity {{$event->quantity}}</p>