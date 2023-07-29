<?php

namespace App\Http\Controllers\API;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Mobil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MobilController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $data = Mobil::where(function ($query) use ($request) {
                $query->where('merek', 'ilike', '%' . $request->search . '%')
                    ->orWhere('model', 'ilike', '%' . $request->search . '%');
            });

            if ((isset($request->mulai) && !isset($request->selesai)) || (!isset($request->mulai) && isset($request->selesai))) {
                return Helper::responseError("Tanggal mulai dan tanggal selesai tidak boleh kosong");
            }
            if (isset($request->mulai) && isset($request->selesai)) {
                $data = $data->whereDoesntHave('peminjaman', function ($query) use ($request) {
                    $query->whereBetween('tgl_mulai', [$request->mulai, $request->selesai])
                        ->orWhereBetween('tgl_selesai', [$request->mulai, $request->selesai]);
                });
            } 
            $data = $data->get();
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
        $validated = $request->validate([
            'merek'         => 'required',
            'model'         => 'required',
            'nomor_plat'    => 'required|unique:mobils',
            'tarif'         => 'required',
        ]);

        try {
            $data = Mobil::create([
                "merek"         =>  $request->input("merek"),
                "model"         =>  $request->input("model"),
                "nomor_plat"    =>  $request->input("nomor_plat"),
                "tarif"         =>  $request->input("tarif"),
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
            $data = Mobil::find($id);
            if (!$data) {
                return Helper::responseError('Data tidak ditemukan', [], 404);
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
            'merek'         => 'required',
            'model'         => 'required',
            'nomor_plat'    => 'required',
            'tarif'         => 'required',
        ]);
        try {
            $data = Mobil::find($id);
            if (!$data) {
                return Helper::responseError('Data tidak ditemukan', [], 404);
            }
            if ($data->user_id != auth()->id()) {
                return Helper::responseError('Anda Tidak Memiliki Akses', [], 401);
            }
            $data->update([
                "merek"         =>  $request->input("merek"),
                "model"         =>  $request->input("model"),
                "nomor_plat"    =>  $request->input("nomor_plat"),
                "tarif"         =>  $request->input("tarif"),
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
            $data = Mobil::find($id);
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

    public function getByUser(Request $request)
    {
        try {
            $data = Mobil::where(function ($query) use ($request) {
                $query->where('merek', 'ilike', '%' . $request->search . '%')
                    ->orWhere('model', 'ilike', '%' . $request->search . '%');
            })->where('user_id', auth()->id())
                ->get();
            return Helper::responseOK('Berhasil mendapatkan data', $data);
        } catch (\Throwable $th) {
            Log::error($th);
            return Helper::responseError($th->getMessage());
        }
    }
}
