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


class PeriodeController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $typePeriodes = $em->getRepository(TypePeriode::class)->chargerParDates();
        return $this->render('@MondofutePeriode/Periode/index.html.twig', array('typePeriodes' => $typePeriodes));
    }

    public function newAction(Request $request)
    {
        $periode = new Periode();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new PeriodeType(), $periode);
        $form->add('submit', SubmitType::class);
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

                $this->addFlash('success', 'les périodes sont en cours de création');
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
//            dump($periode);
            $periodes->add($periode);
//            décale la date de debut à +7 jours afin de créer les périodes semaines par semaine
            $debutTmp->add(new \DateInterval('P7D'));
            unset($periode);
        }
//        dump($periodes);
        return $periodes;
    }

    public function enregistrerPeriodesDansSites(ArrayCollection $periodes, array $sites)
    {
//        dump($periodes->first()->getDebut());
//        dump($periodes->last()->getDebut());
        $nbRequete = 0;
        /** @var Site $site */
        foreach ($sites as $site) {
            /** @var EntityManagerInterface $emSite */
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
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
                    $periode->setType($emSite->getRepository(TypePeriode::class)->find($periode->getType()->getId()));
                    $emSite->persist($periode);
                    $nbRequete++;
                    if ($nbRequete > 20) {
                        $nbRequete = 0;
                        $emSite->flush();
                        $emSite->clear();
                    }

                }
            }
            $emSite->flush();
        }
    }
}
