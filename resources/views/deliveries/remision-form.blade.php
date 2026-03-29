@extends('layouts/default')

@section('title')
    Remisión de entrega
@parent
@stop

@section('content')
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h2 class="box-title">Remisión de entrega</h2>
                </div>
                <div class="box-body">

                    @if ($settings && $settings->remision_letterhead)
                        <p class="text-muted">
                            <x-icon type="checkmark" class="text-success" />
                            Membrete configurado en Ajustes → Marca. El PDF usará esa imagen como encabezado.
                        </p>
                    @else
                        <div class="callout callout-info">
                            <p>
                                Para que la remisión use su hoja membretada, suba la imagen en
                                <strong>Ajustes → Marca → Membrete remisiones (PDF)</strong>.
                            </p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('remision.pdf') }}" target="_blank" id="remision-form">
                        @csrf

                        <fieldset class="form-horizontal">
                            <legend>Tipo de remisión</legend>
                            <div class="form-group">
                                <div class="col-sm-9 col-sm-offset-3">
                                    <label class="radio-inline">
                                        <input type="radio" name="modo" value="simple" checked id="modo-simple">
                                        Simple (un solo equipo en la lista)
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="modo" value="multiple" id="modo-multiple">
                                        Varios equipos (entrega múltiple)
                                    </label>
                                </div>
                            </div>
                        </fieldset>

                        @if (isset($allAssetOptions) && $allAssetOptions->isNotEmpty())
                            <fieldset>
                                <legend>Equipos</legend>
                                <p class="help-block">Seleccione los activos a incluir en esta remisión.</p>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th style="width:48px;">Incluir</th>
                                            <th>Placa</th>
                                            <th>Nombre</th>
                                            <th>Serial</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($allAssetOptions as $a)
                                            <tr>
                                                <td>
                                                    <input type="checkbox"
                                                           name="assets[]"
                                                           value="{{ $a->id }}"
                                                           class="asset-cb"
                                                           {{ $assets->contains('id', $a->id) ? 'checked' : '' }}>
                                                </td>
                                                <td>{{ $a->asset_tag }}</td>
                                                <td>{{ $a->name }}</td>
                                                <td>{{ $a->serial }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </fieldset>
                        @endif

                        @if (isset($licenses) && $licenses->isNotEmpty())
                            <fieldset>
                                <legend>Licencias</legend>
                                <p class="help-block">Seleccione las licencias a incluir en esta remisión.</p>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th style="width:48px;">Incluir</th>
                                            <th>Licencia</th>
                                            <th>Categoría</th>
                                            <th>Clave Serial / Notas</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($licenses as $l)
                                            <tr>
                                                <td>
                                                    <input type="checkbox"
                                                           name="licenses[]"
                                                           value="{{ $l->id }}"
                                                           checked>
                                                </td>
                                                <td>{{ $l->name }}</td>
                                                <td>{{ $l->category ? $l->category->name : '' }}</td>
                                                <td>{{ $l->serial ?: '—' }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </fieldset>
                        @endif

                        @if ($accessoryCheckoutsForAssignee->isNotEmpty() || $accessoryCheckoutsForAccessory->isNotEmpty())
                            @php
                                $accRows = $accessoryCheckoutsForAccessory->isNotEmpty()
                                    ? $accessoryCheckoutsForAccessory
                                    : $accessoryCheckoutsForAssignee;
                            @endphp
                            <fieldset>
                                <legend>Herramientas / accesorios</legend>
                                <p class="help-block">Opcional: incluya accesorios asignados en la misma entrega.</p>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th style="width:48px;">Incluir</th>
                                            <th>Herramienta</th>
                                            <th>Asignado a</th>
                                            <th>Cantidad</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($accRows as $co)
                                            <tr>
                                                <td>
                                                    <input type="checkbox"
                                                           name="accessory_checkouts[]"
                                                           value="{{ $co->id }}">
                                                </td>
                                                <td>{{ $co->accessory?->name }}</td>
                                                <td>
                                                    @if ($co->assignedTo)
                                                        {{ $co->assignedTo->display_name ?? $co->assignedTo->name ?? '—' }}
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td>1</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </fieldset>
                        @endif

                        @if (isset($consumables) && count($consumables) > 0)
                            <fieldset style="margin-top: 15px;">
                                <legend>Consumibles</legend>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th>Seleccionar</th>
                                            <th>Consumible</th>
                                            <th>Cantidad</th>
                                            <th>Destino</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($consumables as $transaction)
                                            <tr>
                                                <td class="text-center">
                                                    <input type="checkbox" name="consumables[]"
                                                           value="{{ $transaction->id }}" checked>
                                                </td>
                                                <td>
                                                    @if($transaction->consumable)
                                                        {{ $transaction->consumable->name }}
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $transaction->quantity }}
                                                </td>
                                                <td>
                                                    @if($transaction->type == 'user' && $transaction->user)
                                                        {{ $transaction->user->present()->fullName }}
                                                    @elseif($transaction->type == 'location' && $transaction->location)
                                                        {{ $transaction->location->name }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </fieldset>
                        @endif

                        <fieldset>
                            <legend>Observaciones</legend>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <textarea name="observaciones"
                                              class="form-control"
                                              rows="4"
                                              placeholder="Condiciones de entrega, estado del equipo, notas al recibir…"></textarea>
                                </div>
                            </div>
                        </fieldset>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <x-icon type="download" />
                                    Descargar remisión en PDF
                                </button>
                                <a href="{{ url()->previous() }}" class="btn btn-default">Volver</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('moar_scripts')
    @parent
    <script nonce="{{ csrf_token() }}">
        (function () {
            var form = document.getElementById('remision-form');
            var modoSimple = document.getElementById('modo-simple');
            var modoMultiple = document.getElementById('modo-multiple');
            var assetCbs = document.querySelectorAll('.asset-cb');

            function syncSimpleMode() {
                if (!modoSimple || !modoSimple.checked) return;
                var first = true;
                assetCbs.forEach(function (cb) {
                    if (cb.checked) {
                        if (!first) cb.checked = false;
                        first = false;
                    }
                });
            }

            if (modoSimple) {
                modoSimple.addEventListener('change', syncSimpleMode);
            }
            if (form) {
                form.addEventListener('submit', function (e) {
                    var any = false;
                    document.querySelectorAll('input[name="assets[]"]:checked, input[name="accessory_checkouts[]"]:checked, input[name="licenses[]"]:checked, input[name="consumables[]"]:checked').forEach(function (el) {
                        if (el.checked) any = true;
                    });
                    if (!any) {
                        e.preventDefault();
                        alert('Seleccione al menos un equipo o una herramienta.');
                    }
                });
            }
            assetCbs.forEach(function (cb) {
                cb.addEventListener('change', function () {
                    if (modoSimple && modoSimple.checked) {
                        if (cb.checked) {
                            assetCbs.forEach(function (other) {
                                if (other !== cb) other.checked = false;
                            });
                        }
                    }
                });
            });
        })();
    </script>
@endsection
