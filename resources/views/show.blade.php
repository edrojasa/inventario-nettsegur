<h2>REMISIÓN</h2>

<p><strong>Cliente:</strong> {{ $remision->cliente }}</p>
<p><strong>Proyecto:</strong> {{ $remision->proyecto }}</p>
<p><strong>Fecha:</strong> {{ $remision->fecha }}</p>

<hr>

<h4>Items entregados:</h4>

<table border="1" width="100%">
    <tr>
        <th>Tipo</th>
        <th>Item</th>
        <th>Cantidad</th>
    </tr>

    @foreach($items as $item)
    <tr>
        <td>{{ $item->tipo }}</td>
        <td>{{ $item->item_id }}</td>
        <td>{{ $item->cantidad }}</td>
    </tr>
    @endforeach
</table>

<br><br>

<p><strong>Entregado por:</strong> {{ $remision->entregado_por }}</p>
<p><strong>Recibido por:</strong> {{ $remision->recibido_por }}</p>

<br><br><br>

<table width="100%">
<tr>
<td>_____________________<br>Firma entrega</td>
<td>_____________________<br>Firma recibe</td>
</tr>
</table>