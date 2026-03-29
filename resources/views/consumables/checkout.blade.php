@extends('layouts/default')

{{-- Page title --}}
@section('title')
     {{ trans('admin/consumables/general.checkout') }}
@parent
@stop

{{-- Page content --}}
@section('content')

@php
    $checkoutDestType = old('checkout_to_type', session('checkout_to_type') ?: 'user');
    if (! in_array($checkoutDestType, ['user', 'location'], true)) {
        $checkoutDestType = 'user';
    }
    $remaining = max(0, (int) $consumable->numRemaining());
@endphp

<div class="row">
  <div class="col-md-9">

    <form class="form-horizontal" id="checkout_form" method="post" action="" autocomplete="off">
      <input type="hidden" name="_token" value="{{ csrf_token() }}" />

      <div class="box box-default">

        @if ($consumable->id)
          <div class="box-header with-border">
            <div class="box-heading">
              <h2 class="box-title">{{ $consumable->name }} </h2>
            </div>
          </div>
        @endif

        <div class="box-body">

          {{-- Información del consumible (solo lectura) --}}
          @if ($consumable->name)
          <div class="form-group">
            <label class="col-sm-3 control-label">{{ trans('admin/consumables/general.consumable_name') }}</label>
            <div class="col-md-6">
              <p class="form-control-static">{{ $consumable->name }}</p>
            </div>
          </div>
          @endif

          @if ($consumable->company)
          <div class="form-group">
              <label class="col-sm-3 control-label">{{ trans('general.company') }}</label>
              <div class="col-md-6">
                  <p class="form-control-static">{!! $consumable->company->present()->formattedNameLink  !!}</p>
              </div>
          </div>
          @endif

          @if ($consumable->category)
          <div class="form-group">
              <label class="col-sm-3 control-label">{{ trans('general.category') }}</label>
              <div class="col-md-6">
                  <p class="form-control-static">{!! $consumable->category->present()->formattedNameLink  !!}</p>
              </div>
          </div>
          @endif

          <div class="form-group">
              <label class="col-sm-3 control-label">{{ trans('admin/components/general.total') }}</label>
              <div class="col-md-6">
                  <p class="form-control-static">{{ $consumable->qty }}</p>
              </div>
          </div>

          <div class="form-group">
              <label class="col-sm-3 control-label">{{ trans('admin/components/general.remaining') }}</label>
              <div class="col-md-6">
                  <p class="form-control-static">{{ $remaining }}</p>
              </div>
          </div>

          {{-- Destino: Usuario o Locación (mismo patrón btn-group + radios que el resto del sistema) --}}
          <div class="form-group" id="assignto_selector">
            <label class="col-md-3 control-label" id="checkout_dest_label">{{ trans('admin/hardware/form.checkout_to') }}</label>
            <div class="col-md-8">
                <div class="btn-group" data-toggle="buttons" role="radiogroup" aria-labelledby="checkout_dest_label">
                    <label class="btn btn-theme{{ $checkoutDestType === 'user' ? ' active' : '' }}">
                        <input name="checkout_to_type" value="user" type="radio"
                            {{ $checkoutDestType === 'user' ? 'checked' : '' }}
                            aria-label="{{ trans('general.user') }}">
                        <x-icon type="user" />
                        {{ trans('general.user') }}
                    </label>
                    <label class="btn btn-theme{{ $checkoutDestType === 'location' ? ' active' : '' }}">
                        <input name="checkout_to_type" value="location" type="radio"
                            {{ $checkoutDestType === 'location' ? 'checked' : '' }}
                            aria-label="{{ trans('general.location') }}">
                        <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                        {{ trans('general.location') }}
                    </label>
                </div>
                {!! $errors->first('checkout_to_type', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
            </div>
          </div>

          {{-- Selects: visibles según destino; el JS global (#assigned_user / #assigned_location) mantiene el comportamiento dinámico --}}
          @include ('partials.forms.edit.user-select', [
              'translated_name' => trans('general.user'),
              'fieldname' => 'assigned_to',
              'style' => $checkoutDestType === 'user' ? '' : 'display: none;',
          ])
          @include ('partials.forms.edit.location-select', [
              'translated_name' => trans('general.location'),
              'fieldname' => 'assigned_location',
              'style' => $checkoutDestType === 'location' ? '' : 'display: none;',
          ])

          @if ($consumable->requireAcceptance() || $consumable->getEula() || ($snipeSettings->webhook_endpoint!=''))
              <div class="form-group notification-callout">
                <div class="col-md-8 col-md-offset-3">
                  <div class="callout callout-info">

                    @if ($consumable->category->require_acceptance=='1')
                      <i class="far fa-envelope"></i>
                      {{ trans('admin/categories/general.required_acceptance') }}
                      <br>
                    @endif

                    @if ($consumable->getEula())
                      <i class="far fa-envelope"></i>
                      {{ trans('admin/categories/general.required_eula') }}
                        <br>
                    @endif

                    @if ($snipeSettings->webhook_endpoint!='')
                        <i class="fab fa-slack"></i>
                        {{ trans('general.webhook_msg_note') }}
                    @endif
                  </div>
                </div>
              </div>
          @endif

          {{-- Cantidad --}}
          <div class="form-group {{ $errors->has('qty') ? 'error' : '' }} ">
              <label for="checkout_qty" class="col-md-3 control-label">{{ trans('general.qty') }}</label>
              <div class="col-md-7 col-sm-12 required">
                  <div class="col-md-2" style="padding-left:0px">
                    <input class="form-control" type="number" name="checkout_qty" id="checkout_qty"
                        value="{{ old('checkout_qty', 1) }}"
                        min="1"
                        max="{{ $remaining > 0 ? $remaining : 1 }}"
                        maxlength="999999" />
                  </div>
              </div>
              {!! $errors->first('qty', '<div class="col-md-8 col-md-offset-3"><span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span></div>') !!}
          </div>

          {{-- Notas --}}
          <div class="form-group {{ $errors->has('note') ? 'error' : '' }}">
            <label for="note" class="col-md-3 control-label">{{ trans('admin/hardware/form.notes') }}</label>
            <div class="col-md-7">
              <textarea class="col-md-6 form-control" name="note" id="note" rows="4">{{ old('note') }}</textarea>
              {!! $errors->first('note', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
            </div>
          </div>

          {{-- Remisión --}}
          <div class="form-group">
              <label class="col-md-3 control-label"></label>
              <div class="col-md-7">
                  <label class="checkbox-inline">
                      <input type="checkbox" name="generate_remision" value="1" class="minimal" {{ old('generate_remision') ? 'checked' : '' }}>
                      {{ trans('admin/consumables/general.generate_remision_checkbox') }}
                  </label>
              </div>
          </div>

        </div>

            <x-redirect_submit_options
                    index_route="consumables.index"
                    :button_label="trans('general.checkout')"
                    :options="[
                                'index' => trans('admin/hardware/form.redirect_to_all', ['type' => trans('general.consumables')]),
                                'item' => trans('admin/hardware/form.redirect_to_type', ['type' => trans('general.consumable')]),
                                'target' => trans('admin/hardware/form.redirect_to_checked_out_to'),
                                ]"/>
      </div>
    </form>

  </div>
</div>
@stop

@section('moar_scripts')
    @include('partials/assets-assigned')
    <script nonce="{{ csrf_token() }}">
        $(function () {
            $('input[name=checkout_to_type]').on('change', function () {
                var t = $('input[name=checkout_to_type]:checked').val();
                if (t === 'location') {
                    $('[name="assigned_to"]').val('').trigger('change.select2');
                } else if (t === 'user') {
                    $('[name="assigned_location"]').val('').trigger('change.select2');
                }
            });
        });
    </script>
@endsection
