@extends('layouts.app')
@section('title', 'Manufacture Product List')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Manufacturing
        <small>Product List</small>
    </h1>
   
</section>

<!-- Main content -->
<section class="content">






<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' =>  'Manufacture Product List :-  '.$order->title ])
        @can('category.create')
            @slot('tool')
               
            @endslot
        @endcan
        @can('category.Details')
		{!! Form::open(['url' => action('ManufacturingController@productdetailsstore'), 'method' => 'post', 
    'id' => 'product_add_form','class' => 'product_form', 'files' => true ]) !!}
		
		<div class="section">
		<h3> Product Name :-  <span style="color:blue"><?php echo $order->title?>  </span></h3>
			
        <div class="row">

		
		
		<div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('quantity',  'Manufactured Quantity') !!} 
            {!! Form::number('quantity', null, ['class' => 'form-control',
              'placeholder' => 'Manufactured Quantity']); !!}
          </div>
        </div>
		
		
			
		<div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('damage_quantity',  'Damage  Quantity') !!} 
            {!! Form::number('damage_quantity', null, ['class' => 'form-control',
              'placeholder' => 'Damage  Quantity']); !!}
          </div>
        </div>
		
		
        <div class="col-sm-3">
            
                    <label for="start_date">Manufacturing Date</label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        <input type="text" name="manuf_date" id="manuf_date" value="{{@format_date('now')}}" class="form-control" readonly>
                    </div>
         
        </div>
		
		<div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('remarks',  'Remarks') !!} 
            {!! Form::text('remarks', null, ['class' => 'form-control',
              'placeholder' => 'Remarks']); !!}
          </div>
        </div>
		
		
		<div class="col-sm-3">
            <div class="form-group" style=" margin-top: 6%; margin-bottom: 14%;">
			 <input type="hidden" name="order_id" value="{{$order->id}}">
                    <button type="submit" value="submit" class="btn btn-primary">Submit</button>
			</div>		
         
        </div>
		
		
		
	
    
			
			
			</div>
			
			
	
  
 

  
		
		</div>
		
		
		
		
		{!! Form::close() !!}
		
		
		
            <div class="table-responsive">
			
                <table class="table table-bordered table-striped" id="productdetails_table">
                    <thead>
                        <tr>
                       
                            <th>Manufacured Items</th>
							<th>Damaged  Items</th>
							<th>Manufacure Date</th>
							<th>Remarks</th>
							
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
					
					
					
				
			
					
					
                </table>
				
				 <div class="row"  style="width:95%;margin-top:2%;margin-left:4%">
				 <div class="col-sm-6">
				
				 <h3> Total Items : {{ $order->quantity}} </h3>
				 <h3> Manufactured Items :  {{ $finished_production }}  </h3>
				 <h3> Damage Items :  {{ $damage_quantity }}  </h3>
				 
				 
				  
				 <h3> Pending Items : {{ $order->quantity - $finished_production- $damage_quantity}} </h3>
				 <?php
						   if($order->end_date !='')
						   {
						   $deadline = date("m/d/Y", strtotime($order->end_date) );
						   }
						   ?>
				 <h3> Delivery Date : {{ @$deadline }} </h3>
				 <h3> Status: {{ $order->status }}</h3>
				 </div>
				 
				 <div class="col-sm-6">
				 <?php
				 if($order->product_id == '')
				 {
					 ?>
				 {!! Form::open(['url' => action('ManufacturingController@savetopos'), 'method' => 'post', 
                     'id' => 'pos_add_form','class' => 'pos_add_form', 'files' => true ]) !!}
					 <table class="table table-bordered table-striped" i>
                    <thead>
                          <tr>
                       
                            <td>Product Name</td>
							<td><input type="text" class="form-control" name="product_name" required value="<?php echo $order->title?>"></td>
							
                          </tr>
						  <tr>
                       
                            <td>Product SKU</td>
							<td><input type="text" class="form-control" name="product_sku" required value=""></td>
							
                          </tr>
						  <tr>
                       
                            <td>Category</td>
							<td>
							 
						        {!! Form::select('category_id', $categories, $order->category_id, ['placeholder' => __('messages.please_select'), 'required', 'class' => 'form-control select2']); !!}
        
					
							</td>
							
                          </tr>
						    <tr>
                       
                            <td>Brands</td>
							<td>
							 
						        {!! Form::select('brand_id', $brands, null, ['placeholder' => __('messages.please_select'), 'required', 'class' => 'form-control select2']); !!}
        
					
							</td>
							
                          </tr>
						 	  <tr>
                       
                            <td>Quantity</td>
							<td><input type="text" class="form-control" name="product_quantity" required  value="<?php echo  $finished_production ?>"></td>
							
                         </tr>
						 <tr>
                       
                            <td>Price</td>
							<td><input type="text" class="form-control" name="product_price" value="" required></td>
							
                         </tr>
						 <tr>
                       
                            <td><input type="hidden" class="form-control" name="order_id" required value="<?php echo $order->id?>"></td>
							<td> <button type="submit" class="btn btn-warning btn-lg">Procced to Sales</button></td>
							
                         </tr>
                    </thead>
					
					 </table>
				
				 
				 {!! Form::close() !!}
                 <?php
				 }
				 ?>
				 </div>
				 
            </div>
        @endcan
    @endcomponent

    <div class="modal fade productdetails_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection


@section('javascript')
  @php $asset_v = env('APP_VERSION'); @endphp

	<script type="text/javascript">
    $(document).ready( function(){
		
	
		
		$('#manuf_date').datepicker({
            autoclose: true,
            format: datepicker_date_format
        });
        
    });

  </script>
@endsection