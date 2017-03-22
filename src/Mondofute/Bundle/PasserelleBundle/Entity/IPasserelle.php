<?php
/**
 * Created by PhpStorm.
 * User: Stephane
 * Date: 28/02/2017
 * Time: 09:50
 */

namespace Mondofute\Bundle\PasserelleBundle\Entity;


interface IPasserelle
{
    public function getCatalogue();

    public function getStocks();

    public function getTarifs();
}