<?php

namespace Mondofute\Bundle\ClientBundle\Controller;

use DateTime;
use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\ClientBundle\Entity\Client;
use Mondofute\Bundle\ClientBundle\Entity\ClientUser;
use Mondofute\Bundle\CodePromoBundle\Entity\CodePromoClient;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Nucleus\ContactBundle\Entity\Civilite;
use Nucleus\MoyenComBundle\Entity\Adresse;
use Nucleus\MoyenComBundle\Entity\Email;
use Nucleus\MoyenComBundle\Entity\Pays;
use Nucleus\MoyenComBundle\Entity\TelFixe;
use Nucleus\MoyenComBundle\Entity\TelMobile;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Client controller.
 *
 */
class ClientController extends Controller
{
    /**
     * Lists all Client entities.
     *
     */
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();

        $count = $em
            ->getRepository('MondofuteClientBundle:ClientUser')
            ->countTotal();
        $pagination = array(
            'page' => $page,
            'route' => 'client_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array();

        $entities = $this->getDoctrine()->getRepository('MondofuteClientBundle:ClientUser')
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

        return $this->render('@MondofuteClient/client/index.html.twig', array(
            'clientUsers' => $entities,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new Client entity.
     *
     */
    public function newAction(Request $request)
    {
        /** @var Site $site */
        $clientUser = new ClientUser();

        $client = new Client();

        $clientUser->setClient($client);

        $this->addMoyenComs($client);
        $form = $this->createForm('Mondofute\Bundle\ClientBundle\Form\ClientUserType', $clientUser);
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $clientUser->setEnabled(true);

            $em = $this->getDoctrine()->getManager();


            foreach ($client->getMoyenComs() as $moyenCom) {
                $moyenCom->setDateCreation();
                $typeComm = (new ReflectionClass($moyenCom))->getShortName();

                if ($typeComm == 'Email' && empty($login)) {
                    $login = $moyenCom->getAdresse();
                    $clientUser
                        ->setUsername($login)
                        ->setEmail($login);
                }
            }
            $client->setDateCreation();

            if (!$this->loginExist($clientUser)) {
                $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0));
                foreach ($sites as $site) {
                    $clientUserClone = clone $clientUser;

                    $clientClone = clone $client;
                    $emSite = $this->getDoctrine()->getManager($site->getLibelle());

                    $clientClone->setCivilite($emSite->find(Civilite::class, $client->getCivilite()->getId()));
                    $this->dupliquerMoyenComs($client, $emSite);

                    $clientUserClone->setClient($clientClone);

                    $emSite->persist($clientClone);
                    $emSite->persist($clientUserClone);

                    $emSite->flush();
                }
                $this->dupliquerMoyenComs($client, $em);
//        dump($client->getMoyenComs());die;
                $em->persist($client);
                $em->persist($clientUser);

                $em->flush();

                // add flash messages
                $this->addFlash(
                    'success',
                    'Le client ' . $clientUser->getUsername() . ' a bien été créé.'
                );

                return $this->redirectToRoute('client_edit', array('id' => $clientUser->getId()));
            }

        }

        return $this->render('@MondofuteClient/client/new.html.twig', array(
            'client' => $clientUser,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Client $client
     */
    private function addMoyenComs(Client $client)
    {
        $client
            ->addMoyenCom(new Adresse())
            ->addMoyenCom(new TelFixe())
            ->addMoyenCom(new TelMobile())
            ->addMoyenCom(new Email())
            ->addMoyenCom(new Email());
    }

    private function loginExist(ClientUser $clientUser)
    {

        $em = $this->getDoctrine()->getManager();
        $clientUserByUsername = $em->getRepository(ClientUser::class)->findOneBy(array('username' => $clientUser->getUsername()));
        $clientUserByMail = $em->getRepository(ClientUser::class)->findOneBy(array('email' => $clientUser->getEmail()));

        if ((!empty($clientUserByUsername) && $clientUser != $clientUserByUsername)
            ||
            (!empty($clientUserByMail) && $clientUser != $clientUserByMail)
        ) {
            $this->addFlash(
                'error',
                'Le Login/Email ' . $clientUser->getUsername() . ' éxiste déjà.'
            );

            return true;
        }
        return false;
    }

    private function dupliquerMoyenComs(Client $client, EntityManager $emSite)
    {
        foreach ($client->getMoyenComs() as $moyenCom) {
            $typeComm = (new ReflectionClass($moyenCom))->getShortName();
            switch ($typeComm) {
                case 'Adresse':
                    /** @var Adresse $moyenCom */
                    $newMoyenCom = new Adresse();
                    $newMoyenCom
                        ->setCodePostal($moyenCom->getCodePostal())
                        ->setAdresse1($moyenCom->getAdresse1())
                        ->setAdresse2($moyenCom->getAdresse2())
                        ->setAdresse3($moyenCom->getAdresse3())
                        ->setVille($moyenCom->getVille())
//                        ->setPays($moyenCom->getPays())
                        ->setPays($emSite->find(Pays::class, $moyenCom->getPays()->getId()));
//                    dump($emSite->find(Pays::class , $moyenCom->getPays()->getId()));die;
                    break;
                case 'TelFixe':
                    /** @var TelFixe $moyenCom */
                    $newMoyenCom = new TelFixe();
                    $newMoyenCom
                        ->setNumero($moyenCom->getNumero());
                    break;
                case 'TelMobile':
                    /** @var TelMobile $moyenCom */
                    $newMoyenCom = new TelMobile();
                    $newMoyenCom
//                        ->setSmsing($moyenCom->getSmsing())
                        ->setNumero($moyenCom->getNumero());
                    break;
                case 'Email':
                    /** @var Email $moyenCom */
                    $newMoyenCom = new Email();
                    $newMoyenCom
                        ->setAdresse($moyenCom->getAdresse());
                    break;
                default:
                    break;
            }
            if (!empty($newMoyenCom)) {
//                $newMoyenCom->setDateCreation($moyenCom->getDateCreation());
                $newMoyenCom->setDateCreation();
                $client->removeMoyenCom($moyenCom);
                $client->addMoyenCom($newMoyenCom);
            }
        }
    }

    /**
     * Finds and displays a Client entity.
     *
     */
    public function showAction(ClientUser $clientUser)
    {
        $deleteForm = $this->createDeleteForm($clientUser);

        return $this->render('@MondofuteClient/client/show.html.twig', array(
            'clientUser' => $clientUser,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Client entity.
     *
     * @param Client $client The Client entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ClientUser $clientUser)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('client_delete', array('id' => $clientUser->getId())))
            ->add('Supprimer', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Client entity.
     *
     */
    public function editAction(Request $request, ClientUser $clientUser)
    {
        $client = $clientUser->getClient();
        $deleteForm = $this->createDeleteForm($clientUser);
        $editForm = $this->createForm('Mondofute\Bundle\ClientBundle\Form\ClientUserType', $clientUser)
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour'));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $dateModification = new DateTime();

//            $client->setDateModification($dateModification);
            foreach ($client->getMoyenComs() as $moyenCom) {
                $moyenCom->setDateModification($dateModification);
                $typeComm = (new ReflectionClass($moyenCom))->getShortName();

                if ($typeComm == 'Email' && empty($login)) {
                    $login = $moyenCom->getAdresse();
                    $clientUser
                        ->setUsername($login)
                        ->setEmail($login);
                }
            }

            if (!$this->loginExist($clientUser)) {

                $em = $this->getDoctrine()->getManager();

                $userManager = $this->get('fos_user.user_manager');
                $userManager->updatePassword($clientUser);

                $this->majSites($clientUser);

                $em->persist($clientUser);
                $em->persist($client);
                $em->flush();

                // add flash messages
                $this->addFlash(
                    'success',
                    'Le client ' . $clientUser->getUsername() . ' a bien été modifié.'
                );

                return $this->redirectToRoute('client_edit', array('id' => $clientUser->getId()));
            }
        }

        return $this->render('@MondofuteClient/client/edit.html.twig', array(
            'client' => $clientUser,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    private function majSites(ClientUser $clientUser)
    {
        /** @var Site $site */
        $client = $clientUser->getClient();
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0));
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $clientUserSite = $emSite->find(ClientUser::class, $clientUser);
            $clientSite = $clientUserSite->getClient();
            $clientSite
                ->setDateNaissance($client->getDateNaissance())
                ->setVip($client->getVip())
                ->setNom($client->getNom())
                ->setPrenom($client->getPrenom())
                ->setCivilite($emSite->find(Civilite::class, $client->getCivilite()));
//            $clientSite->setDateModification($client->getDateModification());

            $clientUserSite->setPassword($clientUser->getPassword());
            $clientUserSite->setEnabled($clientUser->isEnabled());

            foreach ($client->getMoyenComs() as $moyenCom) {
                $typeComm = (new ReflectionClass($moyenCom))->getShortName();

                $moyenComSite = $clientSite->getMoyenComs()->filter(function ($element) use ($typeComm) {
                    $typeCommSite = (new ReflectionClass($element))->getShortName();
                    return $typeCommSite == $typeComm;
                });
                switch ($typeComm) {
                    case 'Adresse':
                        /** @var Adresse $moyenCom */
                        $moyenComSite->first()
                            ->setCodePostal($moyenCom->getCodePostal())
                            ->setAdresse1($moyenCom->getAdresse1())
                            ->setAdresse2($moyenCom->getAdresse2())
                            ->setAdresse3($moyenCom->getAdresse3())
                            ->setVille($moyenCom->getVille())
                            ->setPays($emSite->find(Pays::class, $moyenCom->getPays()));
//                            ->setDateModification($moyenCom->getDateModification());
                        break;
                    case 'TelFixe':
                        /** @var TelFixe $moyenCom */
                        $moyenComSite->first()
                            ->setNumero($moyenCom->getNumero());
//                            ->setDateModification($moyenCom->getDateModification());
                        break;
                    case 'TelMobile':
                        /** @var TelMobile $moyenCom */
                        $moyenComSite->first()
//                            ->setSmsing($moyenCom->getSmsing())
                            ->setNumero($moyenCom->getNumero());
//                            ->setDateModification($moyenCom->getDateModification());
                        break;
                    case 'Email':
                        /** @var Email $email */
                        foreach ($moyenComSite as $key => $email) {
                            $moyenComSite->get($key)
                                ->setAdresse($moyenCom->getAdresse());
//                                ->setDateModification($moyenCom->getDateModification());
                            if (empty($login)) {
                                $login = $moyenCom->getAdresse();
                                $clientUserSite
                                    ->setUsername($login)
                                    ->setEmail($login);
                            }
//                            dump($clientUserSite);
                        }
                        break;
                    default:
                        break;
                }
            }

            $emSite->persist($clientSite);
            $emSite->persist($clientUserSite);
            $emSite->flush();
            unset($login);
        }
    }

    /**
     * Deletes a Client entity.
     *
     */
    public function deleteAction(Request $request, ClientUser $clientUser)
    {
        /** @var Site $site */
        $client = $clientUser->getClient();
        $form = $this->createDeleteForm($clientUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0));
            foreach ($sites as $site) {
                $emSite = $this->getDoctrine()->getManager($site->getLibelle());
                $clientUserSite = $emSite->find(ClientUser::class, $clientUser->getId());
                
                if (!empty($clientUserSite)) {
                    $clientSite = $clientUserSite->getClient();
                    foreach ($clientSite->getMoyenComs() as $moyenComSite) {
                        $clientSite->removeMoyenCom($moyenComSite);
                        $emSite->remove($moyenComSite);
                    }

                    $codePromoClients = $emSite->getRepository(CodePromoClient::class)->findBy(array('client' => $clientSite));
                    foreach ($codePromoClients as $codePromoClient){
                        $emSite->remove($codePromoClient);
                    }

                    $emSite->flush();
                    $emSite->remove($clientSite);
                    $emSite->remove($clientUserSite);
                    $emSite->flush();
                }
            }

            foreach ($client->getMoyenComs() as $moyenCom) {
                $client->removeMoyenCom($moyenCom);
                $em->merge($moyenCom);
                $em->remove($moyenCom);
            }

            $codePromoClients = $em->getRepository(CodePromoClient::class)->findBy(array('client' => $client));
            foreach ($codePromoClients as $codePromoClient){
                $em->remove($codePromoClient);
            }

            $em->flush();

            $em->remove($client);
            $em->remove($clientUser);
            $em->flush();

            $this->addFlash('success','Le client a bien été modifié.');
        }

        return $this->redirectToRoute('client_index');
    }
}
