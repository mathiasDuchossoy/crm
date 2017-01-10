<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 23/08/2016
 * Time: 13:55
 */

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity;

/**
 * Class ForfaitQuantiteType
 * @package Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity
 */
class ForfaitQuantiteType
{
    const Fixe = 1;
    const Choix = 2;

    public static $libelles = array(
        ForfaitQuantiteType::Fixe => 'Quantité fixe',
        ForfaitQuantiteType::Choix => 'Quantité au choix'
    );

    /**
     * @param $id
     * @return mixed
     */
    static public function getLibelle($id)
    {
        return self::$libelles[$id];
    }

    /**
     * @param $ForfaitQuantiteType
     * @return mixed
     */
    public function getForfaitQuantiteType($ForfaitQuantiteType)
    {
        return constant($ForfaitQuantiteType);
    }
}