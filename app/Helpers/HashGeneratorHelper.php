<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class HashGeneratorHelper{

    public static function hashGenerator($total,$installmentsNumber)
    {
        $currency_code = getenv('CURRENCY_CODE');
        $merchant_key = getenv('MERCHANT_KEY');
        $invoice_id = Str::random(15);
        $app_secret = getenv('APP_SECRET');
        $data = $total . '|' . $installmentsNumber . '|' . $currency_code . '|' . $merchant_key . '|' . $invoice_id;

        Session::put('invoice_id',$invoice_id);

        $iv = substr(sha1(mt_rand()), 0, 16);
        $password = sha1($app_secret);

        $salt = substr(sha1(mt_rand()), 0, 4);
        $saltWithPassword = hash('sha256', $password . $salt);

        $encrypted = openssl_encrypt(
            $data, 'aes-256-cbc', $saltWithPassword, 0, $iv
        );
        $msg_encrypted_bundle = $iv . ':' . $salt . ':' . $encrypted;
        $msg_encrypted_bundle = str_replace('/', '__', $msg_encrypted_bundle);
        return $msg_encrypted_bundle;
    }

}
