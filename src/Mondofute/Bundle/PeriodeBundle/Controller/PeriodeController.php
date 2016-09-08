<?php

namespace Mondofute\Bundle\PeriodeBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use JMS\JobQueueBundle\Entity\Job;
use Mondofute\Bundle\PeriodeBundle\Entity\Periode;
use Mondofute\Bundle\PeriodeBundle\Entity\TypePeriode;
use Mondofute\Bundle\PeriodeBundle\Form\PeriodeType;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class PeriodeController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
//        $typePeriodes = $em->getRepository(TypePeriode::class)->chargerParDates();
        $typePeriodes = $em->getRepository(TypePeriode::class)->findAll();
        $paginations = new ArrayCollection();
        $entitiesByPeriode = new ArrayCollection();
        $maxPerPage = $this->container->getParameter('max_per_page');
        $page = 1;

        $sortbyArray = array(
            'entity.id' => 'ASC',
            'periodes.debut' => 'ASC',
            'periodes.fin' => 'ASC'
        );

        /** @var TypePeriode $typePeriode */
        foreach ($typePeriodes as $typePeriode) {
            $count = $em
                ->getRepository('MondofutePeriodeBundle:Periode')
                ->countTotalByTypePeriode($typePeriode->getId());
            $pagination = array(
                'page' => $page,
                'route' => 'periode_periode_index',
                'pages_count' => ceil($count / $maxPerPage),
                'route_params' => array(),
                'max_per_page' => $maxPerPage
            );

            $paginations->add($pagination);

            $entities = $this->getDoctrine()->getRepository('MondofutePeriodeBundle:Periode')
                ->getList($page, $maxPerPage, $sortbyArray, $typePeriode);

            $entitiesByPeriode->add($entities);
        }
        return $this->render('@MondofutePeriode/Periode/index.html.twig', array(
            'typePeriodes' => $typePeriodes,
            'paginations' => $paginations,
            'periodes' => $entitiesByPeriode
        ));
    }

    /**
     * @param $typePeriodeId
     * @param $page
     * @param $maxPerPage
     * @return Response
     */
    public function listeAction($typePeriodeId, $page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();

        $sortbyArray = array(
            'entity.id' => 'ASC',
            'periodes.debut' => 'ASC',
            'periodes.fin' => 'ASC'
        );

        /** @var TypePeriode $typePeriode */
        $count = $em
            ->getRepository('MondofutePeriodeBundle:Periode')
            ->countTotalByTypePeriode($typePeriodeId);
        $pagination = array(
            'page' => $page,
            'route' => 'periode_periode_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $entities = $this->getDoctrine()->getRepository('MondofutePeriodeBundle:Periode')
            ->getList($page, $maxPerPage, $sortbyArray, $typePeriodeId);

        return $this->render('@MondofutePeriode/Periode/periode.html.twig', array(
            'typePeriodeId' => $typePeriodeId,
            'pagination' => $pagination,
            'periodes' => $entities
        ));
    }

    public function newAction(Request $request)
    {
        $periode = new Periode();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(PeriodeType::class, $periode);
        $form->add('submit', SubmitType::class, array('label' => $this->get('translator')->trans('valider')));
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {


                if (empty($periode->getNbJour())) {
                    $periode->setNbJour($periode->getType()->getNbJourDefaut());
                }
                /*
                 * GESTION EN JOB QUEUE
                 */
//            dump($periode);die;
                $job = new Job('creer:periodes',
                    array(
                        'debut' => $periode->getDebut()->format('Y-m-d'),
                        'fin' => $periode->getFin()->format('Y-m-d'),
                        'type-periode' => $periode->getType()->getId(),
                        'nb-jour' => $periode->getNbJour(),
                    ), true, 'periode');
                $em->persist($job);
                $em->flush();
//            dump($job);die;
                /*
                 * GESTION DES PERIODES EN COMMANDE
                 */
//            $kernel = $this->get('kernel');
//            $application = new Application($kernel);
//            $application->setAutoExit(false);
//            $input = new ArrayInput(array(
//                'command' => 'creer:periodes',
//                'debut' => $periode->getDebut()->format('Y-m-d'),
//                'fin' => $periode->getFin()->format('Y-m-d'),
//                'type-periode' => $periode->getType()->getId(),
//                'nb-jour' => $periode->getNbJour(),
//            ));
//            $output = new BufferedOutput();
//            $application->run($input, $output);
//            dump($output->fetch());die;
//            return $this->redirectToRoute('periode_periode_index');

                $this->addFlash('success', $this->get('translator')->trans('creation_periodes_en_cours'));
            }
        }
        return $this->render('MondofutePeriodeBundle:Periode:new.html.twig', array('form' => $form->createView()));
    }

    public function creerPeriodes(\DateTime $debut, \DateTime $fin, TypePeriode $typePeriode, $nbJour = null)
    {
        if (empty($nbJour)) {
            $nbJour = $typePeriode->getNbJourDefaut();
        }
        $periodes = new ArrayCollection();
//        clone la date de fin afin de calculer la date de dernierDebut
        $dernierDebut = clone $fin;
//        calcule la date de dernier debut
        $dernierDebut->sub(new \DateInterval('P' . $nbJour . 'D'));
//        initialise la variable date de début
        $debutTmp = clone $debut;
//        vérifie que la date de début de la periode à créer est inférieure ou égale à la date de dernier début
        while ($debutTmp->format('Ymd') <= $dernierDebut->format('Ymd')) {
//            clone la date de début de la période afin de calculer la date de fin
            $finTmp = clone $debutTmp;
//            calcul la date de fin (date de debut + nombre de jours)
            $finTmp->add(new \DateInterval('P' . $nbJour . 'D'));
//            Création de la période
            $periode = new Periode();
            $periode->setDebut(clone $debutTmp)
                ->setFin(clone $finTmp)
                ->setNbJour($nbJour)
                ->setType(clone $typePeriode);
            $periodes->add($periode);
//            décale la date de debut à +7 jours afin de créer les périodes semaines par semaine
            $debutTmp->add(new \DateInterval('P7D'));
            unset($periode);
        }
        return $periodes;
    }

    public function enregistrerPeriodesDansSites(ArrayCollection $periodes, array $sites)
    {
        $idPeriodesMin = array();
        $managerPeriode = $this->container->get('nucleus_manager_bdd.entity.manager_bdd');
        /** @var Site $site */
        foreach ($sites as $site) {
//            initialisation du insertMassif pour les periodes
            $managerPeriode->initInsertMassif('periode', array('type_id', 'debut', 'fin', 'nb_jour'), false,
                array($site->getLibelle()))
                ->setNbLignesParInsertMassif(5000);
            /** @var EntityManagerInterface $emSite */
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
//            $emSite->beginTransaction();
            $idPeriodesMin[$site->getLibelle()] = intval($emSite->createQuery('SELECT MAX(p.id) AS id FROM '.Periode::class.' AS p')->getArrayResult()[0]['id'],10);

//            Récupération des périodes répondant aux critères dans la base de données 
            $periodesSite = $emSite->getRepository(Periode::class)->rechercherPeriodesIntervale($periodes->first()->getDebut(),
                $periodes->last()->getFin(), $periodes->first()->getType());
//            /** @var Periode $periode */
            foreach ($periodes as $periode) {
//                vérifie si la période à ajouter existe déjà en base
                $result = $periodesSite->filter(
                    function (Periode $element) use ($periode) {
                        return ($element->getDebut() == $periode->getDebut() && $element->getFin() == $periode->getFin() && $element->getNbJour() == $periode->getNbJour() && $element->getType()->getId() == $periode->getType()->getId());
                    })->first();
                if (empty($result)) {
                    /** @var Periode $periode */
                    $periode->setType($emSite->getRepository(TypePeriode::class)->find($periode->getType()->getId()));
//                    ajout de la periode pour l'insert massif
                    $managerPeriode->addInsertLigne(array(
                        $periode->getType()->getId(),
                        $periode->getDebut()->format('Y-m-d'),
                        $periode->getFin()->format('Y-m-d'),
                        $periode->getNbJour()
                    ));
                }
            }
//            if(isset($emSite))
//                unset($emSite);
//            envoie les enregistrement des Periodes
            $managerPeriode->insertMassif();
        }
        return $idPeriodesMin;
    }
}
