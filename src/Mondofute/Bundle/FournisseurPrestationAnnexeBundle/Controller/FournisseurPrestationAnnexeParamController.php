<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Controller;

use DateTime;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeParam;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PeriodeValidite;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PrestationAnnexeTarif;
use Mondofute\Bundle\PeriodeBundle\Entity\Periode;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class FournisseurPrestationAnnexeParamController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    public function getPrixByPeriodeAction($id, $periodeId)
    {
        $em = $this->getDoctrine()->getManager();
        $param = $em->find(FournisseurPrestationAnnexeParam::class, $id);
        $periode = $em->find(Periode::class, $periodeId);
        $response = 0;
        /** @var PrestationAnnexeTarif $tarif */
        foreach ($param->getTarifs() as $tarif) {
            /** @var PeriodeValidite $periodeValidite */
            if ($tarif->getPeriodeValidites()->isEmpty()) {
                $response = $tarif->getPrixPublic();
            } else {
                $periodeValidite = $tarif->getPeriodeValidites()->filter(function (PeriodeValidite $element) use ($periode) {
                    return $element->getDateDebut() >= $periode->getDebut() && $element->getDateFin() <= $periode->getFin();
                })->first();
                if (!empty($periodeValidite)) {
                    $response = $tarif->getPrixPublic();
                }
            }
        }

        return new Response($response);
    }

    public function getPrixByDatesAction($id, $dateDebut, $dateFin)
    {
        $em = $this->getDoctrine()->getManager();
        $param = $em->find(FournisseurPrestationAnnexeParam::class, $id);
        $dateDebut = new DateTime(date($dateDebut));
        $dateFin = new DateTime(date($dateFin));
        $response = 0;
        /** @var PrestationAnnexeTarif $tarif */
        foreach ($param->getTarifs() as $tarif) {
            /** @var PeriodeValidite $periodeValidite */
            if ($tarif->getPeriodeValidites()->isEmpty()) {
                $response = $tarif->getPrixPublic();
            } else {
                $periodeValidite = $tarif->getPeriodeValidites()->filter(function (PeriodeValidite $element) use ($dateDebut, $dateFin) {
                    return $element->getDateDebut() >= $dateDebut && $element->getDateFin() <= $dateFin;
                })->first();
                if (!empty($periodeValidite)) {
                    $response = $tarif->getPrixPublic();
                }
            }
        }

        return new Response($response);
    }
}
