<?php

namespace App\Libs;

use DateTime;

class Helper
{


    /**
     * convert eng numbers to persian numbers
     *
     * @param  mixed $srting
     * @return srting
     */
    public static function faNum($srting)
    {
        $en_num = ['0','1','2','3','4','5','6','7','8','9'];
        $fa_num = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];

        return str_replace($en_num, $fa_num, $srting);
    }

    /**
     * convert persian numbers to eng numbers
     *
     * @param  mixed $srting
     * @return srting
     */
    public static function enNum($srting)
    {
        $en_num = ['0','1','2','3','4','5','6','7','8','9'];
        $fa_num = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];

        return str_replace($fa_num, $en_num, $srting);
    }



    /**
     * unset null values function
     *
     * @param Array $items
     * @return Array
     */
    public static function removeArray($items){

        if (!is_array($items)) {
            return $items;
        }

       return collect($items)
            ->reject(function ($items) {
                if (!is_array($items)) {
                    if(strtolower($items) == 'null') return true;
                }

                return is_null($items);
            })
            ->flatMap(function ($items, $key) {

                return is_numeric($key)
                    ? [self::removeArray($items)]
                    : [$key => self::removeArray($items)];
            })
            ->toArray();
    }

    public static function isDate($date){
        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date)) {
            return true;
        } else {
            return false;
        }
    }

    public static function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }

}
