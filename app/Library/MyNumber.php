<?php
namespace App\Library;

use Illuminate\Support\Facades\DB;

class MyNumber {
    public static function terbilang_get_valid($str, $from, $to, $min = 1, $max = 9) {
        $val = false;
        $from = ($from < 0) ? 0 : $from;
        for ($i = $from; $i < $to; $i++) {
            if (((int) $str{$i} >= $min) && ((int) $str{$i} <= $max))
                $val = true;
        }
        return $val;
    }

    public static function terbilang_get_str($i, $str, $len) {
        $numA = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan");
        $numB = array("", "se", "dua ", "tiga ", "empat ", "lima ", "enam ", "tujuh ", "delapan ", "sembilan ");
        $numC = array("", "satu ", "dua ", "tiga ", "empat ", "lima ", "enam ", "tujuh ", "delapan ", "sembilan ");
        $numD = array(0 => "puluh", 1 => "belas", 2 => "ratus", 4 => "ribu", 7 => "juta", 10 => "milyar", 13 => "triliun");
        $buf = "";
        $pos = $len - $i;
        switch ($pos) {
            case 1:
                if (!self::terbilang_get_valid($str, $i - 1, $i, 1, 1))
                    $buf = $numA[(int) $str{$i}];
                break;
            case 2: case 5: case 8: case 11: case 14:
                if ((int) $str{$i} == 1) {
                    if ((int) $str{$i + 1} == 0)
                        $buf = ($numB[(int) $str{$i}]) . ($numD[0]);
                    else
                        $buf = ($numB[(int) $str{$i + 1}]) . ($numD[1]);
                }
                else if ((int) $str{$i} > 1) {
                    $buf = ($numB[(int) $str{$i}]) . ($numD[0]);
                }
                break;
            case 3: case 6: case 9: case 12: case 15:
                if ((int) $str{$i} > 0) {
                    $buf = ($numB[(int) $str{$i}]) . ($numD[2]);
                }
                break;
            case 4: case 7: case 10: case 13:
                if (self::terbilang_get_valid($str, $i - 2, $i)) {
                    if (!self::terbilang_get_valid($str, $i - 1, $i, 1, 1))
                        $buf = $numC[(int) $str{$i}] . ($numD[$pos]);
                    else
                        $buf = $numD[$pos];
                }
                else if ((int) $str{$i} > 0) {
                    if ($pos == 4)
                        $buf = ($numB[(int) $str{$i}]) . ($numD[$pos]);
                    else
                        $buf = ($numC[(int) $str{$i}]) . ($numD[$pos]);
                }
                break;
        }
        return $buf;
    }

    public static function toTerbilang($nominal) {
        $buf = "";
        $str = $nominal . "";
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $buf = trim($buf) . " " . self::terbilang_get_str($i, $str, $len);
        }
        return ucwords(trim($buf));
    }

    public static function toNumber($str){
        $str = str_replace(array(".", ","), array("", "."), $str);
        return $str*1;
    }
    
    public static function toString($str, $decimal=2){
        return number_format($str, $decimal, ",", ".");
    }
    
    public static function toReadableHarga($str, $withSpan = TRUE) {
        return ($withSpan ? "<span style='display:none'>" . ($str < 0 ? "-" : "+") . str_pad(abs($str), 10, "0", STR_PAD_LEFT) . "</span>" : "") . "Rp. " . number_format($str, 0, ",", ".");
//        return ($withSpan ? "<span style='display:none'>" . ($str < 0 ? "-" : "+") . str_pad(abs($str), 10, "0", STR_PAD_LEFT) . "</span>" : "") . "Rp " . number_format($str, 0, ".", ",");
    }

    public static function toReadableAngka($str, $withSpan = TRUE) {
//        return ($withSpan ? "<span style='display:none'>" . ($str < 0 ? "-" : "+") . str_pad(abs($str), 10, "0", STR_PAD_LEFT) . "</span>" : "") . number_format($str, 0, ",", ".");
        return ($withSpan ? "<span style='display:none'>" . ($str < 0 ? "-" : "+") . str_pad(abs($str), 10, "0", STR_PAD_LEFT) . "</span>" : "") . number_format($str, 0, ",", ".");
    }

    public static function randomKey(){
        return date("ymds").$this->randomNumber(4);
    }
    
    public static function randomNumber($length) {
        $result = '';
        for($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }
        return $result;
    }
}