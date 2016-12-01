<?php

namespace Mondofute\Bundle\PromotionBundle\Entity;

/**
 * TypeAffectation
 */
class TypeAffectation
{
    const logement = 1;
    const prestationAnnexe = 2;

    public static $libelles = array(
        TypeAffectation::logement => 'Logement',
        TypeAffectation::prestationAnnexe => 'Prestations annexe',
    );

    static public function getLibelle($id)
    {
        return self::$libelles[$id];
    }
}

