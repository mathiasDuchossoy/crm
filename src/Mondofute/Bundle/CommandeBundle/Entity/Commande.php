<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 01/02/2017
 * Time: 11:10
 */

namespace Mondofute\Bundle\CommandeBundle\Entity;


use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Mondofute\Bundle\ClientBundle\Entity\Client;
use Mondofute\Bundle\CommandeBundle\Entity\CommandeLitigeDossier;
use Mondofute\Bundle\CommentaireBundle\Entity\CommentaireClient;
use Mondofute\Bundle\CommentaireBundle\Entity\CommentaireInterne;
use Mondofute\Bundle\SiteBundle\Entity\Site;

class Commande
{

    /**
     * @var integer
     */
    private $id;
    /**
     * @var DateTime
     */
    private $dateCommande;
    /**
     * @var integer
     */
    private $numCommande;
    /**
     * @var Collection
     */
    private $clients;
    /**
     * @var Collection
     */
    private $commandeLignes;
    /**
     * @var Collection
     */
    private $commandeEtatDossiers;
    /**
     * @var Collection
     */
    private $commandeStatutDossiers;
    /**
     * @var Site
     */
    private $site;
    /**
     * @var integer
     */
    private $prixVente = 0;
    /**
     * @var Collection
     */
    private $commandeLitigeDossiers;
    /**
     * @var CommentaireClient
     */
    private $commentaireClient;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $commentaireInterne;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->clients = new ArrayCollection();
        $this->commandeLignes = new ArrayCollection();
        $this->commandeEtatDossiers = new ArrayCollection();
        $this->commandeStatutDossiers = new ArrayCollection();
        $this->dateCommande = new DateTime();
        $this->commandeLitigeDossiers = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get dateCommande
     *
     * @return DateTime
     */
    public function getDateCommande()
    {
        return $this->dateCommande;
    }

    /**
     * Set dateCommande
     *
     * @param DateTime $dateCommande
     *
     * @return Commande
     */
    public function setDateCommande($dateCommande)
    {
        $this->dateCommande = $dateCommande;

        return $this;
    }

    /**
     * Get numCommande
     *
     * @return integer
     */
    public function getNumCommande()
    {
        return $this->numCommande;
    }

    /**
     * Set numCommande
     *
     * @param integer $numCommande
     *
     * @return Commande
     */
    public function setNumCommande($numCommande)
    {
        $this->numCommande = $numCommande;

        return $this;
    }

    /**
     * Add client
     *
     * @param Client $client
     *
     * @return Commande
     */
    public function addClient(Client $client)
    {
        $this->clients[] = $client;

        return $this;
    }

    /**
     * Remove client
     *
     * @param Client $client
     */
    public function removeClient(Client $client)
    {
        $this->clients->removeElement($client);
    }

    /**
     * Get clients
     *
     * @return Collection
     */
    public function getClients()
    {
        return $this->clients;
    }

    /**
     * Add commandeLigne
     *
     * @param CommandeLigne $commandeLigne
     *
     * @return Commande
     */
    public function addCommandeLigne(CommandeLigne $commandeLigne)
    {
        $this->commandeLignes[] = $commandeLigne->setCommande($this);

        return $this;
    }

    /**
     * Remove commandeLigne
     *
     * @param CommandeLigne $commandeLigne
     */
    public function removeCommandeLigne(CommandeLigne $commandeLigne)
    {
        $this->commandeLignes->removeElement($commandeLigne);
    }

    /**
     * Get commandeLignes
     *
     * @return Collection
     */
    public function getCommandeLignes()
    {
//        $newCommandeLignes = new ArrayCollection();
//        foreach ($this->commandeLignes as $commandeLigne) {
//            $oReflectionClass = new ReflectionClass($commandeLigne);
//            if ($oReflectionClass->getShortName() == 'CommandeLignePrestationAnnexe') {
//                /** @var CommandeLignePrestationAnnexe $commandeLigne */
//                if (empty($commandeLigne->getCommandeLigneSejour())) {
//                    $newCommandeLignes->add($commandeLigne);
//                }
//            } else {
//                $newCommandeLignes->add($commandeLigne);
//            }
//        }
//        $this->commandeLignes->clear();
//        foreach ($newCommandeLignes as $newCommandeLigne) {
//            $this->commandeLignes->add($newCommandeLigne);
//        }
        return $this->commandeLignes;
    }

    /**
     * Add commandeEtatDossier
     *
     * @param CommandeEtatDossier $commandeEtatDossier
     *
     * @return Commande
     */
    public function addCommandeEtatDossier(CommandeEtatDossier $commandeEtatDossier)
    {
        $this->commandeEtatDossiers[] = $commandeEtatDossier->setCommande($this);

        return $this;
    }

    /**
     * Remove commandeEtatDossier
     *
     * @param CommandeEtatDossier $commandeEtatDossier
     */
    public function removeCommandeEtatDossier(CommandeEtatDossier $commandeEtatDossier)
    {
        $this->commandeEtatDossiers->removeElement($commandeEtatDossier);
    }

    /**
     * Get commandeEtatDossiers
     *
     * @return Collection
     */
    public function getCommandeEtatDossiers()
    {
        return $this->commandeEtatDossiers;
    }

    /**
     * Remove commandeStatutDossier
     *
     * @param CommandeStatutDossier $commandeStatutDossier
     */
    public function removeCommandeStatutDossier(CommandeStatutDossier $commandeStatutDossier)
    {
        $this->commandeStatutDossiers->removeElement($commandeStatutDossier);
    }

    /**
     * Get site
     *
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set site
     *
     * @param Site $site
     *
     * @return Commande
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get prixVente
     *
     * @return integer
     */
    public function getPrixVente()
    {
        return $this->prixVente;
    }

    /**
     * Set prixVente
     *
     * @param integer $prixVente
     *
     * @return Commande
     */
    public function setPrixVente($prixVente)
    {
        $this->prixVente = $prixVente;

        return $this;
    }

    public function getCommandeLigneRemisePromotions()
    {
        return $this->commandeLignes->filter(function ($element) {
            return get_class($element) == RemisePromotion::class;
        });
    }

    public function getSejourPeriodes()
    {
        return $this->commandeLignes->filter(function ($element) {
            return get_class($element) == SejourPeriode::class;
        });
    }

    public function getCommandeLigneRemiseDecotes($type = null)
    {
        return $this->commandeLignes->filter(function ($element) use ($type) {
            if (!empty($type)) {
                return (get_class($element) == RemiseDecote::class and $element->getDecote()->getType() == $type);
            }
            return get_class($element) == RemiseDecote::class;
        });
    }

    public function getPrestationAnnexeExternes()
    {
        return $this->commandeLignes->filter(function ($element) {
            return get_class($element) == CommandeLignePrestationAnnexe::class;
        });
    }

    /**
     * Remove commandeLitigeDossier
     *
     * @param CommandeLitigeDossier $commandeLitigeDossier
     */
    public function removeCommandeLitigeDossier(
        CommandeLitigeDossier $commandeLitigeDossier
    )
    {
        $this->commandeLitigeDossiers->removeElement($commandeLitigeDossier);
    }

    /**
     * @return CommandeStatutDossier
     */
    public function getCommandeStatutDossier()
    {
        $criteresStatut = Criteria::create();
        $criteresStatut->orderBy(array('dateHeure' => 'DESC'));
        return $this->getCommandeStatutDossiers()->matching($criteresStatut)->first();
    }

    /**
     * Get commandeStatutDossiers
     *
     * @return Collection
     */
    public function getCommandeStatutDossiers()
    {
        return $this->commandeStatutDossiers;
    }

    /**
     * @param StatutDossier $statutDossier
     *
     * @return $this
     */
    public function addStatutDossier($statutDossier)
    {
        $commandeStatutDossier = new CommandeStatutDossier($statutDossier);
        $this->addCommandeStatutDossier($commandeStatutDossier);

        return $this;
    }

    /**
     * Add commandeStatutDossier
     *
     * @param CommandeStatutDossier $commandeStatutDossier
     *
     * @return Commande
     */
    public function addCommandeStatutDossier(CommandeStatutDossier $commandeStatutDossier)
    {
        $this->commandeStatutDossiers[] = $commandeStatutDossier->setCommande($this);

        return $this;
    }

    /**
     * @return CommandeLitigeDossier
     */
    public function getCommandeLitigeDossier()
    {
        $criteresLitige = Criteria::create();
        $criteresLitige->orderBy(array('dateHeure' => 'DESC'));
        return $this->getCommandeLitigeDossiers()->matching($criteresLitige)->first();
    }

    /**
     * Get commandeLitigeDossiers
     *
     * @return Collection
     */
    public function getCommandeLitigeDossiers()
    {
        return $this->commandeLitigeDossiers;
    }

    /**
     * Get commentaireClient
     *
     * @return CommentaireClient
     */
    public function getCommentaireClient()
    {
        return $this->commentaireClient;
    }

    /**
     * Set commentaireClient
     *
     * @param CommentaireClient $commentaireClient
     *
     * @return Commande
     */
    public function setCommentaireClient(CommentaireClient $commentaireClient = null)
    {
        $this->commentaireClient = $commentaireClient;

        return $this;
    }

    /**
     * Add commentaireInterne
     *
     * @param CommentaireInterne $commentaireInterne
     *
     * @return Commande
     */
    public function addCommentaireInterne(CommentaireInterne $commentaireInterne)
    {
        $this->commentaireInterne[] = $commentaireInterne;

        return $this;
    }

    /**
     * Remove commentaireInterne
     *
     * @param CommentaireInterne $commentaireInterne
     */
    public function removeCommentaireInterne(CommentaireInterne $commentaireInterne)
    {
        $this->commentaireInterne->removeElement($commentaireInterne);
    }

    /**
     * Get commentaireInterne
     *
     * @return Collection
     */
    public function getCommentaireInterne()
    {
        return $this->commentaireInterne;
    }

    public function getCommentaireUtilisateurs() {
        return $this->commentaireClient->getReponses();
    }

    /**
     * @param LitigeDossier $litigeDossier
     *
     * @return $this
     */
    public function addLitigeDossier($litigeDossier)
    {
        $commandeLitigeDossier = new CommandeLitigeDossier($litigeDossier);
        $this->addCommandeLitigeDossier($commandeLitigeDossier);

        return $this;
    }

    /**
     * Add commandeLitigeDossier
     *
     * @param CommandeLitigeDossier $commandeLitigeDossier
     *
     * @return Commande
     */
    public function addCommandeLitigeDossier(
        CommandeLitigeDossier $commandeLitigeDossier
    )
    {
        $this->commandeLitigeDossiers[] = $commandeLitigeDossier->setCommande($this);

        return $this;
    }

}
