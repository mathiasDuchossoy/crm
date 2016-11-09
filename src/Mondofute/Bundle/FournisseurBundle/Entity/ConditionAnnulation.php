<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 24/10/2016
 * Time: 10:59
 */

namespace Mondofute\Bundle\FournisseurBundle\Entity;


class ConditionAnnulation
{
    const standard = 1;
    const personnalisee = 2;

    public static $libelles = array(
        ConditionAnnulation::standard => 'Standard',
        ConditionAnnulation::personnalisee => 'Personnalisée'
    );

    static public function getLibelle($permission)
    {
        return self::$libelles[$permission];
    }

}