<h2> Friendly notification </h2>
<h4> Hi , {{$promoCode->username}}</h4>
<p> A new interesting event {{$promoCode->title}} is now opened to book</p>
<p> We are very pleased to give you, who are always keen on our exciting events , a {{(1-$promoCode->type)*100}}% Promotional Code <strong>{{$promoCode->promoCode}}</strong></p>
<p>Keep in touch and stay tuned</p>
<a href="{{url('/events/'.$promoCode->eventID.'/student')}}">Follow this link to book in the event</a>
<p> Kind Regards</p>
