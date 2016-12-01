<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 01/12/2016
 * Time: 11:10
 */

namespace Mondofute\Bundle\PromotionBundle\Entity;


class TypeRemise
{
    const euro = 1;
    const poucentage = 2;

    public static $libelles = array(
        TypeRemise::euro => '€',
        TypeRemise::poucentage => '%',
    );

    static public function getLibelle($id)
    {
        return self::$libelles[$id];
    }
}