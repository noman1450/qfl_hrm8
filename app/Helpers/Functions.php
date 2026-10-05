<?php

namespace App\Helpers;

class Functions
{
    public static function number_to_words($number) {
        if (($number < 0) || ($number > 99999999999)) {
            throw new \Exception("Number is out of range");
        }

        $Kt = floor($number / 10000000); /* Koti */
        $number -= $Kt * 10000000;
        $Gn = floor($number / 100000);  /* lakh  */
        $number -= $Gn * 100000;
        $kn = floor($number / 1000);     /* Thousands (kilo) */
        $number -= $kn * 1000;
        $Hn = floor($number / 100);      /* Hundreds (hecto) */
        $number -= $Hn * 100;
        $Dn = floor($number / 10);       /* Tens (deca) */
        $n = $number % 10;               /* Ones */

        $res = "";

        if ($Kt) {
            $res .= static::number_to_words($Kt) . " Crore ";
        }

        if ($Gn) {
            $res .= static::number_to_words($Gn) . " Lakh";
        }

        if ($kn) {
            $res .= (empty($res) ? "" : " ") .static::number_to_words($kn) . " Thousand";
        }

        if ($Hn) {
            $res .= (empty($res) ? "" : " ") .static::number_to_words($Hn) . " Hundred";
        }

        $ones = [
            "", "One", "Two", "Three", "Four", "Five", "Six",
            "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen",
            "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen",
            "Nineteen"
        ];

        $tens = [
            "", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty",
            "Seventy", "Eigthy", "Ninety"
        ];

        if ($Dn || $n) {
            if (!empty($res)) {
                $res .= " and ";
            }

            if ($Dn < 2) {
                $res .= $ones[$Dn * 10 + $n];
            } else {
                $res .= $tens[$Dn];

                if ($n) {
                    $res .= "-" . $ones[$n];
                }
            }
        }

        if (empty($res)) {
            $res = "zero";
        }

        return $res;
    }
    
    public static function sms($toUser, $message)
    {
        //-------
        $message        = urlencode($message);
        $url            = "http://services.smsnet24.com/sendSms?user_id=business3@digitallabbd.com&user_password=01733393711&sms_receiver=$toUser&sms_text=$message";
        $ch             = curl_init();
        $timeout        = 5;
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $server_output = curl_exec($ch);
        curl_close($ch);
    }
    
    static function MobileNumber($mobile_number)
    {
        $Code_Array = array(
            "15" => "TeleTalk",
            "16" => "Airtel",
            "18" => "Robi",
            "17" => "GP",
            "13" => "GP",
            "19" => "Banglalink",
            "14" => "Banglalink",
        );
        $mobile_number_status = true;


        $country_code  = "880";
        $mob_number    = str_replace(' ', '', $mobile_number);
        $code          = substr($mob_number, -10, 2);
        $number        = substr($mob_number, -8, 8);


        if (!array_key_exists($code, $Code_Array)) {
            $mobile_number_status = false;
        }

        $mobile_number = $country_code . $code . $number;

        if (strlen($mobile_number) != 13) {
            $mobile_number_status = false;
        }

        $response = [
            'status'        => $mobile_number_status,
            'mobile_number' => $mobile_number,
        ];
        
        return $response;        
    }
    
    static function response($status = false, $message = '', $error = '', $data)
    {
        $response = [
            'success' => isset($status) ? $status : false,
            'message' => isset($message) ? $message : '',
            'data'    => isset($data) ? $data : '',
            'error'   => isset($error) ? $error : '',
            'error_code' => $status == true ? 200 : 404,
        ];
      	return response()->json($response);
    }    
}
