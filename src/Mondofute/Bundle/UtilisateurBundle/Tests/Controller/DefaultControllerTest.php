<?php

namespace Mondofute\Bundle\UtilisateurBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $utilisateur = static::createUtilisateur();

        $crawler = $utilisateur->request('GET', '/');

        $this->assertContains('Hello World', $utilisateur->getResponse()->getContent());
    }
}
