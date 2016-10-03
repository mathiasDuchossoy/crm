<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 23/08/2016
 * Time: 13:55
 */

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity;

/**
 * Class Type
 * @package Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity
 */
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
}