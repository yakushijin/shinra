<?php

namespace Utility;

class DataFormat
{

   public function nullDataStringChange($data)
   {
      if (is_null($data)) {
         $data = "";
      }
      return $data;
   }

   public function stgingDataNullChange($data)
   {
      if ($data == "") {
         $data = null;
      }
      return $data;
   }


   //乱数生成用
   public function randGet($length, $type, $dateAddUes)
   {
      $res = null;
      $string_length = $length;
      $smallAlphabet = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
      $bigAlphabet = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
      $symbol = array('*', '-', '!', '%', '?', '#', '$', '&', '_');
      $number = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);

      switch ($type) {
         case "smallAlphabet":
            $base_string = $smallAlphabet;
            break;
         case "bigAlphabet":
            $base_string = $bigAlphabet;
            break;
         case "allAlphabet":
            $base_string = array_merge($smallAlphabet, $bigAlphabet);
            break;
         case "symbol":
            $base_string = $symbol;
            break;
         case "number":
            $base_string = $number;
            break;
         case "all":
            $base_string = array_merge($smallAlphabet, $bigAlphabet, $symbol, $number);
            break;
      }

      for ($i = 0; $i < $string_length; $i++) {
         if ($i === 0) {
            $res .= $base_string[mt_rand(0, count($base_string) - 4)];
         } else {
            $res .= $base_string[mt_rand(0, count($base_string) - 1)];
         }
      }

      switch ($dateAddUes) {
         case "none":
            break;
         case "back":
            $now = \Carbon\Carbon::now();
            $date = date("ymdHis", strtotime($now));
            $res = $res . $date;
            break;
         case "first":
            $now = \Carbon\Carbon::now();
            $date = date("ymdHis", strtotime($now));
            $res =  $date . $res;
            break;
         default:
            break;
      }

      return $res;
   }
}
