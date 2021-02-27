<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('CategoryController@store'), 'method' => 'post', 'id' => 'category_add_form' ]) !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title"> Material Stock Details</h4>
    </div>

    <div class="modal-body">
      <table class="table table-condensed table-bordered table-th-green text-center table-striped" id="purchase_entry_table">
						<thead>
							<tr>
								<th>#</th>
								<th>Material</th>
								<th>Unit</th>
								<th>Total Stock</th>
								<th>Used </th>
								<th>Current Stock</th>
							</tr>
      </thead>
						<tbody>
						<tr>
								<td>1</td>
								<td>{{$material->material_name}}</td>
								<td>{{$material->material_unit}}</td>
								<td>{{$total_stock}}</td>
								<td>{{$used_stock}}</td>
								<td>{{$current_stock}}</td>
							</tr>
						</tbody>
					</table>					
	  
	
 

    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->