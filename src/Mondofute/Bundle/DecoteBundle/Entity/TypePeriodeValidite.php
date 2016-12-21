<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 01/12/2016
 * Time: 11:10
 */

namespace Mondofute\Bundle\DecoteBundle\Entity;


class TypePeriodeValidite
{
    const permanent = 1;
    const dateADate = 2;
    const periode = 3;

    public static $libelles = array(
        TypePeriodeValidite::permanent => 'Permanent',
        TypePeriodeValidite::dateADate => 'Date à date',
        TypePeriodeValidite::periode => 'Période',
    );

    static public function getLibelle($id)
    {
        return self::$libelles[$id];
    }
}