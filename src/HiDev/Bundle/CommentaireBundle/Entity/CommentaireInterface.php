<?php
/**
 * Created by PhpStorm.
 * User: Stephane
 * Date: 05/01/2017
 * Time: 09:55
 */

namespace HiDev\Bundle\CommentaireBundle\Entity;


interface CommentaireInterface
{
    public function getAuteur();

    public function setAuteur($auteur);
}