<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 29/08/2016
 * Time: 14:33
 */

namespace HiDev\Bundle\CodePromoBundle\Entity;


class TypeRemise
{
    const euro = 1;
    const poucentage = 2;

    public static $libelles = array(
        TypeRemise::euro      => '€',
        TypeRemise::poucentage => '%',
    );

    static public function getLibelle($id)
    {
        return self::$libelles[$id];
    }
}