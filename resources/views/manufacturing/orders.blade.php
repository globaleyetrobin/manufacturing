@extends('layouts.app')
@section('title', 'Manufacturing Orders')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Manufacturing
        <small>Orders</small>
    </h1>
   
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'Manufacturing Orders' )])
        @can('category.create')
            @slot('tool')
                <div class="box-tools">
                    <a type="button" class="btn btn-block btn-primary" 
                   href="{{url('/manufacturing/createorder')}}" 
                    >
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</a>
                </div>
            @endslot
        @endcan
        @can('category.Details')
            <div class="table-responsive">
			
                <table class="table table-bordered table-striped" id="manufactureorder_table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>No of Items</th>
							<th>Completed</th>
							<th>Pending</th>
							<th>Priority</th>
							<th>Start Date</th>
							<th>Delivery On</th>
							
							
							
							
							<th>Status</th>
							<th>Materials</th>
							<th>Manufactured</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
					
					
					
					<tr role="row" class="odd">
					<td class="sorting_1">Formal Shirt - Blue Lines </td>
					<td>500</td>
					<td>0</td>
					<td>500</td>
					<td>15-02-2021</td>
					<td>15-03-2021</td>
					
					
				
					
					<td> 
					 
					   
					   
					   <span  class="label label-warning">
                   Low</span>
                    </td>
					<td> Not Started </td>
						<td> <a href="{{url('/manufacturing/assignmaterials')}}" class="btn btn-xs btn-success" >Assign</a> </td>
					
					
					
						<td> <a href="{{url('/manufacturing/productiondetails')}}" class="btn btn-xs btn-success" >Details</a> </td>
					<td> 
					
					<button data-href="#" class="btn btn-xs btn-primary edit_category_button"><i class="glyphicon glyphicon-edit"></i>  Edit</button>
                        &nbsp;
                       <button data-href="#" class="btn btn-xs btn-danger delete_category_button"><i class="glyphicon glyphicon-trash"></i> Delete</button>
                    </td>
					</tr>
					
						<tr role="row" class="odd">
					<td class="sorting_1">Round neck t shirt </td>
					<td>500</td>
					<td>0</td>
					<td>500</td>
					<td>15-02-2021</td>
					<td>15-03-2021</td>
					
					
					<td> 
					 
					   
					   
					   <span  class="label label-danger">
                   High</span>
                    </td>
					<td> Not Started </td>
					
						<td> <a href="{{url('/manufacturing/assignmaterials')}}" class="btn btn-xs btn-success" >Assign</a> </td>
						
						<td> <a href="{{url('/manufacturing/productiondetails')}}" class="btn btn-xs btn-success" >Details</a> </td>
						
					<td> 
					
					<button data-href="#" class="btn btn-xs btn-primary edit_category_button"><i class="glyphicon glyphicon-edit"></i>  Edit</button>
                        &nbsp;
                       <button data-href="#" class="btn btn-xs btn-danger delete_category_button"><i class="glyphicon glyphicon-trash"></i> Delete</button>
                    </td>
					</tr>
					
					
					
						<tr role="row" class="odd">
					<td class="sorting_1">Jeans Pants </td>
					<td>500</td>
					<td>0</td>
					<td>500</td>
					<td>15-02-2021</td>
					<td>15-03-2021</td>
			
					
					<td> 
					 
					   
					   
					   <span  class="label label-primary">
                   Medium</span>
                    </td>
					<td> Not Started </td>
					
						<td> <a href="{{url('/manufacturing/assignmaterials')}}" class="btn btn-xs btn-success" >Assign</a> </td>
						
						<td> <a href="{{url('/manufacturing/productiondetails')}}" class="btn btn-xs btn-success" >Details</a> </td>
						
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
