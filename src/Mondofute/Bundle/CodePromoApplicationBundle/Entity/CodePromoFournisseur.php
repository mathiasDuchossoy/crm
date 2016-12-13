<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 29/09/2016
 * Time: 16:11
 */

namespace Mondofute\Bundle\CodePromoApplicationBundle\Entity;


use Mondofute\Bundle\CodePromoBundle\Entity\CodePromo;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;

class CodePromoFournisseur
{

    /**
     * @var integer
     */
    private $id;
    /**
     * @var Fournisseur
     */
    private $fournisseur;
    /**
     * @var CodePromo
     */
    private $codePromo;
    /**
     * @var integer
     */
    private $type;

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
     * @return Fournisseur
     */
    public function getFournisseur()
    {
        return $this->fournisseur;
    }

    /**
     * Set fournisseur
     *
     * @param Fournisseur $fournisseur
     *
     * @return CodePromoFournisseur
     */
    public function setFournisseur(Fournisseur $fournisseur = null)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * Get codePromo
     *
     * @return CodePromo
     */
    public function getCodePromo()
    {
        return $this->codePromo;
    }

    /**
     * Set codePromo
     *
     * @param CodePromo $codePromo
     *
     * @return CodePromoFournisseur
     */
    public function setCodePromo(CodePromo $codePromo = null)
    {
        $this->codePromo = $codePromo;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return CodePromoFournisseur
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }
}
