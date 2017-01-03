<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 01/12/2016
 * Time: 11:10
 */

namespace Mondofute\Bundle\DecoteBundle\Entity;


class TypeApplication
{
    const prixLogement = 1;
    const ALaPersonne = 2;

    public static $libelles = array(
        TypeApplication::prixLogement => 'prix logement',
        TypeApplication::ALaPersonne => 'A la personne',
    );

    static public function getLibelle($id)
    {
        return self::$libelles[$id];
    }
}