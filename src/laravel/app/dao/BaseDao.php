<?php

namespace Dao;


use Utility as utility;

class BaseDao
{
   public function getDateFormat($date)
   {
      $dataFormat = new utility\DataFormat();
      $formatDate = $dataFormat->nullDataStringChange($date);

      return $formatDate;
   }

   public function putDateFormat($date)
   {
      $dataFormat = new utility\DataFormat();
      $formatDate = $dataFormat->nullDataStringChange($date);

      return $formatDate;
   }

   public function nullChangeString($date)
   {
      $dataFormat = new utility\DataFormat();
      $formatDate = $dataFormat->nullDataStringChange($date);

      return $formatDate;
   }

   public function colorInitSet($id, $type)
   {
      $checkNumber = (int) substr($id, -1);
      $initColor = collect([]);
      switch ($type) {
         case "user":
            $initColor->push(array('color' => '#c3f4c0', 'textColor' => '#008822', 'borderColor' => '#c3f4c0'));
            $initColor->push(array('color' => '#a4d2ff', 'textColor' => '#002371', 'borderColor' => '#a4d2ff'));
            $initColor->push(array('color' => '#eed7ee', 'textColor' => '#661b4e', 'borderColor' => '#eed7ee'));
            $initColor->push(array('color' => '#ffd7c6', 'textColor' => '#8e0000', 'borderColor' => '#ffd7c6'));
            $initColor->push(array('color' => '#e7cc9f', 'textColor' => '#b48b16', 'borderColor' => '#e7cc9f'));
            $initColor->push(array('color' => '#002371', 'textColor' => '#a4d2ff', 'borderColor' => '#002371'));
            $initColor->push(array('color' => '#008822', 'textColor' => '#c3f4c0', 'borderColor' => '#008822'));
            $initColor->push(array('color' => '#8e0000', 'textColor' => '#e7d1ec', 'borderColor' => '#8e0000'));
            $initColor->push(array('color' => '#b48b16', 'textColor' => '#e7cc9f', 'borderColor' => '#b48b16'));
            $initColor->push(array('color' => '#661b4e', 'textColor' => '#eed7ee', 'borderColor' => '#661b4e'));
         break;

         case "group":
            $initColor->push(array('color' => '#002371', 'textColor' => '#a4d2ff', 'borderColor' => '#002371'));
            $initColor->push(array('color' => '#008822', 'textColor' => '#c3f4c0', 'borderColor' => '#008822'));
            $initColor->push(array('color' => '#8e0000', 'textColor' => '#e7d1ec', 'borderColor' => '#8e0000'));
            $initColor->push(array('color' => '#b48b16', 'textColor' => '#e7cc9f', 'borderColor' => '#b48b16'));
            $initColor->push(array('color' => '#661b4e', 'textColor' => '#eed7ee', 'borderColor' => '#661b4e'));
            $initColor->push(array('color' => '#c3f4c0', 'textColor' => '#008822', 'borderColor' => '#c3f4c0'));
            $initColor->push(array('color' => '#a4d2ff', 'textColor' => '#002371', 'borderColor' => '#a4d2ff'));
            $initColor->push(array('color' => '#eed7ee', 'textColor' => '#661b4e', 'borderColor' => '#eed7ee'));
            $initColor->push(array('color' => '#ffd7c6', 'textColor' => '#8e0000', 'borderColor' => '#ffd7c6'));
            $initColor->push(array('color' => '#e7cc9f', 'textColor' => '#b48b16', 'borderColor' => '#e7cc9f'));
            break;

         case "tab":
            $initColor->push(array('color' => '#00ffc0', 'textColor' => '#132b15', 'borderColor' => '#00ffc0'));
            $initColor->push(array('color' => '#d1ecea', 'textColor' => '#000083', 'borderColor' => '#d1ecea'));
            $initColor->push(array('color' => '#e4ecd1', 'textColor' => '#6a7c00', 'borderColor' => '#e4ecd1'));
            $initColor->push(array('color' => '#ecdad1', 'textColor' => '#c6a400', 'borderColor' => '#ecdad1'));
            $initColor->push(array('color' => '#c3c6ec', 'textColor' => '#550071', 'borderColor' => '#c3c6ec'));
            $initColor->push(array('color' => '#ffd000', 'textColor' => '#6a4d00', 'borderColor' => '#ffd000'));
            $initColor->push(array('color' => '#e7d1ec', 'textColor' => '#6d2b55', 'borderColor' => '#e7d1ec'));
            $initColor->push(array('color' => '#abe3bc', 'textColor' => '#005300', 'borderColor' => '#abe3bc'));
            $initColor->push(array('color' => '#00d0ff', 'textColor' => '#000000', 'borderColor' => '#00d0ff'));
            $initColor->push(array('color' => '#ece8d1', 'textColor' => '#000078', 'borderColor' => '#ece8d1'));
            break;
      }

      return $initColor[$checkNumber];
   }
}
