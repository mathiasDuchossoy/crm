<?php

namespace Mondofute\Bundle\LogementPeriodeBundle\Controller;

use Doctrine\ORM\EntityManager;
use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LogementPeriodeController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    public function associerLogements($idPeriodesMin, $sites)
    {
        /** @var Site $site */
        foreach ($sites as $site) {
            /** @var EntityManager $emSite */
            $em = $this->getDoctrine()->getManager($site->getLibelle());
            $logements = $em->createQuery('SELECT l.id FROM ' . Logement::class . ' l')->getArrayResult();
            foreach ($logements as $logement) {
                $this->associerPeriodes($logement['id'], $site, $idPeriodesMin[$site->getLibelle()]);
            }
        }
    }

    /**
     * @param $idLogement
     * @param $site Site
     * @param null $idPeriodeMin
     */
    public function associerPeriodes($idLogement, $site, $idPeriodeMin = null)
    {
        /** @var EntityManager $em */
        $today = new \DateTime();
        $em = $this->getDoctrine()->getManager($site->getLibelle());
        $where = '';
//        si $idPeriodeMin est renseigné on récupère les id > idPeriodeMin si on récupère toutes les périodes
        if (!empty($idPeriodeMin)) {
            $where = ' AND p.id > ' . $idPeriodeMin;
//            récupère toutes les periodes
        }
//        $requete = 'SELECT p.id FROM ' . Periode::class . ' AS p WHERE p.debut >= ' . $today->format('Y-m-d') . $where;
        $requete = 'SELECT p.id FROM periode p LEFT JOIN logement_type_periode ltp ON ltp.type_periode_id = p.type_id WHERE ltp.logement_id = ' . $idLogement . ' AND p.debut >= ' . $today->format('Y-m-d') . $where;
//        $periodes = $em->createQuery($requete)->getArrayResult();
        $periodes = $em->getConnection()->executeQuery($requete)->fetchAll();
        $this->enregistrerLogementPeriodes($idLogement, $periodes, $site);
    }

    /**
     * @param $idLogement
     * @param $periodes
     * @param $site Site
     */
    public function enregistrerLogementPeriodes($idLogement, $periodes, $site)
    {
        $managerLogementPeriode = $this->container->get('nucleus_manager_bdd.entity.manager_bdd');
        $managerLogementPeriodeLocatif = $this->container->get('nucleus_manager_bdd.entity.manager_bdd');
        $managerLogementPeriode->initInsertMassif('logement_periode', array('logement_id', 'periode_id', 'actif'), true, array($site->getLibelle()));
        $managerLogementPeriodeLocatif->initInsertMassif('logement_periode_locatif', array('logement_id', 'periode_id', 'prix_public', 'stock'), true, array($site->getLibelle()));
        foreach ($periodes as $periode) {
            $managerLogementPeriode->addInsertLigne(array($idLogement, $periode['id'], true));
            $managerLogementPeriodeLocatif->addInsertLigne(array($idLogement, $periode['id'], 0, 0));
        }
        $managerLogementPeriode->insertMassif();
        $managerLogementPeriodeLocatif->insertMassif();
    }
}
