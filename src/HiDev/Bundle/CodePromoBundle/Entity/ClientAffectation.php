<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 29/08/2016
 * Time: 14:32
 */

namespace HiDev\Bundle\CodePromoBundle\Entity;


class ClientAffectation
{
    const tous = 1;
    const existants = 2;

    public static $libelles = array(
        ClientAffectation::tous => 'Tout le monde',
        ClientAffectation::existants => 'Clients existants',
    );

    static public function getLibelle($id)
    {
        return self::$libelles[$id];
    }
}