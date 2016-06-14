<?php

namespace Mondofute\Bundle\FournisseurBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\FournisseurBundle\Entity\FournisseurInterlocuteur;
use Mondofute\Bundle\FournisseurBundle\Entity\Interlocuteur;
use Mondofute\Bundle\FournisseurBundle\Entity\InterlocuteurUser;
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


            $userManager = $this->get('fos_user.user_manager');
            $userManager->updatePassword($interlocuteurUser);

//            dump($interlocuteurUser);

            foreach ($interlocuteur->getMoyenComs() as $moyenCom) {
                $typeComm = (new ReflectionClass($moyenCom))->getShortName();

                if ($typeComm == 'Email' && empty($login)) {
                    $login = $moyenCom->getAdresse();
                    $interlocuteurUser
                        ->setUsername($login)
                        ->setEmail($login);
                    unset($login);
                }
            }
        }

        return $this;
    }

    public function testInterlocuteursLoginExist(Collection $fournisseurInterlocuteurs)
    {
        $exist = false;
        $users = new ArrayCollection();
        /** @var InterlocuteurUser $interlocuteurUser */
        foreach ($fournisseurInterlocuteurs as $fournisseurInterlocuteur) {
            $interlocuteurUser = $fournisseurInterlocuteur->getInterlocuteur()->getUser();
            if ($users->contains($interlocuteurUser->getUsername())) {
                $this->addFlash(
                    'error',
                    'Le Login/Email ' . $interlocuteurUser->getUsername() . ' a déjà été ajouté une fois dans le formulaire.'
                );
                $exist = true;
            } elseif ($this->loginExist($interlocuteurUser)) {
                $exist = true;
            }
            $users->add($interlocuteurUser->getUsername());
        }
        return $exist;
    }

    public function loginExist(InterlocuteurUser $interlocuteurUser)
    {

        $em = $this->getDoctrine()->getEntityManager();
        $interlocuteurUserByUsername = $em->getRepository(InterlocuteurUser::class)->findOneBy(array('username' => $interlocuteurUser->getUsername()));
        $interlocuteurUserByMail = $em->getRepository(InterlocuteurUser::class)->findOneBy(array('email' => $interlocuteurUser->getEmail()));
        if ((!empty($interlocuteurUserByUsername) && $interlocuteurUser != $interlocuteurUserByUsername)
            ||
            (!empty($interlocuteurUserByMail) && $interlocuteurUser != $interlocuteurUserByMail)
        ) {
            $this->addFlash(
                'error',
                'Le Login/Email ' . $interlocuteurUser->getUsername() . ' existe déjà.'
            );

            return true;
        }
        return false;
    }

    public function deleteInterlocuteur(Interlocuteur $interlocuteur)
    {

    }

}
