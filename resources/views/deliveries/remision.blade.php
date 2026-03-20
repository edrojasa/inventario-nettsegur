<<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Remisión de Entrega</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
        }

        .info {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #000;
        }

        th, td {
            padding: 8px;
            text-align: center;
        }

        .signatures {
            margin-top: 50px;
        }

        .firma {
            display: inline-block;
            width: 45%;
            text-align: center;
        }
    </style>
</head>

<body>

<div class="header" style="text-align: center;">

    <img src="{{ asset('img/logoazul.png') }}" width="150" style="margin-bottom: 10px;">

    <div class="title">NETTSEGUR</div>
    <div>Remisión de Entrega de Equipos</div>
    <div>Fecha: {{ now()->format('Y-m-d H:i') }}</div>

</div>

<div class="info">
    <strong>Entregado por:</strong> {{ auth()->user()->name ?? 'Sistema' }}
</div>

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
        @foreach($assets as $index => $asset)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $asset->name }}</td>
            <td>{{ $asset->serial }}</td>
            <td>{{ $asset->asset_tag }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

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