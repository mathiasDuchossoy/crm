<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 09/03/2016
 * Time: 13:15
 */

namespace Mondofute\Bundle\FournisseurBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;

trait FournisseurTrait
{
    function verifContient($contient, Fournisseur $fournisseur)
    {
        return $contient & $fournisseur->getContient();
    }

}