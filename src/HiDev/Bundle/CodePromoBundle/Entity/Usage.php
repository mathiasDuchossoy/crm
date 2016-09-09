<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 29/08/2016
 * Time: 14:33
 */

namespace HiDev\Bundle\CodePromoBundle\Entity;


class Usage
{
    const unique = 1;
    const uniqueParPeriode = 2;
    const multiple = 3;

    public static $libelles = array(
        Usage::unique => 'Unique',
        Usage::uniqueParPeriode => 'Unique par période de validité',
        Usage::multiple => 'Multiple',
    );

    static public function getLibelle($id)
    {
        return self::$libelles[$id];
    }
}