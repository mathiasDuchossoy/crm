<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 29/08/2016
 * Time: 14:32
 */

namespace Mondofute\Bundle\CodePromoBundle\Entity;


class Application
{
    const logement = 1;
    const prestationAnnexe = 2;
    const fraisDeDossier = 3;

    public static $libelles = array(
        Application::logement => 'Logement',
        Application::prestationAnnexe => 'Prestations annexe',
        Application::fraisDeDossier => 'Frais de dossier',
    );

    static public function getLibelle($id)
    {
        return self::$libelles[$id];
    }
}