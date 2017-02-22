<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 16/01/2017
 * Time: 16:56
 */

namespace Mondofute\Bundle\DecoteBundle\Entity;


class ChoixVariante1
{
    const semaineMoinsChereOfferte = 1;
    const appliquerRemise = 2;

    public static $libelles = array(
        ChoixVariante1::semaineMoinsChereOfferte => 'semaine moins chère offerte',
        ChoixVariante1::appliquerRemise => 'appliquer remise',
    );

    static public function getLibelle($id)
    {
        return self::$libelles[$id];
    }
}