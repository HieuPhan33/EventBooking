@extends('layouts.app')

@section('content')
			<form action="{{ url('/events', ['id' => $event->id]) }}" method="post">
     			{{method_field('PATCH')}}
     			{{ csrf_field() }}
				<div class="form-group row">
					{{Form::label('title', 'Title',['class'=>'col-1 col-form-label'])}}  
					<div class="col-7">
						{{Form::text('title',$event->title,['class'=>'form-control','placeholder' => 'Title']	)}}
					</div>
				</div>

				<div class="form-group row">
					{{Form::label('description', 'Description',['class'=>'col-1 col-form-label'])}}
					<div class="col-7">
						{{Form::textarea('description',$event->description,['class'=>'form-control','placeholder' => 'Description'])}}
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
						{{Form::text('location',$event->location,['class'=>'form-control','placeholder'=>'Location',''])}}
					</div>
				</div>

				<div class="form-group row">
					{{Form::label('capacity','capacity',['class'=>'col-1 col-form-label'])}}
					<div class="col-4">
						{{Form::text('capacity',$event->capacity,['class'=>'form-control','placeholder'=>'capacity',''])}}
					</div>
				</div>

				<div class="form-group row">
					{{Form::label('datetime','Time',['class'=>'col-1 col-form-label'])}}
					<div class="col-4">
						{{Form::date('datetime',$event->time,['class'=>'form-control'])}}
					</div>
				</div>

				<div class="form-group row">
					{{Form::label('price','price',['class'=>'col-1 col-form-label'])}}
					<div class="col-4">
						{{Form::text('price',$event->price,['class'=>'form-control','onkeyup'=>'validatePrice()'])}}
					</div>
				</div>
				<div name="promoCodesContainer"></div>
				<button style="display:none" name="addCodesBtn" type="button" onClick="addCodes()">Add Promotional Code</button>


				<input name="numOfCodes" value="0" type="hidden">
				<div class="form-group row">
					{{Form::submit('Submit',['class'=>'btn btn-primary'])}}
				</div>
			</form>
@endsection