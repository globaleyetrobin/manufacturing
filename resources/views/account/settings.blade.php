@extends('layouts.app')
@section('title','Account  Settings')

@section('content')

<style>
.heading
{
    background: #f7f1f1;
    padding: 5px 10px;
}

</style>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> Account Settings</h1>
    
</section>

<!-- Main content -->
<section class="content">
{!! Form::open(['url' => action('AccountController@AccountSettingsStore'), 'method' => 'post', 
    'id' => 'product_add_form','class' => 'product_form', 'files' => true ]) !!}
    @component('components.widget', ['class' => 'box-primary'])
        <div class="row">
       
     
		
		<div class="clearfix"></div>
		
		
     
	 <div class="page-content page-container" id="page-content">
     <div class="padding">
         <div class="row container d-flex justify-content-center">
             <div class="col-lg-12 grid-margin stretch-card">
                 <div class="card">
                     <div class="card-body">
                         
                         <div class="table-responsive">
						 
						 <h3 class="heading"> Capital Account </h3>
						 
                             <table id="capital_account" class="table table-hover">
                                 <thead>
                                     <tr>
                                         <th>Name</th>
                                         <th>Amount</th>
								         <th>Payment Date</th>
                                         
                                     </tr>
                                 </thead>
                                 <tbody>
								 <?php
								 $i=0;
								 ?>
								
                                     <tr  id="capitalaccount-row">
                                         <td width="55%">
										
									       <input type="text" name="capital[0][capital_name]"    placeholder="Capital Account" class="form-control ">
										 </td>
                                        
										 
										 <td><input type="number" name="capital[0][capital_amount]"    placeholder="Capital Amount" class="form-control "></td>
										 
								          <td><input type="date" name="capital[0][paymentdate]"  id=""  class="form-control" ></td>
								 
								
                                    
                                         <td class="mt-10"><button  type="button" onclick="addcapital();" class="btn btn-success"><i class="fa fa-plus"></i> </button></td>
                                     </tr>
                                 </tbody>
                             </table>
                        



                       	 <h3 class="heading"> Fixed Asset </h3>
						 
                             <table id="fixed_asset" class="table table-hover">
                                 <thead>
                                     <tr>
                                         <th>Name</th>
                                         <th>Account</th>
								        <th>Payment Date</th>
                                         
                                     </tr>
                                 </thead>
                                 <tbody>
								 <?php
								 $j=0;
								 ?>
								
                                     <tr  id="fixedasset-row">
                                         <td width="55%">
										
									       <input type="number" name="asset[0][asset_name]"    placeholder="Fixed Asset" class="form-control ">
										 </td>
                                        
										 
										 <td><input type="number" name="asset[0][asset_amount]"    placeholder="Fixed Asset Amount" class="form-control "></td>
										 <td><input type="date" name="asset[0][paymentdate]"  id=""  class="form-control" ></td>
                                        
                                         <td class="mt-10"><button  type="button" onclick="addfixedasset();" class="btn btn-success"><i class="fa fa-plus"></i> </button></td>
                                     </tr>
                                 </tbody>
                             </table>
							 
							 
							 
							 
							  	 <h3 class="heading"> Loan </h3>
						 
                             <table id="loan" class="table table-hover">
                                 <thead>
                                     <tr>
                                         <th>Loan Name</th>
                                         <th>Account</th>
								        <th>Payment Date</th>
                                         
                                     </tr>
                                 </thead>
                                 <tbody>
								 <?php
								 $k=0;
								 ?>
								
                                     <tr  id="Loan-row">
                                         <td width="55%">
										
									       <input type="number" name="loan[0][loan_name]"    placeholder="Loan Name" class="form-control ">
										 </td>
                                        
										 
										 <td><input type="number" name="loan[0][loan_amount]"    placeholder="Loan Amount" class="form-control "></td>
										 <td><input type="date" name="loan[0][paymentdate]"  id=""  class="form-control" ></td>
                                        
                                         <td class="mt-10"><button  type="button" onclick="addloan();" class="btn btn-success"><i class="fa fa-plus"></i> </button></td>
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
		
        
       
    
       
		
		
		
	
		
		
		
       
		
	
    
			
			
			
			<div class="clearfix"></div>
			
			
		
		
		    <div class="row" style="margin-top:50px">
    <div class="col-sm-2">
    
      <div class="text-left" style="margin-left:6%">
      <div class="btn-group">
     
       
	
        <button type="submit" value="submit" class="btn btn-primary">Submit</button>
		
		
	
      </div>
      
      </div>
    </div>
	
	
	 <div class="col-sm-2">
    
      <div class="text-left" style="margin-left:2%">
      <div class="btn-group">
   

      
		
		
		<a href="{{url('/home')}}"   style="margin-left:2%"  class="btn btn-warning">Go Back</a>
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
	
	
	/*function quantity_cal(qty,ids)
	{
		//total_quantity
		var order_quantity=$('#order_quantity').val();
		var total_quanties=order_quantity*qty;
		var qty_ids='#'+ids+' td input.total_quantity';
		
		//console.log(qty_ids);
		//alert(qty_ids);
		
		
		$(qty_ids).val(total_quanties);
	}
	*/
    $(document).ready( function(){
		
	
		//datepicker_date_format);
		$('.paymentdate').datepicker({
            autoclose: true,
            format: datepicker_date_format
        });
        
    });



		var capital_row = <?php echo $i+1 ?>;
		function addcapital() {
			
			
			//var unit_cols=document.getElementById("materialstable").rows[1].cells[2].innerHTML;
			
		    html = '<tr id="capitalaccount-row' + capital_row + '">';
			
			html += '<td><input type="text" name="capital['+capital_row+'][capital_name]"    placeholder="Capital Account" class="form-control "></td>';
			
			
		
			html += '<td><input type="number" name="capital['+capital_row+'][capital_amount]"    placeholder="Capital Amount" class="form-control "></td>';
			
			html += '<td><input type="date" name="capital['+capital_row+'][paymentdate]"  id=""  class=" form-control" ></td>';
			
		
			
			
			html += '<td class="mt-10"><button class="btn btn-danger" onclick="$(\'#capitalaccount-row' + capital_row + '\').remove();"><i class="fa fa-trash"></i> </button></td>';

			html += '</tr>';

		$('#capital_account tbody').append(html);

		capital_row++;
		}
		
		
		
		
		
	  var asset_row = <?php echo $j+1 ?>;
		function addfixedasset() {
			
			
			//var unit_cols=document.getElementById("materialstable").rows[1].cells[2].innerHTML;
			
		    html = '<tr id="fixedasset-row' + asset_row + '">';
			
			html += '<td><input type="text" name="asset[' + asset_row + '][asset_name]"    placeholder="Fixed Asset " class="form-control "></td>';
			
			
		
			html += '<td><input type="number" name="asset[' + asset_row + '][asset_amount]"    placeholder="Fixed Asset Amount" class="form-control "></td>';
			
			html += '<td><input type="date" name="asset['+capital_row+'][paymentdate]"  id=""  class=" form-control" ></td>';
			
			
			html += '<td class="mt-10"><button class="btn btn-danger" onclick="$(\'#fixedasset-row' + asset_row + '\').remove();"><i class="fa fa-trash"></i> </button></td>';

			html += '</tr>';

		$('#fixed_asset  tbody').append(html);

		asset_row++;
		}
		
		
		
		  var loan_row = <?php echo$k+1 ?>;
		function addloan() {
			
			
		
			
		    html = '<tr id="loan-row' + loan_row + '">';
			
			html += '<td><input type="text" name="loan[' + loan_row + '][loan_name]"    placeholder="loan " class="form-control "></td>';
			
			
		
			html += '<td><input type="number" name="loan[' + loan_row + '][loan_amount]"    placeholder="loan Amount" class="form-control "></td>';
			
			html += '<td><input type="date" name="loan['+capital_row+'][paymentdate]"  id=""  class=" form-control" ></td>';
			
			html += '<td class="mt-10"><button class="btn btn-danger" onclick="$(\'#loan-row' + loan_row + '\').remove();"><i class="fa fa-trash"></i> </button></td>';

			html += '</tr>';

		$('#loan  tbody').append(html);

		loan_row++;
		}
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		function goBack() {
		  window.history.back();
		}
  </script>
@endsection
