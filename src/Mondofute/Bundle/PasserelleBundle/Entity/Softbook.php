<?php

namespace Mondofute\Bundle\PasserelleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Softbook
 */
class Softbook extends Passerelle
{

    /**
     * @var string
     */
    private $paramA;

    /**
     * @var string
     */
    private $paramB;
    /**
     * @var \Mondofute\Bundle\PasserelleBundle\Entity\SoftbookType
     */
    private $type;

    /**
     * Get paramA
     *
     * @return string
     */
    public function getParamA()
    {
        return $this->paramA;
    }

    /**
     * Set paramA
     *
     * @param string $paramA
     * @return Softbook
     */
    public function setParamA($paramA)
    {
        $this->paramA = $paramA;

        return $this;
    }

    /**
     * Get paramB
     *
     * @return string
     */
    public function getParamB()
    {
        return $this->paramB;
    }

    /**
     * Set paramB
     *
     * @param string $paramB
     * @return Softbook
     */
    public function setParamB($paramB)
    {
        $this->paramB = $paramB;

        return $this;
    }

    public function getCatalogue()
    {
        switch ($this->type->getId()) {
            case 1:
                echo 'chargement du catalogue' . PHP_EOL;
                break;
            case 2:
                echo 'recup par prix vente';
                break;
        }
//        echo 'chargement du catalogue'.PHP_EOL;
    }

    /**
     * Get type
     *
     * @return \Mondofute\Bundle\PasserelleBundle\Entity\SoftbookType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param \Mondofute\Bundle\PasserelleBundle\Entity\SoftbookType $type
     * @return Softbook
     */
    public function setType(\Mondofute\Bundle\PasserelleBundle\Entity\SoftbookType $type = null)
    {
        $this->type = $type;

        return $this;
    }
}
