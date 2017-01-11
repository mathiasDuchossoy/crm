<?php

namespace Mondofute\Bundle\PromotionBundle\Entity;

/**
 * TypeAffectation
 */
class TypeAffectation
{
    const logement = 1;
    const prestationAnnexe = 2;
    const type = 3;

    public static $libelles = array(
        TypeAffectation::logement => 'Logement',
        TypeAffectation::prestationAnnexe => 'Prestations annexe',
        TypeAffectation::type => 'Type de fournisseur',
    );

    static public function getLibelle($id)
    {
        return self::$libelles[$id];
    }
}

