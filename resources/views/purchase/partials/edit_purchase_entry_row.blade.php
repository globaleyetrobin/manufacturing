@php
    $hide_tax = '';
    if( session()->get('business.enable_inline_tax') == 0){
        $hide_tax = 'hide';
    }
    $currency_precision = config('constants.currency_precision', 2);
    $quantity_precision = config('constants.quantity_precision', 2);
@endphp
<div class="table-responsive">
    <table class="table table-condensed table-bordered table-th-green text-center table-striped" 
    id="purchase_entry_table">
        <thead>
              <tr>
                <th>#</th>
                <th>Material Name</th>
                <th>Quantity</th>
                <th>@lang( 'lang_v1.unit_cost_before_discount' )</th>
                <th>@lang( 'lang_v1.discount_percent' )</th>
                <th>@lang( 'purchase.unit_cost_before_tax' )</th>
                <th class="{{$hide_tax}}">@lang( 'purchase.subtotal_before_tax' )</th>
                <th class="{{$hide_tax}}">@lang( 'purchase.product_tax' )</th>
                <th class="{{$hide_tax}}">@lang( 'purchase.net_cost' )</th>
                <th>Total Amount</th>
               <!-- <th  class="@if(!session('business.enable_editing_product_from_purchase')) hide @endif">-->
			   <th class="{{$hide_tax}}">
                    @lang( 'lang_v1.profit_margin' )
                </th>
                <th class="{{$hide_tax}}" >@lang( 'purchase.unit_selling_price')</th>
                @if(session('business.enable_lot_number'))
                    <th class="{{$hide_tax}}">
                        @lang('lang_v1.lot_number')
                    </th>
                @endif
                @if(session('business.enable_product_expiry'))
                    <th class="{{$hide_tax}}">@lang('product.mfg_date') / @lang('product.exp_date')</th>
                @endif
                <th>
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </th>
              </tr>
        </thead>
        <tbody>
    <?php $row_count = 0; ?>
    @foreach($materials_lists as $purchase_line)
        <tr>
            <td><span class="sr_number"></span></td>
        <td> 
                {{ $purchase_line->material_name }} ({{$purchase_line->material_code}})
              
            </td>

          <td>
                {!! Form::hidden('purchases[' . $loop->index . '][product_id]', $purchase_line->product_id ); !!}
                {!! Form::hidden('purchases[' . $loop->index . '][variation_id]', $purchase_line->variation_id ); !!}
                {!! Form::hidden('purchases[' . $loop->index . '][purchase_line_id]',
                $purchase_line->id); !!}

            
                {!! Form::text('purchases[' . $loop->index . '][quantity]', 
                number_format($purchase_line->quantity, $quantity_precision, $currency_details->decimal_separator, $currency_details->thousand_separator),
                ['class' => 'form-control input-sm purchase_quantity input_number mousetrap', 'required', 'data-rule-abs_digit' => @$check_decimal, 'data-msg-abs_digit' => __('lang_v1.decimal_value_not_allowed')]); !!} 

                <input type="hidden" class="base_unit_cost" value="{{$purchase_line->default_purchase_price}}">
             
				
		

            </td> 
			
            <td>
                {!! Form::text('purchases[' . $loop->index . '][pp_without_discount]', number_format($purchase_line->pp_without_discount/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm purchase_unit_cost_without_discount input_number', 'required']); !!}
            </td>
            <td>
                {!! Form::text('purchases[' . $loop->index . '][discount_percent]', number_format($purchase_line->discount_percent, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm inline_discounts input_number', 'required']); !!} <b>%</b>
            </td>
            
			
			
			<td>
                {!! Form::text('purchases[' . $loop->index . '][purchase_price]', 
                number_format($purchase_line->purchase_price/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm purchase_unit_cost input_number', 'required']); !!}
            </td>
			
			<td >
                <span class="row_subtotal_before_tax">
                    {{number_format($purchase_line->quantity * $purchase_line->purchase_price/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}
                </span>
                <input type="hidden" class="row_subtotal_before_tax_hidden" value="{{number_format($purchase_line->quantity * $purchase_line->purchase_price/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}">
            </td>
			
			<td class="{{$hide_tax}}">
                <div class="input-group">
                    <select name="purchases[{{ $loop->index }}][purchase_line_tax_id]" class="form-control input-sm purchase_line_tax_id" placeholder="'Please Select'">
                        <option value="" data-tax_amount="0" @if( empty( $purchase_line->tax_id ) )
                        selected @endif >@lang('lang_v1.none')</option>
                        @foreach($taxes as $tax)
                            <option value="{{ $tax->id }}" data-tax_amount="{{ $tax->amount }}" @if( $purchase_line->tax_id == $tax->id) selected @endif >{{ $tax->name }}</option>
                        @endforeach
                    </select>
                    <span class="input-group-addon purchase_product_unit_tax_text">
                        {{number_format($purchase_line->item_tax/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}
                    </span>
                    {!! Form::hidden('purchases[' . $loop->index . '][item_tax]', number_format($purchase_line->item_tax/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'purchase_product_unit_tax']); !!}
                </div>
            </td>
			
			
			<td class="{{$hide_tax}}">
                {!! Form::text('purchases[' . $loop->index . '][purchase_price_inc_tax]', number_format($purchase_line->purchase_price_inc_tax/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm purchase_unit_cost_after_tax input_number', 'required']); !!}
            </td>
			
			<td  class="{{$hide_tax}}">
                <span class="row_subtotal_after_tax">
                {{number_format($purchase_line->purchase_price_inc_tax * $purchase_line->quantity/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}
                </span>
                <input type="hidden" class="row_subtotal_after_tax_hidden" value="{{number_format($purchase_line->purchase_price_inc_tax * $purchase_line->quantity/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}">
            </td>

            <td class="{{$hide_tax}}" >
                @php
                    $pp = $purchase_line->purchase_price;
                    $sp = $purchase_line->default_sell_price;
                    
                    if($pp == 0){
                        $profit_percent = 100;
                    } else {
                        $profit_percent = (($sp - $pp) * 100 / $pp);
                    }
                @endphp
                
                {!! Form::text('purchases[' . $loop->index . '][profit_percent]', 
                number_format($profit_percent, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), 
                ['class' => 'form-control input-sm input_number profit_percent', 'required']); !!}
            </td>
			
			
			<td class="{{$hide_tax}}">
                @if(session('business.enable_editing_product_from_purchase'))
                    {!! Form::text('purchases[' . $loop->index . '][default_sell_price]', number_format($sp, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm input_number default_sell_price', 'required']); !!}
                @else
                    {{number_format($sp, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}
                @endif

            </td>
            @if(session('business.enable_lot_number'))
                <td class="{{$hide_tax}}">
                    {!! Form::text('purchases[' . $loop->index . '][lot_number]', $purchase_line->lot_number, ['class' => 'form-control input-sm']); !!}
                </td>
            @endif
			
			
			@if(session('business.enable_product_expiry'))
                <td  class="{{$hide_tax}}" style="text-align: left;">
                    @php
                        $expiry_period_type = !empty($purchase_line->product->expiry_period_type) ? $purchase_line->product->expiry_period_type : 'month';
                    @endphp
                    @if(!empty($expiry_period_type))
                    <input type="hidden" class="row_product_expiry" value="">
                    <input type="hidden" class="row_product_expiry_type" value="">

                    @if(session('business.expiry_type') == 'add_manufacturing')
                        @php
                            $hide_mfg = false;
                        @endphp
                    @else
                        @php
                            $hide_mfg = true;
                        @endphp
                    @endif

                    <b class="@if($hide_mfg) hide @endif"><small>@lang('product.mfg_date'):</small></b>
                    @php
                        $mfg_date = null;
                        $exp_date = null;
                     
                    @endphp
                    <div class="input-group @if($hide_mfg) hide @endif">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        {!! Form::text('purchases[' . $loop->index . '][mfg_date]', !empty($mfg_date) ? @format_date($mfg_date) : null, ['class' => 'form-control input-sm expiry_datepicker mfg_date', 'readonly']); !!}
                    </div>
                    <b><small>@lang('product.exp_date'):</small></b>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        {!! Form::text('purchases[' . $loop->index . '][exp_date]', null, ['class' => 'form-control input-sm expiry_datepicker exp_date', 'readonly']); !!}
                    </div>
                    @else
                    <div class="text-center">
                        @lang('product.not_applicable')
                    </div>
                    @endif
                </td>
            @endif
			
			       

            <td><i class="fa fa-times remove_purchase_entry_row text-danger" title="Remove" style="cursor:pointer;"></i></td>
        </tr>
        <?php $row_count = $loop->index + 1 ; ?>
    @endforeach
        </tbody>
    </table>
</div>
<input type="hidden" id="row_count" value="{{ $row_count }}">