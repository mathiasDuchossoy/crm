<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 16/01/2017
 * Time: 15:46
 */

namespace Mondofute\Bundle\DecoteBundle\Entity;


class Variante
{
    const sejour1Semainex2 = 1;
    const aLaPersonne = 2;
    const produitEnPack = 3;
    const venteFlash = 4;
    const stockSpecifique = 5;

    public static $libelles = array(
        Variante::sejour1Semainex2 => '2 x séjours 1 semaine',
        Variante::aLaPersonne => 'à la personne',
        Variante::produitEnPack => 'sur produits en PACK',
        Variante::venteFlash => 'vente flash',
        Variante::stockSpecifique => 'sur un stock spécifique',
    );

    static public function getLibelle($id)
    {
        return self::$libelles[$id];
    }
}