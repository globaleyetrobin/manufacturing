@extends('layouts.app')
@section('title', __('purchase.edit_purchase'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('purchase.edit_purchase') <i class="fa fa-keyboard-o hover-q text-muted" aria-hidden="true" data-container="body" data-toggle="popover" data-placement="bottom" data-content="@include('purchase.partials.keyboard_shortcuts_details')" data-html="true" data-trigger="hover" data-original-title="" title=""></i></h1>
</section>

<!-- Main content -->
<section class="content">

  <!-- Page level currency setting -->
  <input type="hidden" id="p_code" value="{{$currency_details->code}}">
  <input type="hidden" id="p_symbol" value="{{$currency_details->symbol}}">
  <input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
  <input type="hidden" id="p_decimal" value="{{$currency_details->decimal_separator}}">

  @include('layouts.partials.error')

  {!! Form::open(['url' =>  action('PurchaseController@savetrack' , [$purchase->id] ), 'method' => 'POST', 'id' => 'add_purchase_form', 'files' => true ]) !!}

  @php
    $currency_precision = config('constants.currency_precision', 2);
  @endphp

  <input type="hidden" id="purchase_id" value="{{ $purchase->id }}">

    @component('components.widget', ['class' => 'box-primary'])
        <div class="row">
            <div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
              <div class="form-group">
                {!! Form::label('supplier_id', __('purchase.supplier') . ':*') !!}
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-user"></i>
                  </span>
                  {!! Form::select('contact_id', [ $purchase->contact_id => $purchase->contact->name], $purchase->contact_id, ['class' => 'form-control', 'placeholder' => __('messages.please_select') , 'required', 'id' => 'supplier_id']); !!}
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat add_new_supplier" data-name=""><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                  </span>
                </div>
              </div>
            </div>

            <div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
              <div class="form-group">
                {!! Form::label('ref_no', __('purchase.ref_no') . '*') !!}
                {!! Form::text('ref_no', $purchase->ref_no, ['class' => 'form-control', 'required']); !!}
              </div>
            </div>
            
            <div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
              <div class="form-group">
                {!! Form::label('transaction_date', __('purchase.purchase_date') . ':*') !!}
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </span>
                  {!! Form::text('transaction_date', @format_datetime($purchase->transaction_date), ['class' => 'form-control', 'readonly', 'required']); !!}
                </div>
              </div>
            </div>
            
            <div class="col-sm-3 @if(!empty($default_purchase_status)) hide @endif">
              <div class="form-group">
                {!! Form::label('status', __('purchase.purchase_status') . ':*') !!}
                @show_tooltip(__('tooltip.order_status'))
                {!! Form::select('status', $orderStatuses, $purchase->status, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select') , 'required']); !!}
              </div>
            </div>

            <div class="clearfix"></div>

            <div class="col-sm-3">
              <div class="form-group">
                {!! Form::label('location_id', __('purchase.business_location').':*') !!}
                @show_tooltip(__('tooltip.purchase_location'))
                {!! Form::select('location_id', $business_locations, $purchase->location_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'disabled']); !!}
              </div>
            </div>

            <!-- Currency Exchange Rate -->
            <div class="col-sm-3 @if(!$currency_details->purchase_in_diff_currency) hide @endif">
              <div class="form-group">
                {!! Form::label('exchange_rate', __('purchase.p_exchange_rate') . ':*') !!}
                @show_tooltip(__('tooltip.currency_exchange_factor'))
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-info"></i>
                  </span>
                  {!! Form::number('exchange_rate', $purchase->exchange_rate, ['class' => 'form-control', 'required', 'step' => 0.001]); !!}
                </div>
                <span class="help-block text-danger">
                  @lang('purchase.diff_purchase_currency_help', ['currency' => $currency_details->name])
                </span>
              </div>
            </div>

            <div class="col-sm-3" style="display:none">
                <div class="form-group">
                    {!! Form::label('document', __('purchase.attach_document') . ':') !!}
                    {!! Form::file('document', ['id' => 'upload_document']); !!}
                    <p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])</p>
                </div>
            </div>
        </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-primary'])


        <div class="row">
            <div class="col-sm-12"> <!--  added by robin Purchase Received Status -->
             
              @include('purchase.partials.track_purchase_entry_row')
              <hr/>
              <div class="pull-right col-md-5" style="display:none">
                <table class="pull-right col-md-12">
                  <tr class="hide">
                    <th class="col-md-7 text-right">@lang( 'purchase.total_before_tax' ):</th>
                    <td class="col-md-5 text-left">
                      <span id="total_st_before_tax" class="display_currency"></span>
                      <input type="hidden" id="st_before_tax_input" value=0>
                    </td>
                  </tr>
                  <tr>
                    <th class="col-md-7 text-right">@lang( 'purchase.net_total_amount' ):</th>
                    <td class="col-md-5 text-left">
                      <span id="total_subtotal" class="display_currency">{{$purchase->total_before_tax/$purchase->exchange_rate}}</span>
                      <!-- This is total before purchase tax-->
                      <input type="hidden" id="total_subtotal_input" value="{{$purchase->total_before_tax/$purchase->exchange_rate}}" name="total_before_tax">
                    </td>
                  </tr>
                </table>
              </div>

            </div>
        </div>
    @endcomponent


    <div class="row">
        <div class="col-sm-12">
          <button type="button" id="submit_purchase_form" class="btn btn-primary pull-right btn-flat">@lang('messages.update')</button>
        </div>
    </div>
{!! Form::close() !!}
</section>
<!-- /.content -->
<!-- quick product modal -->
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
  @include('contact.create', ['quick_add' => true])
</div>

@endsection

@section('javascript')
  <script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
  <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
  <script type="text/javascript">
    $(document).ready( function(){
      update_table_total();
      update_grand_total();
    });
  </script>
  @include('purchase.partials.keyboard_shortcuts')
@endsection
