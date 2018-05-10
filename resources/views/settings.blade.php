@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="card">
			<div class="card bg-success text-white"><div class="card-header">Setting</div></div>
			<div class="card-body">
				<form action="{{ url('/updateSettings') }}" method="get">
					<div class="form-group row">
						{{Form::label('name', 'Name',['class'=>'col-1 col-form-label'])}}  
						<div class="col-7">
							{{Form::text('name',$detail->name,['class'=>'form-control'])}}
						</div>
					</div>
					<div class="form-group row">
						{{Form::label('age', 'Age',['class'=>'col-1 col-form-label'])}}  
						<div class="col-7">
							{{Form::text('age',$detail->age,['class'=>'form-control'])}}
						</div>
					</div>
					<div class="form-group row">
						<a href="/changePassword" role="button" class="btn btn-info">Change password</a>
					</div>
					<div class="form-group row">
						{{Form::submit('Submit',['class'=>'btn btn-primary'])}}
					</div>
				</form>
			</div>
		</div>
		<div class="card">
			<div class="card bg-success text-white"><div class="card-header">Acitivty</div></div>
			<div class="card-body">
				@if(count($logs) > 0)
					<table class="table table-condensed">
						<thead>
							<tr>
								<th>Time</th>
								<th>Activity</th>
							</tr>
						</thead>
						<tbody>
							@foreach($logs as $log)
								<tr>
									<td>{{$log->timestamp}}</td>
									<td>{{$log->activity}}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				@else
					<p>No activities recently</p>
				@endif
			</div>
		</div>
	</div>
@endsection