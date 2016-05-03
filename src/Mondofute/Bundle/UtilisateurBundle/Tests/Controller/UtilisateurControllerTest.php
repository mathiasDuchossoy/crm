<?php

namespace Mondofute\Bundle\UtilisateurBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UtilisateurControllerTest extends WebTestCase
{
    /*
    public function testCompleteScenario()
    {
        // Create a new utilisateur to browse the application
        $utilisateur = static::createUtilisateur();

        // Create a new entry in the database
        $crawler = $utilisateur->request('GET', '/utilisateur/');
        $this->assertEquals(200, $utilisateur->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /utilisateur/");
        $crawler = $utilisateur->click($crawler->selectLink('Create a new entry')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form(array(
            'mondofute_bundle_utilisateurbundle_utilisateur[field_name]'  => 'Test',
            // ... other fields to fill
        ));

        $utilisateur->submit($form);
        $crawler = $utilisateur->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Test")')->count(), 'Missing element td:contains("Test")');

        // Edit the entity
        $crawler = $utilisateur->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'mondofute_bundle_utilisateurbundle_utilisateur[field_name]'  => 'Foo',
            // ... other fields to fill
        ));

        $utilisateur->submit($form);
        $crawler = $utilisateur->followRedirect();

        // Check the element contains an attribute with value equals "Foo"
        $this->assertGreaterThan(0, $crawler->filter('[value="Foo"]')->count(), 'Missing element [value="Foo"]');

        // Delete the entity
        $utilisateur->submit($crawler->selectButton('Delete')->form());
        $crawler = $utilisateur->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Foo/', $utilisateur->getResponse()->getContent());
    }

    */
}
