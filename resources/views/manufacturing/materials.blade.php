@extends('layouts.app')
@section('title', 'Materials')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Manufacturing
        <small>Materials</small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'Materials List' )])
        @can('category.create')
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal" 
                    data-href="{{url('/manufacturing/materialscreate')}}" 
                    data-container=".materials_modal">
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                </div>
            @endslot
        @endcan
        @can('category.view')
            <div class="table-responsive">
			
                <table class="table table-bordered table-striped" id="materials_table">
                    <thead>
                        <tr>
                            <th>Material Name</th>
                            <th>Code</th>
							<th>Unit</th>
							<th>Stock</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
					
					
					
					<tr role="row" class="odd"><td class="sorting_1">Cotton</td>
					<td>cs001</td>
					<td>kg</td>
					<td> 
					 
					   
					   
					   <button type="button" class="badge badge-primary btn-modal" 
                    data-href="{{url('/manufacturing/materialstock')}}" 
                    data-container=".materials_modal">
                   View Stock</button>
                    </td>
					<td> 
					
					<button data-href="#" class="btn btn-xs btn-primary edit_category_button"><i class="glyphicon glyphicon-edit"></i>  Edit</button>
                        &nbsp;
                       <button data-href="#" class="btn btn-xs btn-danger delete_category_button"><i class="glyphicon glyphicon-trash"></i> Delete</button>
                    </td>
					</tr>
					
					<tr role="row" class="odd"><td class="sorting_1">Silk</td><td>cs002</td>
					<td>kg</td>
					<td> 
					   <button type="button" class="badge badge-primary btn-modal" 
                    data-href="{{url('/manufacturing/materialstock')}}" 
                    data-container=".materials_modal">
                   View Stock</button>
                    </td>
					<td>                  
					<button data-href="#" class="btn btn-xs btn-primary edit_category_button"><i class="glyphicon glyphicon-edit"></i>  Edit</button>
                        &nbsp;
                       <button data-href="#" class="btn btn-xs btn-danger delete_category_button"><i class="glyphicon glyphicon-trash"></i> Delete</button>
                    </td>
					</tr>
					
					<tr role="row" class="odd"><td class="sorting_1">Nilon</td><td>cs003</td>
					<td>kg</td>
					<td> 
					   <button type="button" class="badge badge-primary btn-modal" 
                    data-href="{{url('/manufacturing/materialstock')}}" 
                    data-container=".materials_modal">
                   View Stock</button>
                    </td>
					<td>                  
					<button data-href="#" class="btn btn-xs btn-primary edit_category_button"><i class="glyphicon glyphicon-edit"></i>  Edit</button>
                        &nbsp;
                       <button data-href="#" class="btn btn-xs btn-danger delete_category_button"><i class="glyphicon glyphicon-trash"></i> Delete</button>
                    </td>
					</tr>
					
					
                </table>
            </div>
        @endcan
    @endcomponent

    <div class="modal fade materials_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection
