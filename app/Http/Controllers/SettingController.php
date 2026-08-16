<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Tampilkan halaman pengaturan.
     */
    public function index()
    {
        // Ambil semua setting dan kelompokkan berdasarkan group
        $settings = Setting::all()->groupBy('group');

        return view('settings.index', compact('settings'));
    }

    /**
     * Update pengaturan yang dikirim dari form.
     */
    public function update(Request $request)
    {
        // Ambil semua data request kecuali token
        $data = $request->except('_token', '_method');

        // Khusus checkbox/boolean, html form tidak mengirim key jika un-checked.
        // Jadi kita ambil semua setting bertipe boolean untuk dicek manual
        $booleanKeys = Setting::where('type', 'boolean')->pluck('key')->toArray();

        foreach ($booleanKeys as $key) {
            // Jika key boolean tidak ada di request, berarti checkboxnya dimatikan (false)
            if (!array_key_exists($key, $data)) {
                $data[$key] = '0';
            }
        }

        // Loop untuk update setiap setting
        foreach ($data as $key => $value) {
            // Abaikan key yang bukan milik setting (misalnya _method, _token)
            if (Setting::where('key', $key)->exists()) {
                Setting::set($key, $value);
            }
        }

        return redirect()->route('settings.index')->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
