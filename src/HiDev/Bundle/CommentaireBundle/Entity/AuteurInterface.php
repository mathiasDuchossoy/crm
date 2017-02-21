<?php
/**
 * Created by PhpStorm.
 * User: Stephane
 * Date: 05/01/2017
 * Time: 09:56
 */

namespace HiDev\Bundle\CommentaireBundle\Entity;


interface AuteurInterface
{
    public function getCommentaires();

    public function addCommentaire($commentaire);

    public function removeCommentaire($commentaire);
}