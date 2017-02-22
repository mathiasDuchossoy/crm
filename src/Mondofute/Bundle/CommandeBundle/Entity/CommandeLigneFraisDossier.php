<?php

namespace Mondofute\Bundle\CommandeBundle\Entity;

/**
 * CommandeLigneFraisDossier
 */
class CommandeLigneFraisDossier extends CommandeLigne
{

    public function __construct()
    {
        parent::__construct();
        $this->setPrixCatalogue(9);
    }
}

