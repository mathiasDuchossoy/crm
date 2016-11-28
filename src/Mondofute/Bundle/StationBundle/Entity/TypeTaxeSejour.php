<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 28/11/2016
 * Time: 15:15
 */

namespace Mondofute\Bundle\StationBundle\Entity;


class TypeTaxeSejour
{
    const prix = 1;
    const pasDeTaxe = 2;
    const NC = 3;

    public static $libelles = array(
        TypeTaxeSejour::prix => 'Prix',
        TypeTaxeSejour::pasDeTaxe => 'Pas de taxe',
        TypeTaxeSejour::NC => 'NC'
    );

    static public function getLibelle($libelle)
    {
        return self::$libelles[$libelle];
    }
}