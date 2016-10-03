<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 29/09/2016
 * Time: 16:11
 */

namespace Mondofute\Bundle\CodePromoApplicationBundle\Entity;


class CodePromoFournisseur
{

    /**
     * @var integer
     */
    private $id;
    /**
     * @var \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur
     */
    private $fournisseur;
    /**
     * @var \Mondofute\Bundle\CodePromoBundle\Entity\CodePromo
     */
    private $codePromo;

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
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get fournisseur
     *
     * @return \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur
     */
    public function getFournisseur()
    {
        return $this->fournisseur;
    }

    /**
     * Set fournisseur
     *
     * @param \Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseur
     *
     * @return CodePromoFournisseur
     */
    public function setFournisseur(\Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur $fournisseur = null)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * Get codePromo
     *
     * @return \Mondofute\Bundle\CodePromoBundle\Entity\CodePromo
     */
    public function getCodePromo()
    {
        return $this->codePromo;
    }

    /**
     * Set codePromo
     *
     * @param \Mondofute\Bundle\CodePromoBundle\Entity\CodePromo $codePromo
     *
     * @return CodePromoFournisseur
     */
    public function setCodePromo(\Mondofute\Bundle\CodePromoBundle\Entity\CodePromo $codePromo = null)
    {
        $this->codePromo = $codePromo;

        return $this;
    }
}
