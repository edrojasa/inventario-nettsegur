@extends('layouts.default')

@section('title', 'Detalle de Remisión')

@section('content')

<div class="row">
    <div class="col-md-12">

        <div class="box box-primary">

            <div class="box-header with-border" style="padding: 15px;">
                <h3 class="box-title" style="margin-top: 5px;">
                    <i class="fas fa-file-signature text-primary" style="margin-right: 8px;"></i> Detalles de Remisión
                </h3>
                <div class="box-tools pull-right">
                    <span class="label label-primary" style="font-size: 15px; padding: 8px 12px; display: inline-block;">
                        <i class="fas fa-barcode"></i> {{ $delivery->folio }}
                    </span>
                </div>
            </div>

            <div class="box-body" style="padding: 25px;">

                <div class="row" style="margin-bottom: 25px;">
                    <div class="col-md-6 col-sm-12">
                        <p class="lead" style="margin-bottom: 0;">
                            <strong><i class="far fa-calendar-alt text-muted" style="margin-right: 5px;"></i> Fecha de Generación:</strong> <br>
                            <span style="font-size: 18px; margin-top: 5px; display: inline-block;">{{ $delivery->created_at }}</span>
                        </p>
                    </div>
                </div>

                <p>
    <strong>Asignado a:</strong>

    @if($delivery->user)
        👤 {{ $delivery->user->name }}
    @elseif($delivery->location)
        📍 {{ $delivery->location->name }}
    @else
        No asignado
    @endif
</p>

                <div class="row">
                    <div class="col-md-12">
                        <h4 style="margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                            <i class="fas fa-laptop text-info" style="margin-right: 5px;"></i> Equipos Asignados
                        </h4>
                        <div class="table-responsive" style="margin-bottom: 30px;">
                            <table class="table table-striped table-hover table-bordered">
                                <thead style="background-color: #f9f9f9;">
                                    <tr>
                                        <th><i class="fas fa-tag"></i> Nombre del Equipo</th>
                                        <th style="width: 35%;"><i class="fas fa-hashtag"></i> Serial</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($delivery->assets as $asset)
                                        <tr>
                                            <td style="vertical-align: middle; font-size: 15px;">{{ $asset->name ?? 'Equipo' }}</td>
                                            <td style="vertical-align: middle;"><code>{{ $asset->serial ?? '-' }}</code></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center text-muted" style="padding: 30px;">
                                                <i class="fas fa-box-open fa-2x" style="margin-bottom: 10px; color: #ddd;"></i><br>
                                                No hay equipos incluidos en esta remisión
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <h4 style="margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                            <i class="fas fa-keyboard text-success" style="margin-right: 5px;"></i> Herramientas / Accesorios Asignados
                        </h4>
                        <div class="table-responsive" style="margin-bottom: 20px;">
                            <table class="table table-striped table-hover table-bordered">
                                <thead style="background-color: #f9f9f9;">
                                    <tr>
                                        <th><i class="fas fa-plug"></i> Nombre del Accesorio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($delivery->accessories as $acc)
                                        <tr>
                                            <td style="vertical-align: middle; font-size: 15px;">{{ $acc->name ?? 'Accesorio' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center text-muted" style="padding: 30px;">
                                                <i class="fas fa-box-open fa-2x" style="margin-bottom: 10px; color: #ddd;"></i><br>
                                                No hay accesorios incluidos en esta remisión
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            
            <div class="box-footer" style="padding: 15px 25px; background-color: #fcfcfc;">
                <a href="{{ route('remisiones.index') }}" class="btn btn-default btn-lg">
                    <i class="fas fa-arrow-left"></i> Volver al Log
                </a>
                
                <a href="{{ route('remisiones.pdf', $delivery->id) }}" class="btn btn-success btn-lg pull-right">
                    <i class="fas fa-file-pdf"></i> Descargar Comprobante
                </a>
            </div>
        </div>

    </div>
</div>

@endsection