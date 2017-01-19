<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 01/12/2016
 * Time: 11:10
 */

namespace Mondofute\Bundle\SaisonBundle\Entity;


class Flux
{
    const fluxXml = 1;
    const extranet = 2;
    const fluxXmlArkiane = 3;
    const fluxXmlHomeResa = 4;
    const fluxXmlResalys = 5;

    public static $libelles = array(
        Flux::fluxXml => 'Flux XML',
        Flux::extranet => 'Extranet',
        Flux::fluxXmlArkiane => 'Flux XML Arkiane',
        Flux::fluxXmlHomeResa => 'Flux XML HomeResa',
        Flux::fluxXmlResalys => 'Flux XML Resalys'
    );

    static public function getLibelle($id)
    {
        return self::$libelles[$id];
    }
}