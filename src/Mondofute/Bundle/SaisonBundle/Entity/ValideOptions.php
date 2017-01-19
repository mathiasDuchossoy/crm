<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 01/12/2016
 * Time: 11:10
 */

namespace Mondofute\Bundle\SaisonBundle\Entity;


class ValideOptions
{
    const enLigne = 1;
    const aSaisir = 2;
    const pasDOptions = 3;

    public static $libelles = array(
        ValideOptions::enLigne => 'En ligne',
        ValideOptions::aSaisir => 'À saisir',
        ValideOptions::pasDOptions => "Pas d'options",
    );

    static public function getLibelle($id)
    {
        return self::$libelles[$id];
    }
}