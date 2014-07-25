@extends('core::template')

@section('title', $product->name)

@section('meta')
@stop

@section('css')
@stop

@section('js')
@stop

@section('content')
	<div class="row">
		<div class="col-sm-6">
			@foreach ($product->images as $image)
				<img src="{{ $image->src() }}" style="width:100%" />
			@endforeach
		</div>
		<div class="col-sm-6">
			{{ $product->description }}
			@foreach ($product->options as $option)
				<div class="form-group">
					{{ Form::label('options['.$option->id.']', $option->name) }}
					{{ Form::select('options['.$option->id.']', $option->drop_down(), null, array('class'=>'form-control')) }}
				</div>
			@endforeach
		</div>
	</div>
@stop