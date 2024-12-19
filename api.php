<?php

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// API Routes untuk menu
Route::prefix('menu')->group(function () {

    // Mendapatkan daftar semua menu
    Route::get('/', function () {
        $menus = Menu::all();

        if ($menus->isEmpty()) {
            return response()->json([
                'data' => [],
                'status' => 'error',
                'code' => 404,
                'message' => 'Menu tidak ditemukan'
            ], 404); // Status 404 untuk data tidak ditemukan
        }

        return response()->json([
            'data' => $menus,
            'status' => 'success',
            'code' => 200
        ], 200); // Status 200 untuk sukses
    });

    // Mendapatkan detail menu berdasarkan ID
    Route::get('/{id}', function ($id) {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json([
                'data' => [],
                'status' => 'error',
                'code' => 404,
                'message' => 'Menu tidak ditemukan'
            ], 404); // Status 404 untuk data tidak ditemukan
        }

        return response()->json([
            'data' => $menu,
            'status' => 'success',
            'code' => 200
        ], 200); // Status 200 untuk sukses
    });

    // Memperbarui stok menu (untuk pembelian)
    Route::patch('/{id}', function (Request $request, $id) {
        $request->validate([
            'jumlah' => 'required|integer|min:1', // Validasi jumlah yang dikirim
        ]);

        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json([
                'status' => 'error',
                'message' => 'Menu tidak ditemukan'
            ], 404); // Status 404 jika menu tidak ditemukan
        }

        // Cek apakah stok mencukupi
        if ($menu->stock < $request->jumlah) {
            return response()->json([
                'status' => 'error',
                'message' => 'Stok tidak cukup'
            ], 400); // Status 400 untuk stok tidak cukup
        }

        // Kurangi stok
        $menu->stock -= $request->jumlah;
        $menu->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Stok berhasil diperbarui',
            'updated_stock' => $menu->stock
        ], 200); // Status 200 untuk pembaruan stok berhasil
    });
});
