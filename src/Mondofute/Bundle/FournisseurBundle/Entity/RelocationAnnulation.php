<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 24/10/2016
 * Time: 11:03
 */

namespace Mondofute\Bundle\FournisseurBundle\Entity;


class RelocationAnnulation
{
    const nsp = 1;
    const oui = 2;
    const non = 3;
    const casParCas = 4;

    public static $libelles = array(
        RelocationAnnulation::nsp => 'NSP',
        RelocationAnnulation::oui => 'Oui',
        RelocationAnnulation::non => 'Non',
        RelocationAnnulation::casParCas => 'Cas par cas'
    );

    static public function getLibelle($permission)
    {
        return self::$libelles[$permission];
    }
}