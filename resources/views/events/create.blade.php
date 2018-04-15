@extends('layouts.app')

@section('content')
			<h1>Add Event</h1>
			{!! Form::open(['action' => 'EventsController@store', 'method' => 'POST']) !!}
				<div class="form-group row">
					{{Form::label('title', 'Title',['class'=>'col-1 col-form-label'])}}  
					<div class="col-7">
						{{Form::text('title','',['class'=>'form-control','placeholder' => 'title'])}}
					</div>
				</div>

				<div class="form-group row">
					{{Form::label('description', 'Description',['class'=>'col-1 col-form-label'])}}
					<div class="col-7">
						{{Form::textarea('description','',['class'=>'form-control','placeholder' => 'Description'])}}
					</div>
				</div>

				<div class="form-group row"> 
					{{Form::label('host', 'Event Host',['class'=>'col-1 col-form-label'])}} 
					<div class="col-7">
						<?php
							$hostList = array();
							foreach($hosts as $host){
								$hostList[$host->id] = $host->name;
							}
						?>
						{{Form::select('host',$hostList,['class'=>'form-control'])}}


					</div>
				</div>

				<div class="form-group row">
					{{Form::label('category', 'category',['class'=>'col-1 col-form-label'])}}  
					<div class="col-7">
						<?php
							$categoryList = array();
							foreach($categories as $category){
								$categoryList[$category->id] = $category->name;
							}
						?>
						{{Form::select('category',$categoryList,['class'=>'form-control'])}}

					</div>
				</div>

				<div class="form-group row">
					{{Form::label('location','Location',['class'=>'col-1 col-form-label'])}}
					<div class="col-4">
						{{Form::text('location','',['class'=>'form-control','placeholder'=>'Location',''])}}
					</div>
				</div>

				<div class="form-group row">
					{{Form::label('capacity','capacity',['class'=>'col-1 col-form-label'])}}
					<div class="col-4">
						{{Form::text('capacity','',['class'=>'form-control','placeholder'=>'capacity',''])}}
					</div>
				</div>

				<div class="form-group row">
					{{Form::label('datetime','Time',['class'=>'col-1 col-form-label'])}}
					<div class="col-4">
						{{Form::date('datetime','',['class'=>'form-control'])}}
					</div>
				</div>

				<div class="form-group row">
					{{Form::label('price','price',['class'=>'col-1 col-form-label'])}}
					<div class="col-4">
						{{Form::text('price','',['class'=>'form-control','onkeyup'=>'validatePrice()'])}}
					</div>
				</div>

				<div name="promoCodesContainer"></div>
				<button style="display:none" name="addCodesBtn" type="button" onClick="addCodes()">Add Promotional Code</button>
				<input id="numOfCodes" name="numOfCodes" value="0" type="hidden">
				<div class="form-group row">
					{{Form::submit('Submit',['class'=>'btn btn-primary'])}}
				</div>
			{!! Form::close() !!}
		</div>
		<div class="col-sm-2 sidenav">
		</div>

@endsection