<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 28/11/2016
 * Time: 11:51
 */

namespace Mondofute\Bundle\FournisseurBundle\Entity;


class Priorite
{
    const NC = 0; // 1
    const priorite1 = 1; // 10
    const priorite2 = 2; // 10
    const priorite3 = 3; // 10

    public static $libelles = array(
        Priorite::NC => 'NC',
        Priorite::priorite1 => 'Priorite 1',
        Priorite::priorite2 => 'Priorite 2',
        Priorite::priorite3 => 'Priorite 3'
    );

    static public function getLibelle($libelle)
    {
        return self::$libelles[$libelle];
    }
}