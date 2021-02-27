@extends('layouts.app')
@section('title','Manufacturing Orders')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> Create Orders</h1>
    
</section>

<!-- Main content -->
<section class="content">
{!! Form::open(['url' => action('ManufacturingController@orderstore'), 'method' => 'post', 
    'id' => 'product_add_form','class' => 'product_form', 'files' => true ]) !!}
    @component('components.widget', ['class' => 'box-primary'])
        <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('title',  'Title *') !!}
              {!! Form::text('title', !empty($duplicate_product->name) ? $duplicate_product->name : null, ['class' => 'form-control', 'required',
              'placeholder' => 'Title']); !!}
          </div>
        </div>
       
       

  

        <div class="col-sm-4 @if(!session('business.enable_category')) hide @endif">
          <div class="form-group">
            {!! Form::label('category_id', __('product.category') . ':') !!}
              {!! Form::select('category_id', $categories, !empty($duplicate_product->category_id) ? $duplicate_product->category_id : null, ['placeholder' => __('messages.please_select'), 'required','class' => 'form-control select2']); !!}
          </div>
        </div>

       


       	<div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('order_no',  'Order No') !!} 
            {!! Form::text('order_no', null, ['class' => 'form-control', 'required',
              'placeholder' => 'Order No']); !!}
          </div>
        </div>
		
		<div class="clearfix"></div>
		
		
     

        
       
    
        <div class="clearfix"></div>
        <div class="col-sm-8">
          <div class="form-group">
            {!! Form::label('description', 'Description') !!}
              {!! Form::textarea('description', !empty($duplicate_product->description) ? $duplicate_product->description : null, ['class' => 'form-control']); !!}
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('photo', 'Photo') !!}
            {!! Form::file('photo', ['id' => 'upload_image', 'accept' => 'image/*']); !!}
            <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p></small>
          </div>
        </div>
        </div>
		
		<div class="clearfix"></div>
		
		
		<div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('Quantity',  'Quantity') !!} 
            {!! Form::text('quantity', null, ['class' => 'form-control', 'required',
              'placeholder' => 'quantity']); !!}
          </div>
        </div>
		
		
		<div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('incharge',  'Incharge') !!} 
            {!! Form::text('incharge', null, ['class' => 'form-control',
              'placeholder' => 'incharge']); !!}
          </div>
        </div>
		
		<div class="col-sm-4">
            
                    <label for="priority">Priority</label>
                    <div class="input-group" style="width:100%">
                        <select class="form-control" name="priority" id="priority" >
						 <option> Low </option>
						 <option> Medium </option>
						 <option> High </option>
						</select>
                    </div>
            
        </div>
		
		<div class="clearfix"></div>
		
        <div class="col-sm-4">
            
                    <label for="start_date">Start Date</label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        <input type="text" name="start_date"  id="start_date" value="{{@format_date('now')}}" class="form-control" readonly>
                    </div>
         
        </div>
		
		
		<div class="col-sm-4">
            
                    <label for="end_date">End Date</label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        <input type="text" name="end_date" id="end_date" value="{{@format_date('now')}}" class="form-control" readonly>
                    </div>
            
        </div>
        	<div class="col-sm-4">
            
                    <label for="end_date">Status</label>
                    <div class="input-group" style="width:100%">
                        <select class="form-control" name="priority" id="priority" >
						 <option> Not Started </option>
						 <option> Processing </option>
						 <option> Pending </option>
						 <option> Completed </option>
						</select>
                    </div>
            
        </div>
			
			
			
			
			<div class="clearfix"></div>
			<div class="clearfix"></div>
		
		
		    <div class="row" style="margin-top:50px">
    <div class="col-sm-12">
      <input type="hidden" name="submit_type" id="submit_type">
      <div class="text-left" style="margin-left:2%">
      <div class="btn-group">
   

        <button type="submit" value="submit" class="btn btn-primary">Submit</button>
      </div>
      
      </div>
    </div>
  </div>
    @endcomponent
	
	


  
 
{!! Form::close() !!}
  
</section>
<!-- /.content -->

@endsection

@section('javascript')
  @php $asset_v = env('APP_VERSION'); @endphp

	<script type="text/javascript">
    $(document).ready( function(){
		
		
        //Date picker
        $('#end_date').datepicker({
            autoclose: true,
            format: datepicker_date_format
        });
		
		$('#start_date').datepicker({
            autoclose: true,
            format: datepicker_date_format
        });
        
    });

  </script>
@endsection
