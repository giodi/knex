<?php

namespace App;

use DateTime;

class Helpers
{

    /**
     * Credit: https://www.php.net/manual/en/function.checkdate.php#113205
     ** @return bool
     */
    static function dateValid($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    /**
     * @return array
     */
    static function buildTree(array $elements, string $parentId): array
    {

        $branch = [];

        foreach ($elements as $key => $el) {
            if($el['ark_parent']['value'] === $parentId){
                $el['name'] = $el['name_child']['value'];
                $el['ark'] = $el['ark_child']['value'];

                $children = self::buildTree($elements, $el['ark_child']['value']);
                if($children){
                    $el['children'] = $children;
                }else{
                    $el['value'] = 1;
                }

                unset($el['ark_child'], $el['ark_parent'], $el['name_child']);

                $branch[] = $el;
            }
        }

        return $branch;

    }



}
