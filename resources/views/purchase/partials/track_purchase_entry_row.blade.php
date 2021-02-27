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
                <th>@lang( 'product.product_name' )</th>
                <th>@lang( 'purchase.purchase_quantity' )</th>
				
				<th>Recevied</th>
				<th>Pending</th>
                
              </tr>
        </thead>
        <tbody>
    <?php $row_count = 0; ?>
    @foreach($purchase->purchase_lines as $purchase_line)
	
        <tr>
            <td><span class="sr_number"></span></td>
            <td>
                {{ $purchase_line->product->name }} ({{$purchase_line->variations->sub_sku}})
                @if( $purchase_line->product->type == 'variable') 
                    <br/>(<b>{{ $purchase_line->variations->product_variation->name}}</b> : {{ $purchase_line->variations->name}})
                @endif
            </td>

            <td>
                {!! Form::hidden('purchases[' . $loop->index . '][product_id]', $purchase_line->product_id ); !!}
                {!! Form::hidden('purchases[' . $loop->index . '][variation_id]', $purchase_line->variation_id ); !!}
                {!! Form::hidden('purchases[' . $loop->index . '][purchase_line_id]',
                $purchase_line->id); !!}

                @php
                    $check_decimal = 'false';
                    if($purchase_line->product->unit->allow_decimal == 0){
                        $check_decimal = 'true';
                    }
                @endphp
            
                {!! Form::text('purchases[' . $loop->index . '][quantity]', 
                number_format($purchase_line->quantity, $quantity_precision, $currency_details->decimal_separator, $currency_details->thousand_separator),
                ['class' => 'form-control input-sm purchase_quantity input_number mousetrap', 'required', 'data-rule-abs_digit' => $check_decimal, 'data-msg-abs_digit' => __('lang_v1.decimal_value_not_allowed')]); !!} 

                <input type="hidden" class="base_unit_cost" value="{{$purchase_line->variations->default_purchase_price}}">
                @if(count($purchase_line->product->unit->sub_units) > 0)
                    <br>
                    <select name="purchases[{{$loop->index}}][sub_unit_id]" value="{{$purchase_line->sub_unit_id}}" class="form-control input-sm sub_unit">
                            <option value="{{$purchase_line->product->unit->id}}" data-multiplier="1">{{$purchase_line->product->unit->short_name}}</option>
                        @foreach($purchase_line->product->unit->sub_units as $sub_unit)
                            <option value="{{$sub_unit->id}}" data-multiplier="{{$sub_unit->base_unit_multiplier}}" @if($sub_unit->id == $purchase_line->sub_unit_id) selected @endif >
                                {{$sub_unit->short_name}}
                            </option>
                        @endforeach
                    </select>
                @else 
                    {{ $purchase_line->product->unit->short_name }}
                @endif

                <input type="hidden" name="purchases[{{$loop->index}}][product_unit_id]" value="{{$purchase_line->product->unit->id}}">

                <input type="hidden" class="base_unit_selling_price" value="{{$purchase_line->variations->default_sell_price}}">
            </td>
			<!-- added by robin -->
			<td>
			
			<input class="form-control" value=" {{ @$purchase_line->quantity_received}}" name="purchases[{{$loop->index}}][received]" type="text" >
			</td>
			
			<td>
			<input class="form-control" value=" {{@ $purchase_line->quantity - $purchase_line->quantity_received}}"  name="purchases[{{$loop->index}}][pending]" type="text" >
			</td>
           
        </tr>
        <?php $row_count = $loop->index + 1 ; ?>
    @endforeach
        </tbody>
    </table>
</div>
<input type="hidden" id="row_count" value="{{ $row_count }}">