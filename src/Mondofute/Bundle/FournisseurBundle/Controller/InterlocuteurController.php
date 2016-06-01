<?php

namespace Mondofute\Bundle\FournisseurBundle\Controller;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\FournisseurBundle\Entity\FournisseurInterlocuteur;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class InterlocuteurController extends Controller
{
    public function newAction()
    {

//        return $this->render('MondofuteFournisseurBundle:Interlocuteur:new.html.twig', array(
//            // ...
//        ));
    }

    public function editAction()
    {
        return $this->render('MondofuteFournisseurBundle:Interlocuteur:edit.html.twig', array(// ...
        ));
    }

    public function newInterlocuteurUsers(Collection $interlocuteurs)
    {
        /** @var FournisseurInterlocuteur $fournisseurInterlocuteur */
        
        foreach ($interlocuteurs as $fournisseurInterlocuteur) {
            $interlocuteur = $fournisseurInterlocuteur->getInterlocuteur();
            $interlocuteurUser = $interlocuteur->getUser();
            $interlocuteurUser->setEnabled(true);

            foreach ($interlocuteur->getMoyenComs() as $moyenCom) {
                $typeComm = (new ReflectionClass($moyenCom))->getShortName();

                if ($typeComm == 'Email' && empty($login)) {
                    $login = $moyenCom->getAdresse();
                    $interlocuteurUser
                        ->setUsername($login)
                        ->setEmail($login);
                }
            }
        }

        return $this;
    }

}
