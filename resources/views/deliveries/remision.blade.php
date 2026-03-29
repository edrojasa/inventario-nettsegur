<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Remisión de entrega</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #222;
        }
        .letterhead {
            text-align: center;
            margin-bottom: 12px;
        }
        .letterhead img {
            max-width: 100%;
            max-height: 120px;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin: 10px 0;
        }
        .meta {
            margin-bottom: 12px;
        }
        .obs {
            border: 1px solid #ccc;
            padding: 8px;
            margin-bottom: 14px;
            min-height: 40px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        table, th, td {
            border: 1px solid #333;
        }
        th, td {
            padding: 6px;
            text-align: left;
        }
        th {
            background: #f0f0f0;
        }
        .signatures {
            margin-top: 36px;
        }
        .firma {
            display: inline-block;
            width: 45%;
            text-align: center;
            vertical-align: top;
        }
        .muted {
            color: #555;
            font-size: 10px;
        }
    </style>
</head>
<body>

@if (!empty($letterheadSrc))
    <div class="letterhead">
        <img src="{{ $letterheadSrc }}" alt="">
    </div>
@else
    <div class="title">{{ optional($settings)->site_name ?? 'Nettsegur' }}</div>
@endif

<div style="text-align: center; font-weight: bold; margin-bottom: 10px;">
    Remisión No: {{ $delivery->folio ?? 'SIN FOLIO' }}
</div>

<div class="title">Remisión de entrega de equipos y herramientas</div>
<div class="meta muted">Fecha: {{ now()->format('d/m/Y H:i') }} &nbsp;|&nbsp; Modo: {{ $modo === 'multiple' ? 'Varios equipos' : 'Simple' }}</div>

<p>
    <strong>Asignado a:</strong>

    @if($delivery->user)
        {{ $delivery->user->name }}
    @elseif($delivery->location)
        {{ $delivery->location->name }}
    @endif
</p>
<div class="meta">
    <strong>Entregado por:</strong> {{ optional(auth()->user())->name ?? optional(auth()->user())->username ?? 'Sistema' }}
</div>

@if (!empty($observaciones))
    <div class="meta"><strong>Observaciones:</strong></div>
    <div class="obs">{!! nl2br(e($observaciones)) !!}</div>
@endif

@if ($assets->isNotEmpty())
    <h4 style="margin-bottom: 6px;">Equipos</h4>
    <table>
        <thead>
        <tr>
            <th>#</th>
            <th>Activo</th>
            <th>Serial</th>
            <th>Placa</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($assets as $index => $asset)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $asset->name }}</td>
                <td>{{ $asset->serial }}</td>
                <td>{{ $asset->asset_tag }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif

@if (isset($accessoryRows) && $accessoryRows->isNotEmpty())
    <h4 style="margin-bottom: 6px;">Herramientas / accesorios</h4>
    <table>
        <thead>
        <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Modelo / nota</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($accessoryRows as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $row->accessory?->name }}</td>
                <td>{{ $row->accessory?->model_number }} {{ $row->note ? ' — ' . $row->note : '' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif

@if (isset($licenses) && $licenses->isNotEmpty())
    <h4 style="margin-bottom: 6px;">Licencias</h4>
    <table>
        <thead>
        <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Serial / Clave</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($licenses as $l => $lic)
            <tr>
                <td>{{ $l + 1 }}</td>
                <td>{{ $lic->name }}</td>
                <td>{{ $lic->category ? $lic->category->name : 'N/A' }}</td>
                <td>{{ $lic->serial ?: '—' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif

<div class="signatures">
    <div class="firma">
        ___________________________<br>
        Entrega
    </div>
    <div class="firma">
        ___________________________<br>
        Recibe
    </div>
</div>

</body>
</html>
