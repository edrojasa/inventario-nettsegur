<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\LicenseUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class LicenseUsersController extends Controller
{
    public function store(Request $request, $licenseId)
    {
        $license = License::findOrFail($licenseId);
        $this->authorize('update', $license);

        $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'required|string',
        ]);

        $licenseUser = new LicenseUser();
        $licenseUser->license_id = $license->id;
        $licenseUser->username = $request->input('username');
        $licenseUser->password = Crypt::encryptString($request->input('password'));
        $licenseUser->save();

        return redirect()->route('licenses.show', $license->id)->with('success', 'Usuario agregado exitosamente.');
    }

    public function destroy($licenseId, $id)
    {
        $licenseUser = LicenseUser::findOrFail($id);
        
        $license = License::findOrFail($licenseUser->license_id);
        $this->authorize('update', $license);

        $licenseUser->delete();

        return redirect()->route('licenses.show', $license->id)->with('success', 'Usuario eliminado exitosamente.');
    }
}
