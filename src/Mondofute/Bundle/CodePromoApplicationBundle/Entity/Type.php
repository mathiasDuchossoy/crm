<?php
/**
 *
 */

namespace Mondofute\Bundle\CodePromoApplicationBundle\Entity;


class Type
{
    const hebergement = 1;
    const fournisseurPrestationAnnexe = 2;

    public static $libelles = array(
        Type::hebergement => 'Hebergement',
        Type::fournisseurPrestationAnnexe => 'Prestations annexe',
    );

    static public function getLibelle($id)
    {
        return self::$libelles[$id];
    }
}