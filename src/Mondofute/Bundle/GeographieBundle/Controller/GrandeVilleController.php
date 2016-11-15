<?php

namespace Mondofute\Bundle\GeographieBundle\Controller;

use Aamant\Distance\Distance;
use Aamant\Distance\Providers\GoogleMapProvider;
use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Geocoder\HttpAdapter\CurlHttpAdapter;
use Geocoder\Provider\GoogleMaps;
use Illuminate\Support\Facades\Lang;
use Mondofute\Bundle\GeographieBundle\Entity\GrandeVille;
use Mondofute\Bundle\GeographieBundle\Entity\GrandeVilleTraduction;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Nucleus\MoyenComBundle\Entity\CoordonneesGPS;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Grandeville controller.
 *
 */
class GrandeVilleController extends Controller
{
    /**
     * Lists all GrandeVille entities.
     *
     */
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();

        $count = $em
            ->getRepository(GrandeVille::class)
            ->countTotal();

        $pagination = array(
            'page' => $page,
            'route' => 'geographie_grandeville_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array(
            'traductions.libelle' => 'ASC'
        );

        $unifies = $this->getDoctrine()->getRepository('MondofuteGeographieBundle:GrandeVille')
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

        return $this->render('@MondofuteGeographie/grandeville/index.html.twig', array(
            'grandeVilles' => $unifies,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new grandeVille entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('id' => 'ASC'));
        $grandeVille = new GrandeVille();
        $this->ajoutTraductions($grandeVille, $langues);

        $form = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\GrandeVilleType', $grandeVille);
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $curl     = new \Geocoder\HttpAdapter\CurlHttpAdapter();
            $geocoder = new \Geocoder\Provider\GoogleMapsProvider($curl);

            $traduction_fr = $grandeVille->getTraductions()->filter(function(GrandeVilleTraduction $element){
                return $element->getLangue()->getCode() == 'fr_FR';
            })->first();

            try{
                $geocodedDatas = $geocoder->getGeocodedData($traduction_fr->getLibelle());
                $geocodedData = $geocodedDatas[0];
            }catch (\Geocoder\Exception\NoResultException $exception){
                $geocodedData = null;
            }

            if(!empty($geocodedData)){
                $gps = new CoordonneesGPS();
                $grandeVille->setCoordonneesGps($gps);
                $gps
                    ->setLatitude($geocodedData['latitude'])
                    ->setLongitude($geocodedData['longitude'])
                ;
            }

            $em->persist($grandeVille);
            $em->flush();

            $this->copieVersSites($grandeVille);

            $this->addFlash('success', 'Label créé avec succès.');
            return $this->redirectToRoute('geographie_grandeville_edit', array('id' => $grandeVille->getId()));
        }

        return $this->render('@MondofuteGeographie/grandeville/new.html.twig', array(
            'grandeVille' => $grandeVille,
            'form' => $form->createView(),
            'langues' => $langues
        ));
    }

    /**
     * @param GrandeVille $grandeVille
     * @param $langues
     */
    private function ajoutTraductions($grandeVille, $langues)
    {
        foreach ($langues as $langue) {
            $traduction = $grandeVille->getTraductions()->filter(function (GrandeVilleTraduction $element) use ($langue) {
                return $element->getLangue() == $langue;
            })->first();
            if (false === $traduction) {
                $traduction = new GrandeVilleTraduction();
                $grandeVille->addTraduction($traduction);
                $traduction->setLangue($langue);
            }
        }

        $this->traductionsSortByLangue($grandeVille);

    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param $grandeVilles
     */
    private function traductionsSortByLangue($grandeVilles)
    {
        /** @var ArrayIterator $iterator */
        /** @var GrandeVille $grandeVille */
        foreach ($grandeVilles as $grandeVille) {
            $traductions = $grandeVille->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            $iterator->uasort(function (GrandeVilleTraduction $a, GrandeVilleTraduction $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $grandeVille->setTraductions($traductions);
        }
    }

    /**
     * @param GrandeVille $grandeVille
     */
    function copieVersSites($grandeVille)
    {
        /** @var EntityManager $emSite */
        /** @var GrandeVilleTraduction $traduction */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0), array('classementAffichage' => 'ASC'));
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $grandeVilleSite = $emSite->find(GrandeVille::class, $grandeVille->getId());
            if (empty($grandeVilleSite)) {
                $grandeVilleSite = new GrandeVille();
                $grandeVilleSite->setId($grandeVille->getId());
                $metadata = $emSite->getClassMetadata(get_class($grandeVilleSite));
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
            }
            // *** gps ***
            if(!empty($grandeVille->getCoordonneesGps())){
                if(empty($gps = $grandeVilleSite->getCoordonneesGps())){
                    $gps = new CoordonneesGPS();
                    $grandeVilleSite->setCoordonneesGps($gps);
                }
                $gps
                    ->setLongitude($grandeVille->getCoordonneesGps()->getLongitude())
                    ->setLatitude($grandeVille->getCoordonneesGps()->getLatitude())
                ;
            }else{
                if(!empty($grandeVilleSite->getCoordonneesGps())){
                    $emSite->remove($grandeVilleSite->getCoordonneesGps());
                }
            }
            // *** fin gps ***
            // *** traductions ***
            foreach ($grandeVille->getTraductions() as $traduction) {
                $traductionSite = $grandeVilleSite->getTraductions()->filter(function (GrandeVilleTraduction $element) use ($traduction) {
                    return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                })->first();
                if (false === $traductionSite) {
                    $traductionSite = new GrandeVilleTraduction();
                    $traductionSite->setLangue($emSite->find(Langue::class, $traduction->getLangue()->getId()));
                    $grandeVilleSite->addTraduction($traductionSite);
                }
                $traductionSite->setLibelle($traduction->getLibelle());
            }
            // *** fin traductions ***
            $emSite->persist($grandeVilleSite);
            $emSite->flush();
        }
    }

    /**
     * Finds and displays a grandeVille entity.
     *
     */
    public function showAction(GrandeVille $grandeVille)
    {
        $deleteForm = $this->createDeleteForm($grandeVille);

        return $this->render('@MondofuteGeographie/grandeville/show.html.twig', array(
            'grandeVille' => $grandeVille,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a grandeVille entity.
     *
     * @param GrandeVille $grandeVille The grandeVille entity
     *
     * @return Form The form
     */
    private function createDeleteForm(GrandeVille $grandeVille)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('geographie_grandeville_delete', array('id' => $grandeVille->getId())))
            ->add('Supprimer', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing grandeVille entity.
     *
     */
    public function editAction(Request $request, GrandeVille $grandeVille)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($grandeVille);
        $form = $this->createForm('Mondofute\Bundle\GeographieBundle\Form\GrandeVilleType', $grandeVille)
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $curl     = new \Geocoder\HttpAdapter\CurlHttpAdapter();
            $geocoder = new \Geocoder\Provider\GoogleMapsProvider($curl);

            $traduction_fr = $grandeVille->getTraductions()->filter(function(GrandeVilleTraduction $element){
                return $element->getLangue()->getCode() == 'fr_FR';
            })->first();

            try{
                $geocodedDatas = $geocoder->getGeocodedData($traduction_fr->getLibelle());
                $geocodedData = $geocodedDatas[0];
            }catch (\Geocoder\Exception\NoResultException $exception){
                $geocodedData = null;
            }

            if(!empty($geocodedData)){
                if(empty($gps = $grandeVille->getCoordonneesGps())){
                    $gps = new CoordonneesGPS();
                    $grandeVille->setCoordonneesGps($gps);
                }
                $gps
                    ->setLatitude($geocodedData['latitude'])
                    ->setLongitude($geocodedData['longitude'])
                ;
            }else{
                if(!empty($grandeVille->getCoordonneesGps())){
                    $em->remove($grandeVille->getCoordonneesGps());
                }
            }

            $em->flush();

            $this->copieVersSites($grandeVille);

            $this->addFlash('success', 'Label modifié avec succès.');

            return $this->redirectToRoute('geographie_grandeville_edit', array('id' => $grandeVille->getId()));
        }
        $langues = $em->getRepository(Langue::class)->findAll();

        return $this->render('@MondofuteGeographie/grandeville/edit.html.twig', array(
            'grandeVille' => $grandeVille,
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
            'langues' => $langues
        ));
    }

    /**
     * Deletes a grandeVille entity.
     *
     */
    public function deleteAction(Request $request, GrandeVille $grandeVille)
    {
        $form = $this->createDeleteForm($grandeVille);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try{

                $em = $this->getDoctrine()->getManager();

                $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0));
                foreach ($sites as $site)
                {
                    $emSite = $this->getDoctrine()->getManager($site->getLibelle());
                    $grandeVilleSite = $emSite->find(GrandeVille::class , $grandeVille->getId());
                    $emSite->remove($grandeVilleSite);
                    $emSite->flush();
                }

                $em->remove($grandeVille);
                $em->flush();

                $this->addFlash('success', 'Grande ville supprimé avec succès.');
            }
            catch (Exception $e)
            {
                $this->addFlash('error', 'La grande ville est utilisé par une autre entité.');
            }
        }

        return $this->redirectToRoute('geographie_grandeville_index');
    }
}
