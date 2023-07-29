<?php

namespace App\Http\Controllers\API;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ], [
            '*.required'  => ':attribute tidak boleh kosong.'
        ]);
        if ($validation->fails()) {
            return Helper::responseError('Data Tidak Sesuai', $validation->errors());
        }

        try {
            $user = User::where('email', $request->email)->first();

            if ($user !== null) {
                if (Hash::check($request->password, $user->password)) {
                    $token = $user->createToken($user->id)->plainTextToken;
                    $data = [
                        'user' => $user,
                        'token' => $token,
                    ];
                    return Helper::responseOK('Berhasil Masuk!', $data);
                } else {
                    return Helper::responseError('Maaf, username atau password yang Anda masukkan salah.', []);
                }
            } else {
                return Helper::responseError('Maaf, username atau password yang Anda masukkan salah.', []);
            }
        } catch (\Throwable $th) {
            //throw $th;
            Log::error($th);
            return Helper::responseError($th->getMessage());
        }
    }

    public function register(Request $request)
    {
        // $request->validate([
        //     'name' => ['required', 'string', 'max:255'],
        //     'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
        //     'password' => ['required'],
        // ]);
        $validation = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'address' => ['required'],
            'phone' => ['required'],
            'license' => ['required'],
            'password' => ['required'],
        ], [
            '*.required'  => ':attribute tidak boleh kosong.'
        ]);
        if ($validation->fails()) {
            return Helper::responseError('Data Tidak Boleh Kosong', $validation->errors());
        }

        try {

            $user = User::create([
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'license' => $request->license,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);


            // Auth::login($user);
            $token = $user->createToken($user->id)->plainTextToken;
            $data = [
                'user' => $user,
                'token' => $token,
            ];
            return Helper::responseOK('Berhasil Mendaftar!', $data);
        } catch (\Throwable $th) {
            //throw $th;
            Log::error($th);
            return Helper::responseError($th->getMessage());
        }
    }

}
