@extends('layouts.app')
@section('title','Assign Materials')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> Assign Materials</h1>
    
</section>

<!-- Main content -->
<section class="content">
{!! Form::open(['url' => action('ManufacturingController@materialassignstore'), 'method' => 'post', 
    'id' => 'product_add_form','class' => 'product_form', 'files' => true ]) !!}
    @component('components.widget', ['class' => 'box-primary'])
        <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('title',  'Title *') !!}
              {!! Form::text('title', $order->title, ['class' => 'form-control', 'required', 'readonly',
              'placeholder' => 'Title']); !!}
			  
			  <input type="hidden" name="order_id" value="{{$order->id}}">
			  <input type="hidden" name="order_quantity"  id="order_quantity"  value="{{$order->quantity}}">
          </div>
        </div>
       
       

  

       

       


       	<div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('order_no',  'Order No') !!} 
            {!! Form::text('order_no', $order->order_no, ['class' => 'form-control',
              'placeholder' => 'Order No' ,'readonly']); !!}
          </div>
        </div>
		
		<div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('product_quantity',  'No of Products') !!} 
            {!! Form::text('product_quantity', $order->quantity, ['class' => 'form-control',
              'placeholder' => 'No of Products' ,'readonly']); !!}
          </div>
        </div>
		
		<div class="clearfix"></div>
		
		
     
	 <div class="page-content page-container" id="page-content">
     <div class="padding">
         <div class="row container d-flex justify-content-center">
             <div class="col-lg-12 grid-margin stretch-card">
                 <div class="card">
                     <div class="card-body">
                         
                         <div class="table-responsive">
                             <table id="materialstable" class="table table-hover">
                                 <thead>
                                     <tr>
                                         <th>Material</th>
                                         <th>Quantity/piece</th>
										 <th>Unit</th>
										 <th>Total Quantity  </th>
                                         
                                     </tr>
                                 </thead>
                                 <tbody>
								 <?php
								 $i=0;
								 ?>
								 @if(isset($materials_lines))
								
	 
                                 @foreach( $materials_lines as $materials_line )
							 <tr  id="materialstable-row<?php echo $i?>">
                                         <td width="55%">
										  
                                         {!! Form::select('materterial_id[]', $materials->prepend('Please Select', ''),  $materials_line->materterial_id, [ 'required', 'class' => 'form-control  ']); !!}
         
									
										 </td>
                                         <td><input type="number" name="quantity[]"   value="<?php echo $materials_line->quantity?>"   onkeyup="quantity_cal(this.value,'materialstable-row<?php echo $i?>')" onblur="quantity_cal(this.value,'materialstable-row<?php echo $i?>')"   placeholder="Quantity" class="form-control"></td>
										  <td width="15%">
										  {!! Form::select('unit[]', $units->prepend('Please Select', ''),  $materials_line->unit, [ 'required', 'class' => 'form-control  ']); !!}
										  </td>
										 
										 <td><input type="number" name="total_quantity[]" value="<?php echo $materials_line->total_quantity?>"   placeholder="Total Quantity" class="form-control total_quantity"></td>
                                        
                                       
										 <td class="mt-10"><button  type="button" class="btn btn-danger" onclick="$('#materialstable-row<?php echo $i?>').remove();"><i class="fa fa-trash"></i> </button></td>
									
                                     </tr>
									 
									  <?php
								 $i++;
								 ?>
							     @endforeach
								@endif
                                     <tr  id="materialstable-row">
                                         <td width="55%">
										  
                                         {!! Form::select('materterial_id[]', $materials->prepend('Please Select', ''),  null, [ 'class' => 'form-control  ']); !!}
         
									
										 </td>
                                         <td><input type="number" name="quantity[]"     onkeyup="quantity_cal(this.value,'materialstable-row')" onblur="quantity_cal(this.value,'materialstable-row')"   placeholder="Quantity" class="form-control"></td>
										  <td width="15%">
										  {!! Form::select('unit[]', $units->prepend('Please Select', ''),  null, ['class' => 'form-control  ']); !!}
										  </td>
										 
										 <td><input type="number" name="total_quantity[]"    placeholder="Total Quantity" class="form-control total_quantity"></td>
                                        
                                         <td class="mt-10"><button  type="button" onclick="addmaterialstable();" class="btn btn-success"><i class="fa fa-plus"></i> </button></td>
                                     </tr>
                                 </tbody>
                             </table>
                         </div>
                        
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>
			<div class="clearfix"></div>
		
        
       
    
       
		
		
		
		
		<div class="col-sm-4">
          <div class="form-group">
		  
            {!! Form::label('assigned_by',  'Assigned By') !!} 
            {!! Form::text('assigned_by', @$project_materials->assigned_by, ['class' => 'form-control',
              'placeholder' => 'Assigned By']); !!}
          </div>
        </div>
		
		
		
        <div class="col-sm-4">
            
                    <label for="assigned_date">Assigned Date</label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
						  <?php
						   if(@$project_materials->assigned_date !='')
						   {
						   $assigned_date = date("m/d/Y", strtotime(@$project_materials->assigned_date) );
						   }
						   ?>
                        <input type="text" id="assigned_date" name="assigned_date" 
						value="{{ !empty($project_materials->assigned_date) ? @$assigned_date: @format_date('now') }}" class="form-control" readonly>
                    </div>
         
        </div>
		
		
	
    
			
			
			
			<div class="clearfix"></div>
			
			
		
		
		    <div class="row" style="margin-top:50px">
    <div class="col-sm-2">
    
      <div class="text-left" style="margin-left:6%">
      <div class="btn-group">
     
       
	   <input type="hidden" value="<?php echo @$project_materials->id?>" name="project_materialsid">
        <button type="submit" value="submit" class="btn btn-primary">Submit</button>
		
		
	
      </div>
      
      </div>
    </div>
	
	
	 <div class="col-sm-2">
    
      <div class="text-left" style="margin-left:2%">
      <div class="btn-group">
   

      
		
		
		<a href="{{url('/manufacturing/orders')}}"   style="margin-left:2%"  class="btn btn-warning">Go Back</a>
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
	
	function quantity_cal(qty,ids)
	{
		//total_quantity
		var order_quantity=$('#order_quantity').val();
		var total_quanties=order_quantity*qty;
		var qty_ids='#'+ids+' td input.total_quantity';
		
		//console.log(qty_ids);
		//alert(qty_ids);
		
		
		$(qty_ids).val(total_quanties);
	}
	
    $(document).ready( function(){
		
	
		//datepicker_date_format);
		$('#assigned_date').datepicker({
            autoclose: true,
            format: datepicker_date_format
        });
        
    });



		var materialstable_row = <?php echo $i+1 ?>;
		function addmaterialstable() {
			
			var material_cols=document.getElementById("materialstable").rows[1].cells[0].innerHTML;
			var unit_cols=document.getElementById("materialstable").rows[1].cells[2].innerHTML;
			
		    html = '<tr id="materialstable-row' + materialstable_row + '">';
			html += '<td>' + material_cols + '</td>';
			html += '<td><input type="number" name="quantity[]" onkeyup="quantity_cal(this.value,\'materialstable-row' + materialstable_row + '\')"  onblur="quantity_cal(this.value,\'materialstable-row' + materialstable_row + '\')"   placeholder="Quantity" class="form-control"></td>';
			
			
			html += '<td>' + unit_cols + '</td>';
			
			html += '<td><input type="number" name="total_quantity[]"    placeholder="Total Quantity" class="form-control total_quantity"></td>';
			
			
			html += '<td class="mt-10"><button class="btn btn-danger" onclick="$(\'#materialstable-row' + materialstable_row + '\').remove();"><i class="fa fa-trash"></i> </button></td>';

			html += '</tr>';

		$('#materialstable tbody').append(html);

		materialstable_row++;
		}
		
		
		function goBack() {
		  window.history.back();
		}
  </script>
@endsection
