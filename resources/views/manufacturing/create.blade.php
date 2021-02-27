<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('ManufacturingController@materialstore'), 'method' => 'post', 'id' => 'materials_add_form' ]) !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">Add Material</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('name', __( 'Material Name' ) . ':*') !!}
          {!! Form::text('material_name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'Name' )]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('code', __( 'Material Code' ) . ':') !!}
          {!! Form::text('material_code', null, ['class' => 'form-control', 'placeholder' => __( 'Code' )]); !!}
          
      </div>
	  
	  <div class="form-group">
        {!! Form::label('short_code','Unit') !!}
         
        {!! Form::select('material_unit', $units, null, ['class' => 'form-control select2', 'required']); !!}

      </div>
	  
	
 

    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->