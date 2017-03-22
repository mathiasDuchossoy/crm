<?php
/**
 * Created by PhpStorm.
 * User: Stephane
 * Date: 28/02/2017
 * Time: 10:00
 */

namespace Mondofute\Bundle\PasserelleBundle\Entity;


use Mondofute\FournisseurBundle\Entity\Fournisseur;

abstract class PasserelleFactory
{
    static public function init(Fournisseur $fournisseur)
    {
        if (!empty($fournisseur->getParamPasserelle())) {
            dump($fournisseur->getParamPasserelle());
            try {
                $fournisseur->getParamPasserelle()->getStocks();
            } catch (\Exception $e) {
                dump($e->getMessage());
            }
            try {
//            dump($fournisseur->getPasserelle());
                $fournisseur->getParamPasserelle()->getCatalogue();
            } catch (\Exception $e) {
                dump($e->getMessage());
            }
            try {
                $fournisseur->getParamPasserelle()->getTarifs();
            } catch (\Exception $e) {
                dump($e->getMessage());
            }
        } else {
            echo 'aucune passerelle associée';
        }
    }

    static public function creer($class)
    {
//        $passerelle = new $class;
    }
}