<?php

namespace App\Http\Controllers\API;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PengembalianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = Pengembalian::where('user_id', auth()->id())->with('peminjaman.mobil')->get();
            return Helper::responseOK('Berhasil mendapatkan data', $data);
        } catch (\Throwable $th) {
            Log::error($th);
            return Helper::responseError($th->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_plat' => 'required',
        ]);

        try {

            $peminjaman = Peminjaman::whereHas('mobil', function ($query) use ($request) {
                return $query->where('nomor_plat', $request->nomor_plat);
            })->where('user_id', auth()->id())->first();

            if (!$peminjaman) {
                return Helper::responseError('Anda tidak menyewa mobil dengan nomor plat ' . $request->nomor_plat);
            }
            $pengembalian = Pengembalian::where('peminjaman_id', $peminjaman->id)->first();

            if ($pengembalian) {
                return Helper::responseError('Mobil Sudah Dikembalikan');
            }
            if (now() < $peminjaman->tgl_mulai) {
                return Helper::responseError("Belum Masuk Tanggal Penyewaan");
            }
            $jumlahHari = Helper::diffTime($peminjaman->tgl_mulai, date("d-m-Y"));
            $biaya = $jumlahHari * $peminjaman->mobil->tarif;
            $data = Pengembalian::create([
                "peminjaman_id" =>  $peminjaman->id,
                "biaya"         =>  $biaya,
                "user_id"       =>  auth()->id(),
            ]);
            return Helper::responseOK('Berhasil menambah data', $data);
        } catch (\Throwable $th) {
            //throw $th;
            Log::error($th);
            return Helper::responseError($th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $data = Pengembalian::with('peminjaman')->find($id);
            if (!$data) {
                return Helper::responseError('Data tidak ditemukan', [], 404);
            }
            if ($data->user_id != auth()->id()) {
                return Helper::responseError('Anda Tidak Memiliki Akses', [], 401);
            }
            return Helper::responseOK('Berhasil mengambil data', $data);
        } catch (\Throwable $th) {
            Log::error($th);
            return Helper::responseError($th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'peminjaman_id' => 'required',
            'biaya'         => 'required',
        ]);

        try {
            $data = Pengembalian::find($id);
            if (!$data) {
                return Helper::responseError('Data tidak ditemukan', [], 404);
            }
            if ($data->user_id != auth()->id()) {
                return Helper::responseError('Anda Tidak Memiliki Akses', [], 401);
            }
            $data->update([
                "peminjaman_id" =>  $request->input("peminjaman_id"),
                "biaya"         =>  $request->input("biaya"),
            ]);
            return Helper::responseOK('Berhasil mengubah data', $data);
        } catch (\Throwable $th) {
            Log::error($th);
            return Helper::responseError($th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $data = Pengembalian::find($id);
            if (!$data) {
                return Helper::responseError('Data tidak ditemukan', [], 404);
            }
            if ($data->user_id != auth()->id()) {
                return Helper::responseError('Anda Tidak Memiliki Akses', [], 401);
            }
            $data->delete();
            return Helper::responseOK('Berhasil menghapus data');
        } catch (\Throwable $th) {
            Log::error($th);
            return Helper::responseError($th->getMessage());
        }
    }
}
