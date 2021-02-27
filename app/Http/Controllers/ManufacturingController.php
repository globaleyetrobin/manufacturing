<?php

namespace App\Http\Controllers;


use URL;



use App\AccountTransaction;
use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\CustomerGroup;
use App\Product;
use App\PurchaseLine;
use App\TaxRate;
use App\Transaction;
use App\User;

use App\Unit;
use App\Category;
use App\Brands;

use App\Materials;

use App\Manufacturingorder;
use App\Productmaterial;
use App\Productmaterialsline;

use App\Manufactureproduct;


use App\Utils\BusinessUtil;

use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;

use App\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ManufacturingController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $transactionUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;

        $this->dummyPaymentLine = ['method' => 'cash', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'bank_account_number' => '',
        'is_return' => 0, 'transaction_no' => ''];
    }


       public function materialslist()
    {
        if (!auth()->user()->can('category.view') && !auth()->user()->can('category.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

         /*   $materials = Materials::where('business_id', $business_id)
                        ->select(['material_name', 'material_code', 'material_unit','created_by','id']);
         */
		 
		 
		  $materials = Materials::leftJoin('units AS u', 'materials.material_unit', '=', 'u.id')
		 
		   ->select(['materials.material_name', 'materials.material_code', 'u.actual_name as material_unit','materials.created_by','materials.id']);
		   
            return Datatables::of( $materials)
			  
                ->addColumn(
                    'action',
                    '@can("category.update")
                    <button data-href="{{action(\'ManufacturingController@materialedit\', [$id])}}"  class="btn btn-xs btn-primary edit_materials_button"><i class="glyphicon glyphicon-edit"></i>@lang("messages.edit")</button>
                        &nbsp;
                    @endcan
                    @can("category.delete")
                        <button data-href="{{action(\'ManufacturingController@materialdelete\', [$id])}}" class="btn btn-xs btn-danger delete_materials_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    @endcan'
                )
				->editColumn('created_by', function ($row) {
					
				
                  $url=URL::to('/manufacturing/materialstock/'.$row->id);
				  return  ' <button type="button" class="badge badge-primary btn-modal" 
                    
					
					data-href="'.$url.'"
					
                    data-container=".materials_modal">
                   View Stock</button>';
				  
					
					 
                })
				/* ->editColumn(
                    'created_by',
                    '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="aaaaa">aaaaaaaa</span>'
                )
                */
				
				        ->rawColumns(['created_by', 'action'])
                ->removeColumn('id')
               ->escapeColumns(['operations']) 
                //->rawColumns([3])
				
                ->make(false);
        }

        return view('manufacturing.materialslist');
    }

    public function materials()
	{
		/*$id=1;
	 $link='<button data-href="'.URL::to("/manufacturing/materialstock/$id").'" class="btn btn-xs btn-primary edit_materials_button"><i class="glyphicon glyphicon-edit"></i> 
	 100 @lang("messages.edit")</button>';
                       
		echo $link;
        exit;		
		*/
		$business_id = request()->session()->get('user.business_id');
		$business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers = Contact::suppliersDropdown($business_id, false);
        $orderStatuses = $this->productUtil->orderStatuses();
		
		
		$materials = Materials::forDropdown($business_id);
		
		

        return view('manufacturing.materials')
            ->with(compact('business_locations', 'suppliers', 'orderStatuses'));
			
			
	 
	}
	
	
	
	
	    public function materialscreate()
    {
        if (!auth()->user()->can('category.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
                       
		$units = Unit::forDropdown($business_id, true);				
						
    
	  
        return view('manufacturing.create')
                    ->with(compact('units'));
    }
	
     public function materialedit($id)
	 {
		 
		
		  if (!auth()->user()->can('category.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $material = Materials::where('business_id', $business_id)->find($id);
            
            $parent_categories = Category::where('business_id', $business_id)
                                        ->where('parent_id', 0)
                                        ->where('id', '!=', $id)
                                        ->pluck('name', 'id');
            
           $units = Unit::forDropdown($business_id, true);
            
       

            return view('manufacturing.materialsedit')
                ->with(compact('material','units'));
        }
	 }
	 
   
	 
    public function materialstore(Request $request)
	{
		        $input = $request->only(['material_name', 'material_code','material_unit']);
                $business_id = $request->session()->get('user.business_id');

                 $input['business_id'] = $request->session()->get('user.business_id');
                 $input['created_by'] = $request->session()->get('user.id');

                $materials = Materials::create($input);
				
				
				  $output = ['success' => 1,
                            'msg' => __("Materials added successfully")
                        ];
        
        return redirect('manufacturing/materials')->with('status', $output);
       // return redirect()->back()->with(['status' => $output]);
    
	}
	
     public function materialupdate(Request $request, $id)
	 {
		 
		   
		 
		        $input = $request->only(['material_name', 'material_code','material_unit']);
                $business_id = $request->session()->get('user.business_id');

                $materials = Materials::where('business_id', $business_id)->findOrFail($id);
                $materials->material_name = $input['material_name'];
                $materials->material_code = $input['material_code'];
                $materials->material_unit = $input['material_unit'];
                $materials->save();

                $output = ['success' => true,
                            'msg' => __("Materials updated successfully")
                            ];
							
			   return redirect('manufacturing/materials')->with('status', $output);				
	 }
	 
	 
	     public function materialdelete($id)
    {
		
		
		     if (!auth()->user()->can('category.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                $materials = Materials::where('business_id', $business_id)->findOrFail($id);
                $materials->delete();

                $output = ['success' => true,
                            'msg' => __("Materials deleted successfully")
                            ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
		
       
    }
	 
	 
	 
	 
	 /* Manufacturing Order Save */
	 
	 
	 
	   public function orderstore(Request $request)
	{
		        $input =   $request->only(['title',
											'category_id',
											'order_no',
											'description',
											'photo',
											'quantity',
											'incharge',
											'priority',
											'start_date',
											'end_date',
											'status']);
                $business_id = $request->session()->get('user.business_id');
				
			//	echo date("Y-m-d", strtotime($var) );
			
			     $start_date=$request->post('start_date');
				 
				 $end_date=$request->post('end_date');
             		 
                 $input['start_date'] = date("Y-m-d", strtotime($start_date) );
                 $input['end_date'] = date("Y-m-d", strtotime($end_date) );
			
			
			    
                 $input['business_id'] = $request->session()->get('user.business_id');
                 $input['created_by'] = $request->session()->get('user.id');

                $materials = Manufacturingorder::create($input);
				
				
				  $output = ['success' => 1,
                            'msg' => __("Manufacturing order added successfully")
                        ];
        
        return redirect('manufacturing/orders')->with('status', $output);
       // return redirect()->back()->with(['status' => $output]);
    
	}
	 
	 
	 
	 
       public function orderlist()
    {
        if (!auth()->user()->can('category.view') && !auth()->user()->can('category.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

         /*   $materials = Materials::where('business_id', $business_id)
                        ->select(['material_name', 'material_code', 'material_unit','created_by','id']);
         */
		 //  $materials = Materials::leftJoin('units AS u', 'materials.material_unit', '=', 'u.id')
		 // manufacture_product
		  $materials = Manufacturingorder::where('business_id', $business_id)->
		  select(['manufacturingorders.id', 'manufacturingorders.title', 'manufacturingorders.quantity as quantity ', DB::raw("(SELECT SUM(quantity)  
			  FROM manufacture_product WHERE manufacture_product.order_id=manufacturingorders.id)
		  as finished_product"), DB::raw("(quantity) -  (SELECT SUM(quantity) 
			  FROM manufacture_product WHERE manufacture_product.order_id=manufacturingorders.id)
		  as pending_product"),'manufacturingorders.priority','manufacturingorders.start_date','manufacturingorders.end_date','manufacturingorders.status',
		  'manufacturingorders.business_id','manufacturingorders.created_by' ]);
		   
            return Datatables::of( $materials)
			 
			
                ->addColumn(
                    'action',
                    '@can("category.update")
                    <a href="{{action(\'ManufacturingController@ordersedit\', [$id])}}"  class="btn btn-xs btn-primary "><i class="glyphicon glyphicon-edit"></i>@lang("messages.edit")</a>
                        &nbsp;
                    @endcan
                    @can("category.delete")
                        <button data-href="{{action(\'ManufacturingController@ordersdelete\', [$id])}}" class="btn btn-xs btn-danger delete_orders_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    @endcan'
                )
				->editColumn('business_id', function ($row) {
					
				
                  $url=URL::to('/manufacturing/assignmaterials/'.$row->id);
				  return  ' <a  class="btn btn-xs btn-success" 
                    
					
					href="'.$url.'"
					
                    >
                   Assign</a>';
				  
					
					 
                })
				->editColumn('created_by', function ($row) {
					
				
                  $url=URL::to('/manufacturing/productiondetails/'.$row->id);
				  return  ' <a  class="badge badge-primary " 
                    
					
					href="'.$url.'"
					
                    >
                Details</a>';
				  
					
					 
                })
				/* ->editColumn(
                    'created_by',
                    '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="aaaaa">aaaaaaaa</span>'
                )
                */
				
				 ->rawColumns(['finished','pending','created_by', 'action'])
                ->removeColumn('id')
               ->escapeColumns(['operations']) 
                //->rawColumns([3])
				
                ->make(false);
        }

        return view('manufacturing.orderlist');
    }

	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 /* End Manufacturing Order Save , Edit, Delete */
	 
	 
	 
	 

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */



   /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function materialpurchasecreate()
    {
        if (!auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }

        $taxes = TaxRate::where('business_id', $business_id)
                            ->get();
        $orderStatuses = $this->productUtil->orderStatuses();
        $business_locations = BusinessLocation::forDropdown($business_id);

        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $default_purchase_status = null;
        if (request()->session()->get('business.enable_purchase_status') != 1) {
            $default_purchase_status = 'received';
        }

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }
        $customer_groups = CustomerGroup::forDropdown($business_id);

        $business_details = $this->businessUtil->getDetails($business_id);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        $payment_line = $this->dummyPaymentLine;
        $payment_types = $this->productUtil->payment_types();

        //Accounts
        $accounts = $this->moduleUtil->accountsDropdown($business_id, true);

        return view('manufacturing.materialpurchasecreate')
            ->with(compact('taxes', 'orderStatuses', 'business_locations', 'currency_details', 'default_purchase_status', 'customer_groups', 'types', 'shortcuts', 'payment_line', 'payment_types', 'accounts'));
    }

  


        public function getPurchaseEntryRow(Request $request)
    {
		
	
        if (request()->ajax()) {
            $product_id = $request->input('product_id');
            $variation_id = $request->input('variation_id');
            $business_id = request()->session()->get('user.business_id');

            $hide_tax = 'hide';
            if ($request->session()->get('business.enable_inline_tax') == 1) {
                $hide_tax = '';
            }

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            if (!empty($product_id)) {
                $row_count = $request->input('row_count');
                $product = Product::where('id', $product_id)
                                    ->with(['unit'])
                                    ->first();

                $sub_units = $this->productUtil->getSubUnits($business_id, $product->unit->id);

                $query = Variation::where('product_id', $product_id)
                                        ->with(['product_variation']);
                if ($variation_id !== '0') {
                    $query->where('id', $variation_id);
                }

                $variations =  $query->get();
                
                $taxes = TaxRate::where('business_id', $business_id)
                            ->get();

                return view('manufacturing.purchase_entry_row')
                    ->with(compact(
                        'product',
                        'variations',
                        'row_count',
                        'variation_id',
                        'taxes',
                        'currency_details',
                        'hide_tax',
                        'sub_units'
                    ));
            }
        }
    }



    public function materialstock($id)
    {
		
		  if (!auth()->user()->can('category.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        //$material = Materials::where('business_id', $business_id)->find($id);
		
		$material = Materials::leftJoin('units AS u', 'materials.material_unit', '=', 'u.id')
		 
		   ->select(['materials.material_name', 'materials.material_code', 'u.actual_name as material_unit'])->find($id);
		   		  		  		  
						
         $material_stocks = DB::table('purchase_lines')->where('product_id', '=', $id)
		            
						->select( DB::raw('SUM(quantity) as total_stock'))
						->first(); 
						  
		$total_stock=$material_stocks->total_stock;				
						  
						  
		 $used_materials = DB::table('productmaterials_line')->where('materterial_id', '=', $id)
		            
						->select( DB::raw('SUM(total_quantity) as used_stock'))
						->first(); 
		  $used_stock=$used_materials->used_stock;		

          $current_stock=$total_stock-$used_stock; 		  
			 
						
        return view('manufacturing.stocks')
                    ->with(compact('total_stock','used_stock','current_stock','material'));
    }
	
    
	public function orders()
	{
		return view('manufacturing.orders');
	}
	
	public function createorder()
	{
		 $business_id = request()->session()->get('user.business_id');
		 $categories = Category::where('business_id', $business_id)
                            ->where('parent_id', 0)
                            ->pluck('name', 'id');
							
							
		return view('manufacturing.createorder')->with(compact('categories'));
	}
	
	
		public function ordersedit($id)
	{
		 $business_id = request()->session()->get('user.business_id');
		 $categories = Category::where('business_id', $business_id)
                            ->where('parent_id', 0)
                            ->pluck('name', 'id');
		 $order = Manufacturingorder::where('business_id', $business_id)->findOrFail($id);					
							
		return view('manufacturing.editorder')->with(compact('categories','order'));
	}
	
	   public function ordersdelete($id)
   {
	   		
		     if (!auth()->user()->can('category.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                //$manufactureproduct = Manufactureproduct::findOrFail($id);
               // $manufactureproduct->delete();
			   
			    // DB::table('manufacture_product')->where('id', $id)->delete();
                $orders = Manufacturingorder::where('business_id', $business_id)->findOrFail($id);
                $orders->delete();

                $output = ['success' => true,
                            'msg' => __("Manufacturing order  deleted successfully")
                            ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
		
   }
	
	public function orderupdate(Request $request, $id)
	{
	
	  $input =   $request->only(['title',
											'category_id',
											'order_no',
											'description',
											'photo',
											'quantity',
											'incharge',
											'priority',
											'start_date',
											'end_date',
											'status']);
                $business_id = $request->session()->get('user.business_id');
				
			//	echo date("Y-m-d", strtotime($var) );
			
			     $start_date=$request->post('start_date');
				 
				 $end_date=$request->post('end_date');
             		 
                 $input['start_date'] = date("Y-m-d", strtotime($start_date) );
                 $input['end_date'] = date("Y-m-d", strtotime($end_date) );
			
			
			    
                 $input['business_id'] = $request->session()->get('user.business_id');
                 $input['created_by'] = $request->session()->get('user.id');

                $orders = Manufacturingorder::where('business_id', $business_id)->findOrFail($id);
				
                $orders->title = $input['title'];
				
				$orders->category_id = $input['category_id'];
				
				$orders->order_no = $input['order_no'];
				
				$orders->description = $input['description'];
				
				$orders->quantity = $input['quantity'];
				
				
				$orders->incharge = $input['incharge'];
				
				$orders->priority = $input['priority'];
				
				$orders->start_date = $input['start_date'];
				
				$orders->end_date = $input['end_date'];
				
				$orders->status = $input['status'];
                
				
                $orders->save();
               
				
				
				  $output = ['success' => 1,
                            'msg' => __("Manufacturing order updated successfully ")
                        ];
        
        return redirect('manufacturing/orders')->with('status', $output);
	
}
	public function assignmaterials($id)
	{
		if (!auth()->user()->can('category.create')) {
            abort(403, 'Unauthorized action.');
        }

         $business_id = request()->session()->get('user.business_id');
		 
		 

         $order = Manufacturingorder::where('business_id', $business_id)->findOrFail($id);
		 
		
				
				
		 $materials = Materials::where('business_id', $business_id)
                            
							->whereNull('deleted_at')
                            ->pluck('material_name', 'id');
							
		$units = Unit::where('business_id', $business_id)
                            
							->whereNull('deleted_at')
                            ->pluck('short_name', 'id'); 	

       $materials_lines = Productmaterialsline::where('order_id', $id)
				   ->whereNull('deleted_at')
				  ->select(['id', 'mat_assignid', 'order_id', 'materterial_id', 'quantity', 'unit', 'total_quantity'])->get();
				  
		$project_materials = Productmaterial::where('order_id', $id)
				   ->whereNull('deleted_at')
				  ->select(['id', 'order_id','assigned_by','assigned_date'])->first();		  
					
									
					   		
	   	
							
		return view('manufacturing.assignmaterials')->with(compact('materials','units','order','materials_lines','project_materials'));
	}
	
	
	public function materialassignstore(Request $request)
	{
		
		   $input =   $request->only(['order_id',
											'assigned_by'
											]);
											
			 $assigned_date=$request->post('assigned_date');
			$order_id=$request->post('order_id');								
                $business_id = $request->session()->get('user.business_id');
				
				
				   
					
			
			
			    
				 
             		 
                 $input['assigned_date'] = date("Y-m-d", strtotime($assigned_date) );
                
			
			      $materterial_ids=$request->post('materterial_id');
				  
				  $quantitys=$request->post('quantity');
				   
				  $units=$request->post('unit');
					
				  $total_quantitys=$request->post('total_quantity');
				  
			
				  $count=@count($materterial_ids);
				  
				  $project_materialsid=$request->post('project_materialsid');
                 if($project_materialsid !='')
				 {
                $Productmaterial = Productmaterial::findOrFail($project_materialsid);
                $Productmaterial->assigned_by = $input['assigned_by'];
                $Productmaterial->assigned_date = $input['assigned_date'];
               
                $Productmaterial->save();
                $mat_assignid=$project_materialsid;

				//$materials_delete = Productmaterialsline::where('mat_assignid', $project_materialsid);
				//$materials_delete->delete();
				
				DB::table('productmaterials_line')->where('mat_assignid', $project_materialsid)->delete();


								
				 }
                 else
                 {					 
								
				  $materials = Productmaterial::create($input);
				  $mat_assignid=$materials->id;
				 }
				
				
				
				//  foreach($materterial_ids as $materterial_id)  project_materialsid
				for($i=0;$i<$count; $i++)
				  {
					  if($materterial_ids[$i] !='')
					  {
					  $data['mat_assignid']=$mat_assignid;
					  $data['order_id']=$order_id;
					  $data['materterial_id']=$materterial_ids[$i];
					  $data['quantity']=$quantitys[$i];
					  $data['unit']=$units[$i];
					  $data['total_quantity']=$total_quantitys[$i];
					  
					  $materials_line = Productmaterialsline::create($data);
					  }
					  
				  }
			
               
				
				  $output = ['success' => 1,
                            'msg' => __(" Materials Assigned successfully")
                        ];
        
        return redirect('manufacturing/assignmaterials/'.$order_id)->with('status', $output);
	}
	
	
	
	
	public function productiondetails($id)
	{
		if (!auth()->user()->can('category.create')) {
            abort(403, 'Unauthorized action.');
        }

         $business_id = request()->session()->get('user.business_id');
		 
		 
    
         $order = Manufacturingorder::where('business_id', $business_id)->findOrFail($id);
		 
		 
		  $productions= DB::table('manufacture_product')->where('order_id', '=', $id)
		            
						->select( DB::raw('SUM(quantity) as finished'), DB::raw('SUM(damage_quantity) as damage_quantity'))
						->first(); 
          $finished_production=$productions->finished;	
		  
		  $damage_quantity=$productions->damage_quantity;	
		  
	
        $categories = Category::where('business_id', $business_id)
                            ->where('parent_id', 0)
                            ->pluck('name', 'id');
        $brands = Brands::where('business_id', $business_id)
                            ->pluck('name', 'id');
        $units = Unit::forDropdown($business_id, true);
		 
		return view('manufacturing.productiondetails')->with(compact('order','finished_production','categories','brands','units','damage_quantity'));
	}
	
	
	public function productdetailsstore(Request $request)
	{
		   $input =   $request->only(['quantity',
											'manuf_date','remarks','order_id','damage_quantity']);
					
											
			$order_id=$request->post('order_id');	
			$manuf_date	= 	$request->post('manuf_date');		
		    $input['manuf_date'] = date("Y-m-d", strtotime($manuf_date) );
			$products = Manufactureproduct::create($input);
		
		    $products_id=$products->id;		
             $output = ['success' => 1,
                            'msg' => __(" Materials Assigned successfully")
                        ];
        
        return redirect('manufacturing/productiondetails/'.$order_id)->with('status', $output);				  
	}
	
	public function productdetailslist()
	{
		
		
		
		        if (!auth()->user()->can('category.view') && !auth()->user()->can('category.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

      
		 //manufacture_product 
		 
		  $productlists = Manufactureproduct::select(['quantity', 'damage_quantity','manuf_date', 'remarks','id']);
		   
            return Datatables::of( $productlists)
			  
                ->addColumn(
                    'action',
                    '@can("category.update")
                    <button data-href="{{action(\'ManufacturingController@productdetailsedit\', [$id])}}"  class="btn btn-xs btn-primary edit_productdetails_button"><i class="glyphicon glyphicon-edit"></i>@lang("messages.edit")</button>
                        &nbsp;
                    @endcan
                    @can("category.delete")
                        <button data-href="{{action(\'ManufacturingController@productdetailsdelete\', [$id])}}" class="btn btn-xs btn-danger delete_productdetails_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    @endcan'
                )
				
				/* ->editColumn(
                    'created_by',
                    '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="aaaaa">aaaaaaaa</span>'
                )
                */
				
				        ->rawColumns(['created_by', 'action'])
                ->removeColumn('id')
               ->escapeColumns(['operations']) 
                //->rawColumns([3])
				
                ->make(false);
        }

        return view('manufacturing.productdetailslist');
	}
	
	
	

   public function productdetailsedit($id)
   {
	   
	   	if (!auth()->user()->can('category.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $productdetail = Manufactureproduct::find($id);
            
        
            
       

            return view('manufacturing.productdetailsedit')
                ->with(compact('productdetail'));
        }
   }
   
   public function productdetailsupdate(Request $request, $id)
   {
	           $input = $request->only(['quantity','manuf_date','remarks','order_id','damage_quantity']);
                $business_id = $request->session()->get('user.business_id');
				
				$order_id=$request->post('order_id');	
				$manuf_date	= 	$request->post('manuf_date');		
		         $input['manuf_date'] = date("Y-m-d", strtotime($manuf_date) );
		         $input['manuf_date'] = date("Y-m-d", strtotime($manuf_date) );

                $manufactureproduct = Manufactureproduct::findOrFail($id);
                $manufactureproduct->quantity = $input['quantity'];
				$manufactureproduct->damage_quantity = $input['damage_quantity'];
				
                $manufactureproduct->manuf_date = $input['manuf_date'];
                $manufactureproduct->remarks = $input['remarks'];
                $manufactureproduct->save();

                $output = ['success' => true,
                            'msg' => __("Manufactured Production Details Upddated Successfully ")
                            ];
							
			   return redirect('manufacturing/productiondetails/'.$order_id)->with('status', $output);	
   }
   
   public function productdetailsdelete($id)
   {
	   		
		     if (!auth()->user()->can('category.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                //$manufactureproduct = Manufactureproduct::findOrFail($id);
               // $manufactureproduct->delete();
			   
			   DB::table('manufacture_product')->where('id', $id)->delete();


                $output = ['success' => true,
                            'msg' => __("Manufactured Production Details deleted successfully")
                            ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
		
   }
   
   
   


   public function savetopos(Request $request)
   {
	   

   // product_name  product_sku  category_id  brand_id  product_quantity product_price
	 $business_id = request()->session()->get('user.business_id');
	$product_name=$request->post('product_name');	
	$product_sku=$request->post('product_sku');	
	$category_id=$request->post('category_id');	
	$brand_id=$request->post('brand_id');	
	$product_quantity=$request->post('product_quantity');	
	$product_price=$request->post('product_price');	
	
	$order_id=$request->post('order_id');	
	$user_id = $request->session()->get('user.id');
	   
	  $product_id=DB::table('products')->insertGetId([
						'name' => $product_name,
						'business_id' =>$business_id,
						'type' => 'style',
						'unit_id' => 1,
						'brand_id' => $brand_id,
						'category_id' => $category_id,
						'tax_type' => 'exclusive',
						'enable_stock' => 1,
						'alert_quantity' => 0,
						'sku' => $product_sku,
						
						'barcode' => 'null',
						'barcode_type' => 'C128',
						
						'created_by' =>  $user_id,
						'is_inactive' =>  0
					
					]); 
					
		$product_variations_id=DB::table('product_variations')->insertGetId([
						'variation_template_id' => null,
						'name' =>'DUMMY',
						'product_id' => $product_id,
						'is_dummy' => 1
					
					]); 	

        $variations_id=DB::table('variations')->insertGetId([
						'name' => 'DUMMY',
						'product_id' =>$product_id,
						'sub_sku' =>$product_sku,
						'product_variation_id' => $product_variations_id,
						'default_purchase_price' => $product_price,
						'dpp_inc_tax' => $product_price,
						'profit_percent' => 0.00,
						'default_sell_price' => $product_price,
						'sell_price_inc_tax' => $product_price
					
					]); 	

        $variation_location_details=DB::table('variation_location_details')->insertGetId([
						'product_id' => $product_id,
						'product_variation_id' =>$product_variations_id,
						'variation_id' => $variations_id,
						'location_id' => 1,
						'qty_available' => $product_quantity
					
					]); 						
					
			 DB::table('manufacturingorders')->where('id', $order_id)->update(array('product_id' => $product_id,'price' => $product_price, 'status' => 'Completed'));  
		
					 
		 $output = ['success' => true,
                            'msg' => __("Manufactured Product moved to Pos Successfully ")
                            ];
							
			   return redirect('products')->with('status', $output);	
					
   }




















       public function materialpurchase()
    {
		
		
        if (!auth()->user()->can('purchase.view') && !auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $purchases = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                    ->join(
                        'business_locations AS BS',
                        'transactions.location_id',
                        '=',
                        'BS.id'
                    )
                    ->leftJoin(
                        'transaction_payments AS TP',
                        'transactions.id',
                        '=',
                        'TP.transaction_id'
                    )
                    ->leftJoin(
                        'transactions AS PR',
                        'transactions.id',
                        '=',
                        'PR.return_parent_id'
                    )
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'purchase')
                    ->select(
                        'transactions.id',
                        'transactions.document',
                        'transactions.transaction_date',
                        'transactions.ref_no',
                        'contacts.name',
                        'transactions.status',
                        'transactions.payment_status',
                        'transactions.final_total',
                        'BS.name as location_name',
                        'PR.id as return_transaction_id',
                        DB::raw('SUM(TP.amount) as amount_paid'),
                        DB::raw('(SELECT SUM(TP2.amount) FROM transaction_payments AS TP2 WHERE
                        TP2.transaction_id=PR.id ) as return_paid'),
                        DB::raw('COUNT(PR.id) as return_exists'),
                        DB::raw('COALESCE(PR.final_total, 0) as amount_return')
                    )
                    ->groupBy('transactions.id');

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $purchases->whereIn('transactions.location_id', $permitted_locations);
            }

            if (!empty(request()->supplier_id)) {
                $purchases->where('contacts.id', request()->supplier_id);
            }
            if (!empty(request()->location_id)) {
                $purchases->where('transactions.location_id', request()->location_id);
            }
            if (!empty(request()->payment_status)) {
                $purchases->where('transactions.payment_status', request()->payment_status);
            }
            if (!empty(request()->status)) {
                $purchases->where('transactions.status', request()->status);
            }
            
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $purchases->whereDate('transactions.transaction_date', '>=', $start)
                            ->whereDate('transactions.transaction_date', '<=', $end);
            }
            return Datatables::of($purchases)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                data-toggle="dropdown" aria-expanded="false">' .
                                __("messages.actions") .
                                '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">';
                    if (auth()->user()->can("purchase.view")) {
                        $html .= '<li><a href="#" data-href="' . action('PurchaseController@show', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';
                    }
                    if (auth()->user()->can("purchase.view")) {
                        $html .= '<li><a href="#" class="print-invoice" data-href="' . action('PurchaseController@printInvoice', [$row->id]) . '"><i class="fa fa-print" aria-hidden="true"></i>'. __("messages.print") .'</a></li>';
                    }
                    if (auth()->user()->can("purchase.update")) {
                        $html .= '<li><a href="' . action('PurchaseController@edit', [$row->id]) . '"><i class="glyphicon glyphicon-edit"></i>' . __("messages.edit") . '</a></li>';
                    }
                    if (auth()->user()->can("purchase.delete")) {
                        $html .= '<li><a href="' . action('PurchaseController@destroy', [$row->id]) . '" class="delete-purchase"><i class="fa fa-trash"></i>' . __("messages.delete") . '</a></li>';
                    }

                    $html .= '<li><a href="' . action('LabelsController@show') . '?purchase_id=' . $row->id . '" data-toggle="tooltip" title="Print Barcode/Label"><i class="fa fa-barcode"></i>' . __('barcode.labels') . '</a></li>';

                    if (auth()->user()->can("purchase.view") && !empty($row->document)) {
                        $document_name = !empty(explode("_", $row->document, 2)[1]) ? explode("_", $row->document, 2)[1] : $row->document ;
                        $html .= '<li><a href="' . url('uploads/documents/' . $row->document) .'" download="' . $document_name . '"><i class="fa fa-download" aria-hidden="true"></i>' . __("purchase.download_document") . '</a></li>';
                        if (isFileImage($document_name)) {
                            $html .= '<li><a href="#" data-href="' . url('uploads/documents/' . $row->document) .'" class="view_uploaded_document"><i class="fa fa-picture-o" aria-hidden="true"></i>' . __("lang_v1.view_document") . '</a></li>';
                        }
                    }
                                        
                    if (auth()->user()->can("purchase.create")) {
                        $html .= '<li class="divider"></li>';
                        if ($row->payment_status != 'paid') {
                            $html .= '<li><a href="' . action('TransactionPaymentController@addPayment', [$row->id]) . '" class="add_payment_modal"><i class="fa fa-money" aria-hidden="true"></i>' . __("purchase.add_payment") . '</a></li>';
                        }
                        $html .= '<li><a href="' . action('TransactionPaymentController@show', [$row->id]) .
                        '" class="view_payment_modal"><i class="fa fa-money" aria-hidden="true" ></i>' . __("purchase.view_payments") . '</a></li>';
                    }

                    if (auth()->user()->can("purchase.update")) {
                        $html .= '<li><a href="' . action('PurchaseReturnController@add', [$row->id]) .
                        '"><i class="fa fa-undo" aria-hidden="true" ></i>' . __("lang_v1.purchase_return") . '</a></li>';
                    }
					
					/* added by robin */
					
					 if (auth()->user()->can("purchase.update")) {
                        $html .= '<li><a href="' . action('PurchaseController@track', [$row->id]) .
                        '"><i class="fa fa-get-pocket" aria-hidden="true" ></i>Purchase Track</a></li>';
                    }
                 
                    if (auth()->user()->can("send_notification")) {
                        if ($row->status == 'ordered') {
                            $html .= '<li><a href="#" data-href="' . action('NotificationController@getTemplate', ["transaction_id" => $row->id,"template_for" => "new_order"]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-envelope" aria-hidden="true"></i> ' . __("lang_v1.new_order_notification") . '</a></li>';
                        } elseif ($row->status == 'received') {
                            $html .= '<li><a href="#" data-href="' . action('NotificationController@getTemplate', ["transaction_id" => $row->id,"template_for" => "items_received"]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-envelope" aria-hidden="true"></i> ' . __("lang_v1.item_received_notification") . '</a></li>';
                        } elseif ($row->status == 'pending') {
                            $html .= '<li><a href="#" data-href="' . action('NotificationController@getTemplate', ["transaction_id" => $row->id,"template_for" => "items_pending"]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-envelope" aria-hidden="true"></i> ' . __("lang_v1.item_pending_notification") . '</a></li>';
                        }
                    }

                    $html .=  '</ul></div>';
                    return $html;
                })
                ->removeColumn('id')
                ->editColumn('ref_no', function ($row) {
                    return !empty($row->return_exists) ? $row->ref_no . ' <small class="label bg-red label-round no-print" title="' . __('lang_v1.some_qty_returned') .'"><i class="fa fa-undo"></i></small>' : $row->ref_no;
                })
                ->editColumn(
                    'final_total',
                    '<span class="display_currency final_total" data-currency_symbol="true" data-orig-value="{{$final_total}}">{{$final_total}}</span>'
                )
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn(
                    'status',
                    '<span class="label @transaction_status($status) status-label no-print" data-status-name="{{__(\'lang_v1.\' . $status)}}" data-orig-value="{{$status}}">{{__(\'lang_v1.\' . $status)}}
                        </span><span class="print_section">{{__(\'lang_v1.\' . $status)}}</span>'
                )
                ->editColumn(
                    'payment_status',
                    '<a href="{{ action("TransactionPaymentController@show", [$id])}}" class="view_payment_modal payment-status payment-status-label no-print" data-orig-value="{{$payment_status}}" data-status-name="{{__(\'lang_v1.\' . $payment_status)}}"><span class="label @payment_status($payment_status)">{{__(\'lang_v1.\' . $payment_status)}}
                        </span></a><span class="print_section">{{__(\'lang_v1.\' . $payment_status)}}</span>'
                )
                ->addColumn('payment_due', function ($row) {
                    $due = $row->final_total - $row->amount_paid;
                    $due_html = '<strong>' . __('lang_v1.purchase') .':</strong> <span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="' . $due . '">' . $due . '</span>';

                    
                           
                    if (!empty($row->return_exists)) {
                        $return_due = $row->amount_return - $row->return_paid;
                        $due_html .= '<br><strong>' . __('lang_v1.purchase_return') .':</strong> <a href="' . action("TransactionPaymentController@show", [$row->return_transaction_id]) . '" class="view_purchase_return_payment_modal no-print"><span class="display_currency purchase_return" data-currency_symbol="true" data-orig-value="' . $return_due . '">' . $return_due . '</span></a><span class="display_currency print_section" data-currency_symbol="true">' . $return_due . '</span>';
                    }
                    return $due_html;
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("purchase.view")) {
                            return  action('PurchaseController@show', [$row->id]) ;
                        } else {
                            return '';
                        }
                    }])
                ->rawColumns(['final_total', 'action', 'payment_due', 'payment_status', 'status', 'ref_no'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers = Contact::suppliersDropdown($business_id, false);
        $orderStatuses = $this->productUtil->orderStatuses();

        return view('manufacturing.materialpurchase')
            ->with(compact('business_locations', 'suppliers', 'orderStatuses'));
			
			
    }

   









  /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');

            //Check if subscribed or not
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse(action('PurchaseController@index'));
            }

            $transaction_data = $request->only([ 'ref_no','barcode', 'status', 'contact_id', 'transaction_date', 'total_before_tax', 'location_id','discount_type', 'discount_amount','tax_id', 'tax_amount', 'shipping_details', 'shipping_charges', 'final_total', 'additional_notes', 'exchange_rate']);

            $exchange_rate = $transaction_data['exchange_rate'];

            //Reverse exchange rate and save it.
            //$transaction_data['exchange_rate'] = $transaction_data['exchange_rate'];

            //TODO: Check for "Undefined index: total_before_tax" issue
            //Adding temporary fix by validating
            $request->validate([
                'status' => 'required',
                'contact_id' => 'required',
                'transaction_date' => 'required',
                'total_before_tax' => 'required',
                'location_id' => 'required',
                'final_total' => 'required',
                'document' => 'file|max:'. (config('constants.document_size_limit') / 1000)
            ]);

            $user_id = $request->session()->get('user.id');
            $enable_product_editing = $request->session()->get('business.enable_editing_product_from_purchase');

            //Update business exchange rate.
            Business::update_business($business_id, ['p_exchange_rate' => ($transaction_data['exchange_rate'])]);

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            //unformat input values
            $transaction_data['total_before_tax'] = $this->productUtil->num_uf($transaction_data['total_before_tax'], $currency_details)*$exchange_rate;

            // If discount type is fixed them multiply by exchange rate, else don't
            if ($transaction_data['discount_type'] == 'fixed') {
                $transaction_data['discount_amount'] = $this->productUtil->num_uf($transaction_data['discount_amount'], $currency_details)*$exchange_rate;
            } elseif ($transaction_data['discount_type'] == 'percentage') {
                $transaction_data['discount_amount'] = $this->productUtil->num_uf($transaction_data['discount_amount'], $currency_details);
            } else {
                $transaction_data['discount_amount'] = 0;
            }

            $transaction_data['tax_amount'] = $this->productUtil->num_uf($transaction_data['tax_amount'], $currency_details)*$exchange_rate;
            $transaction_data['shipping_charges'] = $this->productUtil->num_uf($transaction_data['shipping_charges'], $currency_details)*$exchange_rate;
            $transaction_data['final_total'] = $this->productUtil->num_uf($transaction_data['final_total'], $currency_details)*$exchange_rate;

            $transaction_data['business_id'] = $business_id;
            $transaction_data['created_by'] = $user_id;
            $transaction_data['type'] = 'purchase';
            $transaction_data['payment_status'] = 'due';
            $transaction_data['transaction_date'] = $this->productUtil->uf_date($transaction_data['transaction_date'], true);

            //upload document
            $transaction_data['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');
            
            DB::beginTransaction();

            //Update reference count
            $ref_count = $this->productUtil->setAndGetReferenceCount($transaction_data['type']);
            //Generate reference number
            if (empty($transaction_data['ref_no'])) {
                $transaction_data['ref_no'] = $this->productUtil->generateReferenceNumber($transaction_data['type'], $ref_count);
            }

            $transaction = Transaction::create($transaction_data);
            
            $purchase_lines = [];
            $purchases = $request->input('purchases');

            $this->productUtil->createOrUpdatePurchaseLines($transaction, $purchases, $currency_details, $enable_product_editing);

            //Add Purchase payments
            $this->transactionUtil->createOrUpdatePaymentLines($transaction, $request->input('payment'));

            //update payment status
            $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);

            //Adjust stock over selling if found
            $this->productUtil->adjustStockOverSelling($transaction);
            
            DB::commit();
            
            $output = ['success' => 1,
                            'msg' => __('purchase.purchase_add_success')
                        ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __('messages.something_went_wrong')
                        ];
        }

        return redirect('purchases')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('purchase.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $taxes = TaxRate::where('business_id', $business_id)
                            ->pluck('name', 'id');
        $purchase = Transaction::where('business_id', $business_id)
                                ->where('id', $id)
                                ->with(
                                    'contact',
                                    'purchase_lines',
                                    'purchase_lines.product',
                                    'purchase_lines.product.unit',
                                    'purchase_lines.variations',
                                    'purchase_lines.variations.product_variation',
                                    'purchase_lines.sub_unit',
                                    'location',
                                    'payment_lines',
                                    'tax'
                                )
                                ->firstOrFail();

        foreach ($purchase->purchase_lines as $key => $value) {
            if (!empty($value->sub_unit_id)) {
                $formated_purchase_line = $this->productUtil->changePurchaseLineUnit($value);
                $purchase->purchase_lines[$key] = $formated_purchase_line;
            }
        }
        
        $payment_methods = $this->productUtil->payment_types();

        $purchase_taxes = [];
        if (!empty($purchase->tax)) {
            if ($purchase->tax->is_tax_group) {
                $purchase_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($purchase->tax, $purchase->tax_amount));
            } else {
                $purchase_taxes[$purchase->tax->name] = $purchase->tax_amount;
            }
        }

        return view('purchase.show')
                ->with(compact('taxes', 'purchase', 'payment_methods', 'purchase_taxes'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('purchase.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse(action('PurchaseController@index'));
        }

        //Check if the transaction can be edited or not.
        $edit_days = request()->session()->get('business.transaction_edit_days');
        if (!$this->transactionUtil->canBeEdited($id, $edit_days)) {
            return back()
                ->with('status', ['success' => 0,
                    'msg' => __('messages.transaction_edit_not_allowed', ['days' => $edit_days])]);
        }

        //Check if return exist then not allowed
        if ($this->transactionUtil->isReturnExist($id)) {
            return back()->with('status', ['success' => 0,
                    'msg' => __('lang_v1.return_exist')]);
        }

        $business = Business::find($business_id);

        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $taxes = TaxRate::where('business_id', $business_id)
                            ->get();
        $purchase = Transaction::where('business_id', $business_id)
                    ->where('id', $id)
                    ->with(
                        'contact',
                        'purchase_lines',
                        'purchase_lines.product',
                        'purchase_lines.product.unit',
                        'purchase_lines.product.unit.sub_units',
                        'purchase_lines.variations',
                        'purchase_lines.variations.product_variation',
                        'location',
                        'purchase_lines.sub_unit'
                    )
                    ->first();
        
        foreach ($purchase->purchase_lines as $key => $value) {
            if (!empty($value->sub_unit_id)) {
                $formated_purchase_line = $this->productUtil->changePurchaseLineUnit($value);
                $purchase->purchase_lines[$key] = $formated_purchase_line;
            }
        }
     
        $taxes = TaxRate::where('business_id', $business_id)
                            ->get();
        $orderStatuses = $this->productUtil->orderStatuses();

        $business_locations = BusinessLocation::forDropdown($business_id);

        $default_purchase_status = null;
        if (request()->session()->get('business.enable_purchase_status') != 1) {
            $default_purchase_status = 'received';
        }

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }
        $customer_groups = CustomerGroup::forDropdown($business_id);

        $business_details = $this->businessUtil->getDetails($business_id);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        return view('purchase.edit')
            ->with(compact(
                'taxes',
                'purchase',
                'taxes',
                'orderStatuses',
                'business_locations',
                'business',
                'currency_details',
                'default_purchase_status',
                'customer_groups',
                'types',
                'shortcuts'
            ));
    }
	
	
	
	
	
	/* added by robin */
	 public function track($id)
    {
        if (!auth()->user()->can('purchase.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse(action('PurchaseController@index'));
        }

        //Check if the transaction can be edited or not.
        $edit_days = request()->session()->get('business.transaction_edit_days');
        if (!$this->transactionUtil->canBeEdited($id, $edit_days)) {
            return back()
                ->with('status', ['success' => 0,
                    'msg' => __('messages.transaction_edit_not_allowed', ['days' => $edit_days])]);
        }

        //Check if return exist then not allowed
        if ($this->transactionUtil->isReturnExist($id)) {
            return back()->with('status', ['success' => 0,
                    'msg' => __('lang_v1.return_exist')]);
        }

        $business = Business::find($business_id);

        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $taxes = TaxRate::where('business_id', $business_id)
                            ->get();
        $purchase = Transaction::where('business_id', $business_id)
                    ->where('id', $id)
                    ->with(
                        'contact',
                        'purchase_lines',
                        'purchase_lines.product',
                        'purchase_lines.product.unit',
                        'purchase_lines.product.unit.sub_units',
                        'purchase_lines.variations',
                        'purchase_lines.variations.product_variation',
                        'location',
                        'purchase_lines.sub_unit'
                    )
                    ->first();
        
        foreach ($purchase->purchase_lines as $key => $value) {
            if (!empty($value->sub_unit_id)) {
                $formated_purchase_line = $this->productUtil->changePurchaseLineUnit($value);
                $purchase->purchase_lines[$key] = $formated_purchase_line;
            }
        }
     
        $taxes = TaxRate::where('business_id', $business_id)
                            ->get();
        $orderStatuses = $this->productUtil->orderStatuses();

        $business_locations = BusinessLocation::forDropdown($business_id);

        $default_purchase_status = null;
        if (request()->session()->get('business.enable_purchase_status') != 1) {
            $default_purchase_status = 'received';
        }

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }
        $customer_groups = CustomerGroup::forDropdown($business_id);

        $business_details = $this->businessUtil->getDetails($business_id);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        return view('purchase.track')
            ->with(compact(
                'taxes',
                'purchase',
                'taxes',
                'orderStatuses',
                'business_locations',
                'business',
                'currency_details',
                'default_purchase_status',
                'customer_groups',
                'types',
                'shortcuts'
            ));
    }
	
	 public function savetrack(Request $request, $id)
	 {
		 //print_r($request);
		 $purchases = $request->input('purchases');
		 foreach($purchases as $purchase)
		 {
			 
			 $purchase_line_id=$purchase['purchase_line_id'];
			 $product_id=$purchase['product_id'];
			 $received=$purchase['received'];
			 
			 DB::update('update purchase_lines set quantity_received = ? where id = ?',[$received,$purchase_line_id]);
					
		 }
		  $output = ['success' => 1,
                            'msg' => __('purchase recevied status updated')
                        ];
						
		 return redirect('purchases')->with('status', $output);				
	 }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('purchase.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $transaction = Transaction::findOrFail($id);

            //Validate document size
            $request->validate([
                'document' => 'file|max:'. (config('constants.document_size_limit') / 1000)
            ]);

            $transaction = Transaction::findOrFail($id);
            $before_status = $transaction->status;
            $business_id = request()->session()->get('user.business_id');
            $enable_product_editing = $request->session()->get('business.enable_editing_product_from_purchase');

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            $update_data = $request->only([ 'ref_no', 'status', 'contact_id',
                            'transaction_date', 'total_before_tax',
                            'discount_type', 'discount_amount', 'tax_id',
                            'tax_amount', 'shipping_details',
                            'shipping_charges', 'final_total',
                            'additional_notes', 'exchange_rate']);

            $exchange_rate = $update_data['exchange_rate'];

            //Reverse exchage rate and save
            //$update_data['exchange_rate'] = number_format(1 / $update_data['exchange_rate'], 2);

            $update_data['transaction_date'] = $this->productUtil->uf_date($update_data['transaction_date'], true);

            //unformat input values
            $update_data['total_before_tax'] = $this->productUtil->num_uf($update_data['total_before_tax'], $currency_details) * $exchange_rate;

            // If discount type is fixed them multiply by exchange rate, else don't
            if ($update_data['discount_type'] == 'fixed') {
                $update_data['discount_amount'] = $this->productUtil->num_uf($update_data['discount_amount'], $currency_details) * $exchange_rate;
            } elseif ($update_data['discount_type'] == 'percentage') {
                $update_data['discount_amount'] = $this->productUtil->num_uf($update_data['discount_amount'], $currency_details);
            } else {
                $update_data['discount_amount'] = 0;
            }

            $update_data['tax_amount'] = $this->productUtil->num_uf($update_data['tax_amount'], $currency_details) * $exchange_rate;
            $update_data['shipping_charges'] = $this->productUtil->num_uf($update_data['shipping_charges'], $currency_details) * $exchange_rate;
            $update_data['final_total'] = $this->productUtil->num_uf($update_data['final_total'], $currency_details) * $exchange_rate;
            //unformat input values ends

            //upload document
            $document_name = $this->transactionUtil->uploadFile($request, 'document', 'documents');
            if (!empty($document_name)) {
                $update_data['document'] = $document_name;
            }

            DB::beginTransaction();

            //update transaction
            $transaction->update($update_data);

            //Update transaction payment status
            $this->transactionUtil->updatePaymentStatus($transaction->id);

            $purchases = $request->input('purchases');

            $delete_purchase_lines = $this->productUtil->createOrUpdatePurchaseLines($transaction, $purchases, $currency_details, $enable_product_editing, $before_status);

            //Update mapping of purchase & Sell.
            $this->transactionUtil->adjustMappingPurchaseSellAfterEditingPurchase($before_status, $transaction, $delete_purchase_lines);

            //Adjust stock over selling if found
            $this->productUtil->adjustStockOverSelling($transaction);

            DB::commit();

            $output = ['success' => 1,
                            'msg' => __('purchase.purchase_update_success')
                        ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => $e->getMessage()
                        ];
            return back()->with('status', $output);
        }

        return redirect('purchases')->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('purchase.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $business_id = request()->session()->get('user.business_id');

                //Check if return exist then not allowed
                if ($this->transactionUtil->isReturnExist($id)) {
                    $output = [
                        'success' => false,
                        'msg' => __('lang_v1.return_exist')
                    ];
                    return $output;
                }
        
                $transaction = Transaction::where('id', $id)
                                ->where('business_id', $business_id)
                                ->with(['purchase_lines'])
                                ->first();

                //Check if lot numbers from the purchase is selected in sale
                if (request()->session()->get('business.enable_lot_number') == 1 && $this->transactionUtil->isLotUsed($transaction)) {
                    $output = [
                        'success' => false,
                        'msg' => __('lang_v1.lot_numbers_are_used_in_sale')
                    ];
                    return $output;
                }
                
                $delete_purchase_lines = $transaction->purchase_lines;
                DB::beginTransaction();

                $transaction_status = $transaction->status;
                if ($transaction_status != 'received') {
                    $transaction->delete();
                } else {
                    //Delete purchase lines first
                    $delete_purchase_line_ids = [];
                    foreach ($delete_purchase_lines as $purchase_line) {
                        $delete_purchase_line_ids[] = $purchase_line->id;
                        $this->productUtil->decreaseProductQuantity(
                            $purchase_line->product_id,
                            $purchase_line->variation_id,
                            $transaction->location_id,
                            $purchase_line->quantity
                        );
                    }
                    PurchaseLine::where('transaction_id', $transaction->id)
                                ->whereIn('id', $delete_purchase_line_ids)
                                ->delete();

                    //Update mapping of purchase & Sell.
                    $this->transactionUtil->adjustMappingPurchaseSellAfterEditingPurchase($transaction_status, $transaction, $delete_purchase_lines);
                }

                //Delete Transaction
                $transaction->delete();

                //Delete account transactions
                AccountTransaction::where('transaction_id', $id)->delete();

                DB::commit();

                $output = ['success' => true,
                            'msg' => __('lang_v1.purchase_delete_success')
                        ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => $e->getMessage()
                        ];
        }

        return $output;
    }
    
    /**
     * Retrieves supliers list.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSuppliers()
    {
        if (request()->ajax()) {
            $term = request()->q;
            if (empty($term)) {
                return json_encode([]);
            }

            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            $query = Contact::where('business_id', $business_id);

            $selected_contacts = User::isSelectedContacts($user_id);
            if ($selected_contacts) {
                $query->join('user_contact_access AS uca', 'contacts.id', 'uca.contact_id')
                ->where('uca.user_id', $user_id);
            }
            $suppliers = $query->where(function ($query) use ($term) {
                $query->where('name', 'like', '%' . $term .'%')
                                ->orWhere('supplier_business_name', 'like', '%' . $term .'%')
                                ->orWhere('contacts.contact_id', 'like', '%' . $term .'%');
            })
                        ->select('contacts.id', 'name as text', 'supplier_business_name as business_name', 'contact_id')
                        ->onlySuppliers()
                        ->get();
            return json_encode($suppliers);
        }
    }

    /**
     * Retrieves products list.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProducts()
    {
        if (request()->ajax()) {
            $term = request()->term;

            $check_enable_stock = true;
            if (isset(request()->check_enable_stock)) {
                $check_enable_stock = filter_var(request()->check_enable_stock, FILTER_VALIDATE_BOOLEAN);
            }

            if (empty($term)) {
                return json_encode([]);
            }

            $business_id = request()->session()->get('user.business_id');
            $q = Product::leftJoin(
                'variations',
                'products.id',
                '=',
                'variations.product_id'
            )
                ->where(function ($query) use ($term) {
                    $query->where('products.name', 'like', '%' . $term .'%');
                    $query->orWhere('sku', 'like', '%' . $term .'%');
                    $query->orWhere('sub_sku', 'like', '%' . $term .'%');
                })
                ->active()
                ->where('business_id', $business_id)
                ->whereNull('variations.deleted_at')
                ->select(
                    'products.id as product_id',
                    'products.name',
                    'products.type',
                    // 'products.sku as sku',
                    'variations.id as variation_id',
                    'variations.name as variation',
                    'variations.sub_sku as sub_sku'
                )
                ->groupBy('variation_id');

            if ($check_enable_stock) {
                $q->where('enable_stock', 1);
            }
            $products = $q->get();
                
            $products_array = [];
            foreach ($products as $product) {
                $products_array[$product->product_id]['name'] = $product->name;
                $products_array[$product->product_id]['sku'] = $product->sub_sku;
                $products_array[$product->product_id]['type'] = $product->type;
                $products_array[$product->product_id]['variations'][]
                = [
                        'variation_id' => $product->variation_id,
                        'variation_name' => $product->variation,
                        'sub_sku' => $product->sub_sku
                        ];
            }

            $result = [];
            $i = 1;
            $no_of_records = $products->count();
            if (!empty($products_array)) {
                foreach ($products_array as $key => $value) {
                    if ($no_of_records > 1 && $value['type'] != 'single') {
                        $result[] = [ 'id' => $i,
                                    'text' => $value['name'] . ' - ' . $value['sku'],
                                    'variation_id' => 0,
                                    'product_id' => $key
                                ];
                    }
                    $name = $value['name'];
                    foreach ($value['variations'] as $variation) {
                        $text = $name;
                        if ($value['type'] == 'variable') {
                            $text = $text . ' (' . $variation['variation_name'] . ')';
                        }
                        $i++;
                        $result[] = [ 'id' => $i,
                                            'text' => $text . ' - ' . $variation['sub_sku'],
                                            'product_id' => $key ,
                                            'variation_id' => $variation['variation_id'],
                                        ];
                    }
                    $i++;
                }
            }
            
            return json_encode($result);
        }
    }

    
    /**
     * Checks if ref_number and supplier combination already exists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkRefNumber(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $contact_id = $request->input('contact_id');
        $ref_no = $request->input('ref_no');
        $purchase_id = $request->input('purchase_id');

        $count = 0;
        if (!empty($contact_id) && !empty($ref_no)) {
            //check in transactions table
            $query = Transaction::where('business_id', $business_id)
                            ->where('ref_no', $ref_no)
                            ->where('contact_id', $contact_id);
            if (!empty($purchase_id)) {
                $query->where('id', '!=', $purchase_id);
            }
            $count = $query->count();
        }
        if ($count == 0) {
            echo "true";
            exit;
        } else {
            echo "false";
            exit;
        }
    }

    /**
     * Checks if ref_number and supplier combination already exists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function printInvoice($id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $taxes = TaxRate::where('business_id', $business_id)
                                ->pluck('name', 'id');
            $purchase = Transaction::where('business_id', $business_id)
                                    ->where('id', $id)
                                    ->with(
                                        'contact',
                                        'purchase_lines',
                                        'purchase_lines.product',
                                        'purchase_lines.variations',
                                        'purchase_lines.variations.product_variation',
                                        'location',
                                        'payment_lines'
                                    )
                                    ->first();
            $payment_methods = $this->productUtil->payment_types();

            $output = ['success' => 1, 'receipt' => []];
            $output['receipt']['html_content'] = view('purchase.partials.show_details', compact('taxes', 'purchase', 'payment_methods'))->render();
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __('messages.something_went_wrong')
                        ];
        }

        return $output;
    }
}
