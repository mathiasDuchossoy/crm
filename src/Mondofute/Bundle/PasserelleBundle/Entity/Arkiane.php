<?php

namespace Mondofute\Bundle\PasserelleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Arkiane
 */
class Arkiane extends Passerelle
{

    /**
     * @var string
     */
    private $url;

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Arkiane
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }
}
