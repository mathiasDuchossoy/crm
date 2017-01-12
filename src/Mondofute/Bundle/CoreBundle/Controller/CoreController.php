<?php

namespace Mondofute\Bundle\CoreBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CoreController extends Controller
{
    private $compagny;
    private $entityName;

    public function addTraductions($entityUnifie, $entityName, $compagny = 'Mondofute')
    {
        $em = $this->getDoctrine()->getManager();
        foreach ($entityUnifie->{'get' . ucfirst($entityName) . 's'}() as $entity) {
            $langues = $em->getRepository(Langue::class)->findAll();
            foreach ($langues as $langue) {
                $traduction = $entity->getTraductions()->filter(function ($element) use ($langue) {
                    return $element->getLangue() == $langue;
                })->first();
                if (false === $traduction) {
                    $traductionClassName = ucfirst($compagny) . '\\Bundle\\' . ucfirst($entityName) . 'Bundle\\Entity\\' . ucfirst($entityName) . 'Traduction';
                    $traduction = new $traductionClassName();
                    $entity->addTraduction($traduction);
                    $traduction->setLangue($langue);
                }
            }
        }
    }

    /**
     * @return mixed
     */
    public function getCompagny()
    {
        return $this->compagny;
    }

    /**
     * @param mixed $compagny
     * @return $this
     */
    public function setCompagny($compagny)
    {
        $this->compagny = $compagny;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEntityName()
    {
        return $this->entityName;
    }

    /**
     * @param mixed $entityName
     * @return $this
     */
    public function setEntityName($entityName)
    {
        $this->entityName = $entityName;

        return $this;
    }

    /**
     * Classe les traductions par rapport à leurs id
     */
    private function traductionsSortByLangue($entity)
    {
        /** @var ArrayIterator $iterator */
        $traductions = $entity->getTraductions();
        $iterator = $traductions->getIterator();
        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        $iterator->uasort(function ($a, $b) {
            return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $traductions = new ArrayCollection(iterator_to_array($iterator));

        $entity->getTraductions()->clear();

        foreach ($traductions as $traduction) {
            $this->addTraduction($traduction);
        }
    }
}
