public function show($id)
{
    $remision = DB::table('remisiones')->where('id', $id)->first();

    $items = DB::table('remision_items')
        ->where('remision_id', $id)
        ->get();

    return view('remisiones.show', compact('remision', 'items'));
}