<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 30/11/2016
 * Time: 10:10
 */

namespace Mondofute\Bundle\FournisseurBundle\Entity;


class FournisseurContient
{
    const PRODUIT = 1; // 1
    const FOURNISSEUR = 2; // 10

    public static $libelles = array(
        FournisseurContient::FOURNISSEUR => 'Fournisseurs',
        FournisseurContient::PRODUIT => 'Produits'
    );

    static public function getLibelle($permission)
    {
        return self::$libelles[$permission];
    }

}