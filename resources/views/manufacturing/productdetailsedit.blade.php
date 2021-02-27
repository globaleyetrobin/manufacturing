<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('ManufacturingController@productdetailsupdate', [$productdetail->id]), 'method' => 'POST', 'id' => 'materials_edit_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">Manufactured Production Details Edit</h4>
    </div>

    <div class="modal-body">
     <div class="form-group">
        {!! Form::label('quantity', 'Quantity') !!}
          {!! Form::text('quantity', $productdetail->quantity, ['class' => 'form-control', 'required', 'placeholder' => 'Quantity']); !!}
      </div>
	  <div class="form-group">
        {!! Form::label('damage_quantity', 'Damage Quantity') !!}
          {!! Form::text('damage_quantity', $productdetail->damage_quantity, ['class' => 'form-control', 'required', 'placeholder' => 'Damage Quantity']); !!}
      </div>
	  	 <?php
				 if($productdetail->manuf_date !='')
				 {
				 $manuf_date = date("m/d/Y", strtotime($productdetail->manuf_date) );
				 }
		?>
						   
	      <div class="form-group">
        {!! Form::label('manuf_date', 'Manufacture Date') !!}
         <input type="text" name="manuf_date" id="manuf_date" value="<?php echo @$manuf_date; ?>" class="form-control" readonly>
          
      </div>

      <div class="form-group">
        {!! Form::label('remarks', 'Remarks') !!}
          {!! Form::text('remarks', $productdetail->remarks, ['class' => 'form-control', 'placeholder' => 'Remarks']); !!}
          
      </div>
	  
	  
	  
      
    </div>

    <div class="modal-footer">
	 <input type="hidden" value="<?php echo $productdetail->order_id?>" name="order_id">
      <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->



@section('javascript')
  @php $asset_v = env('APP_VERSION'); @endphp

	<script type="text/javascript">
    $(document).ready( function(){
		
		
        //Date picker
        $('#manuf_date').datepicker({
            autoclose: true,
            format: datepicker_date_format
        });

        
    });

  </script>
@endsection