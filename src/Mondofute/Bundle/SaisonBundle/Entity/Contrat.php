<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 01/12/2016
 * Time: 11:10
 */

namespace Mondofute\Bundle\SaisonBundle\Entity;


class Contrat
{
    const aFaire = 1;
    const demande = 2;
    const attenteRetour = 3;
    const valide = 4;
    const resilie = 5;
    const prospect = 6;

    public static $libelles = array(
        Contrat::aFaire => 'À faire',
        Contrat::demande => 'Demandé',
        Contrat::attenteRetour => 'Attente retour',
        Contrat::valide => 'Validé',
        Contrat::resilie => 'Résilié',
        Contrat::prospect => 'Prospect'
    );

    static public function getLibelle($id)
    {
        return self::$libelles[$id];
    }
}