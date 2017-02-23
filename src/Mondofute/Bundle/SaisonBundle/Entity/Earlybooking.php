<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 01/12/2016
 * Time: 11:10
 */

namespace Mondofute\Bundle\SaisonBundle\Entity;


class Earlybooking
{
    const pasDEb = 1;
    const ADemander = 2;
    const AParametrer = 3;
    const enFluxXml = 4;

    public static $libelles = array(
        Earlybooking::pasDEb => "Pas d'EB",
        Earlybooking::ADemander => 'À demander',
        Earlybooking::AParametrer => 'À paramétrer',
        Earlybooking::enFluxXml => 'En flux XML',
    );

    static public function getLibelle($id)
    {
        return self::$libelles[$id];
    }
}