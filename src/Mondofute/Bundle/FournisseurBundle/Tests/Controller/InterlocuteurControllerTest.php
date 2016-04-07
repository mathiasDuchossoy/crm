<?php

namespace Mondofute\Bundle\FournisseurBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InterlocuteurControllerTest extends WebTestCase
{
    public function testNew()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/new');
    }

    public function testEdit()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/edit');
    }

}
