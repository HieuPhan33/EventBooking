<h2> Friendly notification </h2>
<h4> Hi , {{$promoCode[0]->username}}</h4>
<h2> Here are the list of promotional codes for new event {{$promoCode[0]->title}}</h2>
<table class="table table-condensed">
	<thead>
		<tr>
			<th>Code</th>
			<th>Discount</th>
		</tr>
	</thead>
	<tbody>
		@foreach($promoCode as $code)
			<tr>
				<td>{{$code->promoCode}}</td>
				<td>{{ (1-$code->type)*100}}%</td>
			</tr>
		@endforeach
	</tbody>
<?php
	$url = "http://localhost:8000/events/".$promoCode[0]->eventID."/manager";
?>
<a href="{{$url}}">Follow this link to view the event</a>
<p> Kind Regards</p>
