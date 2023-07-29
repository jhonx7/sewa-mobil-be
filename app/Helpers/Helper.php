<?php

namespace App\Helpers;

use DateTime;

class Helper
{
    public static function apiJsonFormat($message = null, $data = [])
    {
        $jsonFormat = [
            'message' => $message,
            'data' => $data,
        ];

        return $jsonFormat;
    }
    public static function responseOK($message = null, $data = [], $responseCode = 200)
    {
        $attribute = Helper::apiJsonFormat($message, $data);

        return response()->json($attribute, $responseCode);
    }

    public static function responseError($message = null, $data = [], $responseCode = 400)
    {
        $attribute = Helper::apiJsonFormat($message, $data);

        return response()->json($attribute, $responseCode);
    }
    public static function diffTime($fdate, $tdate)
    {
        $tgl1 = strtotime($fdate);
        $tgl2 = strtotime($tdate);

        $jarak = $tgl2 - $tgl1;

        $hari = $jarak / 60 / 60 / 24;
        return $hari + 1;
    }
}
