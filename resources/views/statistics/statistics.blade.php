@extends('layouts.app')

@section('content')
<div class="radio">
	<label class="radio-inline"><input type="radio" name="chartOpt" value="0">Attendance By Category</label>
</div>
<div class="radio">
	<label class="radio-inline"><input type="radio" name="chartOpt" value="1">Attendance By Time</label>
</div>
<div class="radio">
	<label class="radio-inline"><input type="radio" name="chartOpt" value="2">Attendance By Student Type</label>
</div>
<div class="radio">
	<label class="radio-inline"><input type="radio" name="chartOpt" value="3">Profit By Category</label>
</div>
<div class="radio">
	<label class="radio-inline"><input type="radio" name="chartOpt" value="4">Profit By Time</label>
</div>
	<button onClick="generateChart()" class="btn btn-success">Generate</button>
	<div class="container" id="chartContainer">
		
		<div class="row">
			<div id="chartdiv"></div>
		</div>
		<div class="row">
			<div class="col-sm">
				<div id="chartUndergradDiv"></div>
			</div>
			<div class="col-sm">
				<div id="chartPostgradDiv"></div>
			</div>
		</div>
	</div>



@endsection