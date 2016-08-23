<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 23/08/2016
 * Time: 13:55
 */

namespace Mondofute\Bundle\PrestationAnnexeBundle\Entity;


class Type
{
    const Individuelle = 1;
    const Quantite = 2;
    const Forfait = 3;

    public static $libelles = array(
        Type::Individuelle => 'Individuelle',
        Type::Quantite => 'Quantité',
        Type::Forfait => 'Forfait'
    );


    static public function getLibelle($id)
    {
        return self::$libelles[$id];
    }
}