<?php

namespace DH\ConfigBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $client->restart();// Clears all cookies and the history
        //dashboard
        $crawler = $client->request('GET', '/admin/dashboard');
        $this->assertTrue($crawler->filter('html:contains("Settings")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Add new")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("List")')->count() > 0);
        $linkList = $crawler->filter('a:contains("List")')->eq(0)->link();
        $crawler = $client->click($linkList);
        //list
        $this->assertTrue($crawler->filter('html:contains("Default Value")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Current Value")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Description")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Type")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Min")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Max")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Type")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Choices")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Section")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Type")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Add new")')->count() > 0);
        $linkAdd = $crawler->filter('a:contains("Add new")')->eq(0)->link();
        $crawler = $client->click($linkAdd);        
        //add
        $this->assertTrue($crawler->filter('html:contains("Default Value")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Current Value")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Description")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Type")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Min")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Max")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Type")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Choices")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Section")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Type")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Add new")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Return to list")')->count() > 0);
        $form = $crawler->selectButton('Create')->form();
        $uri = $form->getUri();
        $token = substr($uri, strpos($uri, 'uniqid=')+7);
        $form[$token.'[type]']->select('string');
        $form[$token.'[name]'] = 'unittest: string1';
        $form[$token.'[description]'] = 'setting created by unittest: string1';
        /*$crawler = $client->submit($form);
        //add submit with error
        $this->assertFalse($client->getResponse()->isRedirect());
        $form[$token.'[currentValue]'] = 'current';
        $crawler = $client->submit($form);
        //add submit with error
        $this->assertFalse($client->getResponse()->isRedirect());*/
        $form[$token.'[defaultValue]'] = 'default';
        $crawler = $client->submit($form);
        //add submit
        $this->assertTrue($client->getResponse()->isRedirect());
        //$this->assertTrue($crawler->filter('html:contains("Item has been successfully created.")')->count() > 0);
        /*$linkDelete = $crawler->selectLink('Delete')->link();
        $crawler = $client->click($linkDelete);
        $this->assertTrue($client->getResponse()->isRedirect());*/
    }
}
