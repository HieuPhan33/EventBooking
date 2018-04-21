<form action="/guessIt">
	buying price 
	<select name="buying">
		<option value="vhigh">Very high</option>
		<option value="high">High </option>
		<option value="med">Medium </option>
		<option value="low">Low</option>
	</select><br>

	maintenance price
	<select name="maint">
		<option value="vhigh">Very high</option>
		<option value="high">High </option>
		<option value="med">Medium </option>
		<option value="low">Low</option>
	</select><br>
	number of doors 
	<select name="doors">
		<option value="2">2</option>
		<option value="3">3 </option>
		<option value="4">4</option>
		<option value="5more">5-more</option>
	</select><br>
	capacity in persons
	<select name="persons">
		<option value="2">2</option>
		<option value="4">4 </option>
		<option value="more">more</option>
	</select><br>
	size of luggage boot 
	<select name="lug_boots">
		<option value="small">Small</option>
		<option value="med">Medium </option>
		<option value="big">Big </option>
	</select><br>
	safety
	<select name="safety">
		<option value="low">Small</option>
		<option value="med">Medium </option>
		<option value="high">Big </option>
	</select><br>
	<input type="submit">
</form>
@if(!empty($guess))
	This car is {{$guess}}
@endif