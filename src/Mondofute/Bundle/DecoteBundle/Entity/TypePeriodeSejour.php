<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 01/12/2016
 * Time: 11:10
 */

namespace Mondofute\Bundle\DecoteBundle\Entity;


class TypePeriodeSejour
{
    const permanent = 1;
    const dateADate = 2;
    const periode = 3;

    public static $libelles = array(
        TypePeriodeSejour::permanent => 'Permanent',
        TypePeriodeSejour::dateADate => 'Date à date',
        TypePeriodeSejour::periode => 'Période',
    );

    static public function getLibelle($id)
    {
        return self::$libelles[$id];
    }
}