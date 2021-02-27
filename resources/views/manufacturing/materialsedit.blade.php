<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('ManufacturingController@materialupdate', [$material->id]), 'method' => 'POST', 'id' => 'materials_edit_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">Material Edit</h4>
    </div>

    <div class="modal-body">
     <div class="form-group">
        {!! Form::label('material_name', 'Material Name*') !!}
          {!! Form::text('material_name', $material->material_name, ['class' => 'form-control', 'required', 'placeholder' => __( 'category.category_name' )]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('material_code', 'Material Code') !!}
          {!! Form::text('material_code', $material->material_code, ['class' => 'form-control', 'placeholder' => __( 'category.code' )]); !!}
          
      </div>
	  
	  
	    <div class="form-group">
        {!! Form::label('material_unit','Unit') !!}
         
        {!! Form::select('material_unit', $units, $material->material_unit, ['class' => 'form-control select2', 'required']); !!}

      </div>
      
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->