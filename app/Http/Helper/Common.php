<?php

namespace App\Http\Helper;

class Common {

    public static function responseFormat($status = null, $error = null, $data = null) {
        return [
            'status' => $status,
            'error' => $error,
            'data' => $data
        ];
    }

    public static function failResponseFormat($error = null) {
        if (!is_array($error) && !empty($error)) {
            $error = array($error);
        }
        return [
            'status' => 0,
            'error' => $error,
            'data' => null
        ];
    }

    public static function successResponseFormat($data = null) {
        return [
            'status' => 1,
            'error' => null,
            'data' => $data
        ];
    }

}
