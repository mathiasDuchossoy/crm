<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 23/08/2016
 * Time: 13:55
 */

namespace Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity;

/**
 * Class Type
 * @package Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity
 */
class ModeAffectation
{
    const Station = 1; // Station uniquement qui sont composées de produits
    const Fournisseur = 2; // Fournisseurs d'hébergements

    public static $libelles = array(
        ModeAffectation::Station => 'Station',
        ModeAffectation::Fournisseur => 'Fournisseur'
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
     * @param $type
     * @return mixed
     */
    public function getType($type)
    {
        return constant($type);
    }

    /**
     * @return int
     */
    public function getDefaultValue()
    {
        return $this::Station;
    }
}