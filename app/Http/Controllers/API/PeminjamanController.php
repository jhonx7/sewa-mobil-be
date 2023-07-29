<?php

namespace App\Http\Controllers\API;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PeminjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = Peminjaman::where('user_id', auth()->id())->with('mobil')->get();
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
            'tgl_mulai'     => 'required',
            'tgl_selesai'   => 'required',
            'mobil_id'      => 'required',
        ]);

        try {
            $peminjaman = Peminjaman::where('mobil_id', $request->input("mobil_id"))
                ->where(function ($query) use ($request) {
                    $query->whereBetween('tgl_mulai', [$request->input("tgl_mulai"), $request->input("tgl_selesai")])
                        ->orWhereBetween('tgl_selesai', [$request->input("tgl_mulai"), $request->input("tgl_selesai")]);
                })
                ->first();

            if ($peminjaman) {
                return Helper::responseError('Mobil sedang disewa pada tanggal tersebut');
            }
            $data = Peminjaman::create([
                "tgl_mulai"     =>  $request->input("tgl_mulai"),
                "tgl_selesai"   =>  $request->input("tgl_selesai"),
                "mobil_id"      =>  $request->input("mobil_id"),
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
            $data = Peminjaman::with('mobil')->find($id);
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
            'tgl_mulai'     => 'required',
            'tgl_selesai'   => 'required',
            'mobil_id'      => 'required',
        ]);

        try {
            $data = Peminjaman::find($id);
            if (!$data) {
                return Helper::responseError('Data tidak ditemukan', [], 404);
            }
            if ($data->user_id != auth()->id()) {
                return Helper::responseError('Anda Tidak Memiliki Akses', [], 401);
            }
            $data->update([
                "tgl_mulai"     =>  $request->input("tgl_mulai"),
                "tgl_selesai"   =>  $request->input("tgl_selesai"),
                "mobil_id"      =>  $request->input("mobil_id"),
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
            $data = Peminjaman::find($id);
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
