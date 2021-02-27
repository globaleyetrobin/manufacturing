
    <tr>
        <td><span class="sr_number"></span></td>
        <td>
          {{ $product->material_name }} ({{$product->material_code}})
            
        </td>
        <td>
            {!! Form::hidden('purchases[' . $row_count . '][product_id]', $product->id ); !!}
            {!! Form::hidden('purchases[' . $row_count . '][variation_id]', 0 , ['class' => 'hidden_variation_id']); !!}

            @php
                $check_decimal = 'false';
                /*if($product->unit->allow_decimal == 0){
                    $check_decimal = 'true';
                }*/
				 $check_decimal = 'true';
                $currency_precision = config('constants.currency_precision', 2);
                $quantity_precision = config('constants.quantity_precision', 2);
            @endphp
            {!! Form::text('purchases[' . $row_count . '][quantity]', number_format(1, $quantity_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm purchase_quantity input_number mousetrap', 'required', 'data-rule-abs_digit' => $check_decimal, 'data-msg-abs_digit' => __('lang_v1.decimal_value_not_allowed')]); !!}
            <input type="hidden" class="base_unit_cost" value="0">
            <input type="hidden" class="base_unit_selling_price" value="0">

            <input type="hidden" name="purchases[{{$row_count}}][product_unit_id]" value="{{$product->unit_id}}">
            @if(!empty($sub_units))
                <br>
                <select name="purchases[{{$row_count}}][sub_unit_id]" class="form-control input-sm sub_unit">
                    @foreach($sub_units as $key => $value)
                        <option value="{{$key}}" data-multiplier="{{$value['multiplier']}}">
                            {{$value['name']}}
                        </option>
                    @endforeach
                </select>
            @else 
                {{ $product->material_unit }}
            @endif
        </td>
        <td>
            {!! Form::text('purchases[' . $row_count . '][pp_without_discount]',
            number_format(0, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm purchase_unit_cost_without_discount input_number', 'required']); !!}
        </td>
        <td>
            {!! Form::text('purchases[' . $row_count . '][discount_percent]', 0, ['class' => 'form-control input-sm inline_discounts input_number', 'required']); !!}
        </td>
        <td>
            {!! Form::text('purchases[' . $row_count . '][purchase_price]',
            number_format(@$variation->default_purchase_price, $currency_precision, @$currency_details->decimal_separator, @$currency_details->thousand_separator), ['class' => 'form-control input-sm purchase_unit_cost input_number', 'required']); !!}
        </td>
        <td class="{{$hide_tax}}">
            <span class="row_subtotal_before_tax display_currency">0</span>
            <input type="hidden" class="row_subtotal_before_tax_hidden" value=0>
        </td>
        <td class="{{$hide_tax}}">
            <div class="input-group">
                <select name="purchases[{{ $row_count }}][purchase_line_tax_id]" class="form-control select2 input-sm purchase_line_tax_id" placeholder="'Please Select'">
                    <option value="" data-tax_amount="0" @if( $hide_tax == 'hide' )
                    selected @endif >@lang('lang_v1.none')</option>
                    @foreach($taxes as $tax)
                        <option value="{{ $tax->id }}" data-tax_amount="{{ $tax->amount }}" @if( $product->tax == $tax->id && $hide_tax != 'hide') selected @endif >{{ $tax->name }}</option>
                    @endforeach
                </select>
                {!! Form::hidden('purchases[' . $row_count . '][item_tax]', 0, ['class' => 'purchase_product_unit_tax']); !!}
                <span class="input-group-addon purchase_product_unit_tax_text">
                    0.00</span>
            </div>
        </td>
        <td class="{{$hide_tax}}">
            @php
                $dpp_inc_tax = number_format(@$variation->dpp_inc_tax, $currency_precision, @$currency_details->decimal_separator, @$currency_details->thousand_separator);
                if($hide_tax == 'hide'){
                    $dpp_inc_tax = number_format(@$variation->default_purchase_price, @$currency_precision, @$currency_details->decimal_separator, @$currency_details->thousand_separator);
                }

            @endphp
            {!! Form::text('purchases[' . $row_count . '][purchase_price_inc_tax]', $dpp_inc_tax, ['class' => 'form-control input-sm purchase_unit_cost_after_tax input_number', 'required']); !!}
        </td>
        <td>
            <span class="row_subtotal_after_tax display_currency">0</span>
            <input type="hidden" class="row_subtotal_after_tax_hidden" value=0>
        </td>
      <!--  <td class="@if(!session('business.enable_editing_product_from_purchase')) hide @endif">-->
	    <td class="hide">
        </td>
        <td class="hide" >
            
        </td>
        @if(session('business.enable_lot_number'))
            <td class="hide" >
               
            </td>
        @endif
        @if(session('business.enable_product_expiry'))
            <td style="text-align: left;" class="hide">

           
            </td>
        @endif
        <?php $row_count++ ;?>

        <td><i class="fa fa-times remove_purchase_entry_row text-danger" title="Remove" style="cursor:pointer;"></i></td>
    </tr>


<input type="hidden" id="row_count" value="{{ $row_count }}">