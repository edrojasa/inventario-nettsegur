@extends('layouts.default')

@section('title', 'Remisiones')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="box box-default">

            <div class="box-header with-border">
                <h3 class="box-title"><i class="fas fa-file-invoice" style="margin-right: 5px;"></i> Log de Remisiones</h3>
            </div>

            <div class="box-body">

                <!-- 🔍 Buscador Moderno -->
                <div class="row" style="margin-bottom: 20px;">
                    <div class="col-md-5 col-sm-12">
                        <form method="GET" action="{{ url()->current() }}">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fas fa-search"></i></span>
                                <input type="text" name="folio" class="form-control" placeholder="Buscar remisión específica..." value="{{ request('folio') }}">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- 📋 Tabla Mejorada -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered">
                        <thead>
                            <tr>
                                <th><i class="fas fa-barcode" style="margin-right: 5px;"></i> Folio</th>
                                <th><i class="far fa-calendar-alt" style="margin-right: 5px;"></i> Fecha de Creación</th>
                                <th class="text-right" style="width: 200px;"><i class="fas fa-cogs" style="margin-right: 5px;"></i> Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deliveries as $d)
                                <tr>
                                    <td style="vertical-align: middle;"><strong>{{ $d->folio }}</strong></td>
                                    <td style="vertical-align: middle;">{{ $d->created_at }}</td>
                                    <td class="text-right" style="vertical-align: middle;">
                                        <a href="{{ route('remisiones.show', $d->id) }}" class="btn btn-info btn-sm" title="Ver Detalles" style="margin-right: 5px;">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>

                                        <a href="{{ route('remisiones.pdf', $d->id) }}" class="btn btn-success btn-sm" title="Descargar PDF">
                                            <i class="fas fa-file-pdf"></i> PDF
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted" style="padding: 40px;">
                                        <i class="fas fa-inbox fa-3x" style="margin-bottom: 10px; color: #ddd;"></i><br>
                                        No se encontraron remisiones registradas en el sistema.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection