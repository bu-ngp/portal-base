<?php

namespace domain\helpers;

class DateHelper
{
    public static function iso2rus($value)
    {
        $result = preg_replace('/(\d{4})-(\d{2})-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/', '$3.$2.$1 $5:$6:$7', $value);
        $result = preg_replace('/(\d{4})-(\d{2})-(\d{2})/', '$3.$2.$1', $value);

        return $result;
    }

    public static function rus2iso($value)
    {
        $result = preg_replace('/(\d{2}).(\d{2}).(\d{4})\s(\d{2}):(\d{2}):(\d{2})/', '$3-$2-$1 $5:$6:$7', $value);
        $result = preg_replace('/(\d{2}).(\d{2}).(\d{4})/', '$3-$2-$1', $value);

        return $result;
    }
}