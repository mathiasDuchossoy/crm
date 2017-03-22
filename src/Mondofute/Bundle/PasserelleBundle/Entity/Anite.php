<?php

namespace Mondofute\Bundle\PasserelleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Anite
 */
class Anite extends Passerelle
{

    /**
     * @var string
     */
    private $param1;

    /**
     * @var string
     */
    private $param2;

    /**
     * Get param1
     *
     * @return string
     */
    public function getParam1()
    {
        return $this->param1;
    }

    /**
     * Set param1
     *
     * @param string $param1
     * @return Anite
     */
    public function setParam1($param1)
    {
        $this->param1 = $param1;

        return $this;
    }

    /**
     * Get param2
     *
     * @return string
     */
    public function getParam2()
    {
        return $this->param2;
    }

    /**
     * Set param2
     *
     * @param string $param2
     * @return Anite
     */
    public function setParam2($param2)
    {
        $this->param2 = $param2;

        return $this;
    }

    public function getStocks()
    {
        echo 'chargement du stock anite';
    }
}
