<?php
namespace App\Library;

class MyString {

    public static function addZero($num, $length) {
        $num_padded = sprintf("%0" . $length . "d", $num);
        return $num_padded;
    }

    public static function seoUrl($string) {
        //Lower case everything
        $string = strtolower($string);
        //Make alphanumeric (removes all other characters)
        $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
        //Clean up multiple dashes or whitespaces
        $string = preg_replace("/[\s-]+/", " ", $string);
        //Convert whitespaces and underscore to dash
        $string = preg_replace("/[\s_]/", "-", $string);
        return $string;
    }

    public static function generateRandomString($length = 10, $full = FALSE, $upper = FALSE) {
        if ($full == TRUE) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            if ($upper == TRUE)
                $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        } else
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function generateRandomNumber($length = 10) {
//        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function replacer($str, $data) {
        // o = origin, r = replacement
        /*
          $data = array(
          array('o'=>'Txt1', 'r'=>'Txt2'),
          array('o'=>'Txt3', 'r'=>'Txt4')
          );
         */

        foreach ($data as $d) {
            $str = str_replace($d['o'], $d['r'], $str);
        }
        return $str;
    }

    public static function removeImage($content) {
        $content = preg_replace("/<img[^>]+\>/i", "(image) ", $content);
        return $content;
    }

    public static function base64_to_img($data, $output_file, $dir = "wb") {
//        $ifp = fopen($output_file, $dir);
//
//        $data = explode(',', $base64_string);
//
//        fwrite($ifp, base64_decode($data[1]));
//        fclose($ifp);
//
//        return $output_file;
        list($type, $data) = explode(';', $data);
        list(, $data) = explode(',', $data);
        $data = base64_decode($data);

        switch ($type) {
            case "data:image/png": $type_str = ".png";
                break;
            case "data:image/jpeg": case "data:image/jpg": $type_str = ".jpg";
                break;
            case "data:image/gif": $type_str = ".gif";
                break;
        }

        file_put_contents($dir . $output_file . $type_str, $data);
        $data_file = array();
        $data_file['name'] = $output_file . $type_str;
        $data_file['type'] = $type;
        return $data_file;
    }

    function getInitial($name) {
        list ($first, $last) = explode(' ', $name, 2);
        return "$first {$last[0]}.";
    }

    public static function toSummary($content, $length = 70) {
        return substr(strip_tags(self::convertSmartQuotes(self::removeImage($content))), 0, $length) . " ...";
    }

    public static function validateEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            return false;
        else
            return true;
    }

    public static function clean($string) {
        $string = strtolower(trim($string)); // Replaces all spaces with hyphens.
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        // $string = str_replace(':', '-', $string); // Replaces all : with hyphens.
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }

    public static function getDuration($videoID){
       $apikey = "AIzaSyD8SxFSA6OCZ2q2tsr4ThV5vc2bMQK7tUI"; // Like this AIcvSyBsLA8znZn-i-aPLWFrsPOlWMkEyVaXAcv
       $dur = file_get_contents("https://www.googleapis.com/youtube/v3/videos?part=contentDetails&id=". $videoID ."&key=".$apikey);
       $VidDuration =json_decode($dur, true);
       foreach ($VidDuration['items'] as $vidTime)
       {
           $VidDuration= $vidTime['contentDetails']['duration'];
       }
       preg_match_all('/(\d+)/',$VidDuration,$parts);
       $str_part = "";
       $c = 0;

       $str = "";
       $formated_stamp = str_replace(array("PT","H","M","S"), array("",":",":",""), $VidDuration);
       $exploded = explode(":", $formated_stamp);

       foreach ($exploded as $exp) {
            if($c>0) 
                $str .= ":". str_pad($exp, 2, "0", STR_PAD_LEFT);
            else
                $str .= $exp;

            $c++;
       }

       return $str;
       
       // foreach ($parts as $pt) {
       //      if($c==0)
       //          $str_part .= $parts[0][$c];
       //      else
       //          $str_part .= ":". $parts[0][$c];
       //          // $str_part .= (sizeof($parts[0]) >= $c) ? ":". $parts[0][$c] : ":00";
       //      $c++;
       // }
       // // return $parts[0][0].":".$parts[0][1].":".$parts[0][2]; // Return 1:11:46 (i.e) HH:MM:SS
       // echo json_encode($str_part); exit;
       // return $str_part;
    }

    public static function convertSmartQuotes($string) 
    { 
        $search = array(chr(145), 
                        chr(146), 
                        chr(147), 
                        chr(148), 
                        chr(151)); 

        $replace = array("'", 
                         "'", 
                         '"', 
                         '"', 
                         '-'); 

        // return str_replace($search, $replace, $string); 

        return iconv('UTF-8', 'ASCII//TRANSLIT', str_replace("", "", $string));
    } 

}
