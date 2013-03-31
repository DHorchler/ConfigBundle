<?php

namespace DH\ConfigBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConfigTestController extends WebTestCase
{
    protected $client;
    protected $verbose;
    
    public function __construct()
    {
        $this->client = static::createClient();
        $this->verbose = true;
    }
    
    public function testIndex()
    {
        //dashboard
        $crawler = $this->client->request('GET', '/admin/dashboard');
        $this->assertTrue($crawler->filter('html:contains("Settings")')->count() > 0, 'string "Settings" not found on dashboad page');
        $this->assertTrue($crawler->filter('html:contains("Add new")')->count() > 0, 'string "Add new" not found on dashboad page');
        $this->assertTrue($crawler->filter('html:contains("List")')->count() > 0, 'string "List" not found on dashboad page');
        $linkList = $crawler->filter('a:contains("List")')->eq(0)->link();
        $crawler = $this->client->click($linkList);

        //list
        $this->assertTrue($crawler->filter('html:contains("Default Value")')->count() > 0, 'string "Default Value" not found on list page');
        $this->assertTrue($crawler->filter('html:contains("Current Value")')->count() > 0, 'string "Current Value" not found on list page');
        $this->assertTrue($crawler->filter('html:contains("Description")')->count() > 0, 'string "Description" not found on list page');
        $this->assertTrue($crawler->filter('html:contains("Type")')->count() > 0, 'string "Type" not found on list page');
        $this->assertTrue($crawler->filter('html:contains("Min")')->count() > 0, 'string "Min" not found on list page');
        $this->assertTrue($crawler->filter('html:contains("Max")')->count() > 0, 'string "Max" not found on list page');
        $this->assertTrue($crawler->filter('html:contains("Type")')->count() > 0, 'string "Type" not found on list page');
        $this->assertTrue($crawler->filter('html:contains("Choices")')->count() > 0, 'string "Choices" not found on list page');
        $this->assertTrue($crawler->filter('html:contains("Section")')->count() > 0, 'string "Section" not found on list page');
        $this->assertTrue($crawler->filter('html:contains("Add new")')->count() > 0, 'string "Add new" not found on list page');
        
        //data types
        $this->dataTypeTest('integer', 'phpunit integer test1', 'integer added by phpunit, test1', 10, 20, 1, 99);
    }
    
    public function dataTypeTest($dataType, $name, $description, $defaultValue, $currentValue, $min = '', $max = '', $choices = '')
    {
        if ($this->verbose) echo 'start '.$dataType.' data type test, going to list page'.PHP_EOL;
        $crawler = $this->goToListPage();
        //create
        if ($this->verbose) echo 'starting creation of '.$dataType.' setting'.PHP_EOL;
        $crawler = $this->createSetting($crawler, $dataType, $name, $description, $defaultValue, $currentValue, $min, $max, $choices);
        //back to list
        if ($this->verbose) echo 'setting was created, going back to list page'.PHP_EOL;
        $linkList = $crawler->filter('a:contains("Return to list")')->eq(0)->link();
        $crawler = $this->client->click($linkList);
        //check list page
        if ($this->verbose) echo 'checking if new setting appears in list page'.PHP_EOL;
        $this->assertTrue($crawler->filter('a:contains("'.$name.'")')->count() > 0, 'name of newly created setting '.$name.' not found on list page');
        $linkEdit = $crawler->filter('a:contains("'.$name.'")')->eq(0)->link();
        $uri = $linkEdit->getUri();
        $id = ltrim(strrchr(strstr($uri, '/edit', true), '/'), '/');//get ID of newly created setting
        $select = $crawler->filterXPath('//select[@name="action"]');
        $checkbox = $crawler->filterXPath('//input[@type="checkbox" and @name="idx[]" and @value="'.$id.'"]');
        $this->assertTrue($checkbox->count() > 0);
        //go to edit page
        if ($this->verbose) echo 'going to edit page'.PHP_EOL;
        $crawler = $this->client->click($linkEdit);
        //check edit page
        if ($this->verbose) echo 'checking edit page'.PHP_EOL;
        $this->checkEditpage($crawler, $dataType, $name, $description, $defaultValue, $currentValue, $min, $max, $choices);
        //back to list
        if ($this->verbose) echo 'going back to list page'.PHP_EOL;
        $linkList = $crawler->filter('a:contains("Return to list")')->eq(0)->link();
        $crawler = $this->client->click($linkList);      
        // prepare delete
        if ($this->verbose) echo 'preparing deletion of new setting'.PHP_EOL;
        $crawler = $this->deleteSettingById($crawler, $id);
        $this->assertTrue(count($crawler->filter('a:contains("'.$name.'")')) == 0, 'setting '.$name.', ID: '.$id.' could not be deleted');
    }
    
    public function goToListPage()
    {    
        $crawler = $this->client->request('GET', '/admin/dashboard');
        $linkList = $crawler->filter('a:contains("List")')->eq(0)->link();
        $crawler = $this->client->click($linkList);
        $linkList = $crawler->filter('a:contains("Add new")')->eq(0)->link();
        return $this->client->click($linkList);
    }

    public function checkEditPage($crawler, $dataType, $name, $description, $defaultValue, $currentValue, $min, $max, $choices)
    {    
        $this->assertTrue($crawler->filter('html:contains("Defaultvalue")')->count() > 0, 'string "Defaultvalue" not found on edit page');
        $this->assertTrue($crawler->filter('html:contains("Currentvalue")')->count() > 0, 'string "Currentvalue" not found on edit page');
        $this->assertTrue($crawler->filter('html:contains("Description")')->count() > 0, 'string "Description" not found on edit page');
        $this->assertTrue($crawler->filter('html:contains("Type")')->count() > 0, 'string "Type" not found on edit page');
        $this->assertTrue($crawler->filter('html:contains("Min")')->count() > 0, 'string "Min" not found on edit page');
        $this->assertTrue($crawler->filter('html:contains("Max")')->count() > 0, 'string "Max" not found on edit page');
        $this->assertTrue($crawler->filter('html:contains("Section")')->count() > 0, 'string "Section" not found on edit page');
        $this->assertTrue($crawler->filter('html:contains("Updated")')->count() > 0, 'string "Updated" not found on edit page');
        $this->assertTrue($crawler->filter('html:contains("'.$name.'")')->count() > 0, 'name string "'.$name.'" not found on edit page');
        $form = $crawler->selectButton('Update')->form();
        $uri = $form->getUri();
        $token = substr($uri, strpos($uri, 'uniqid=')+7);//get form token
        foreach ($form AS $field) print_r($field);
    }

    public function createSetting($crawler, $dataType, $name, $description, $defaultValue, $currentValue, $min = '', $max = '', $choices = '')
    {    
        $form = $crawler->selectButton('Create')->form();
        $uri = $form->getUri();
        $token = substr($uri, strpos($uri, 'uniqid=')+7);//get form token
        $form[$token.'[type]']->select($dataType);
        $form[$token.'[name]'] = $name;
        $form[$token.'[description]'] = $description;
        $form[$token.'[defaultValue]'] = $defaultValue;
        $form[$token.'[currentValue]'] = $currentValue;
        if ($min != '') $form[$token.'[min]'] = $min;
        if ($max != '') $form[$token.'[max]'] = $max;
        if ($choices != '') $form[$token.'[choices]'] = $choices;
        $crawler = $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());//if no redirection takes place, most probably the element already exists. In that case delete it manually.
        return $this->client->followRedirect();
    }

    public function deleteSettingById($crawler, $id)
    {    
        $numcheckboxes = $crawler->filterXPath('//input[@type="checkbox" and @name="idx[]"]')->count() - 1;
        $this->assertTrue($crawler->filterXPath('//input[@type="checkbox" and @name="idx[]"]')->last()->filterXPath('//input[@type="checkbox" and @name="idx[]" and @value="'.$id.'"]')->count() > 0, 'last checkbox on the list page must be the new setting!');
        $form = $crawler->selectButton('OK')->form();        
        $form['idx['.$numcheckboxes.']']->tick();
        $crawler = $this->client->submit($form);
        if ($this->verbose) echo 'deletion submitted'.PHP_EOL;
        $form = $crawler->selectButton('Yes, execute')->form();
        $crawler = $this->client->submit($form);
        if ($this->verbose) echo 'deletion confirmed'.PHP_EOL;
        return $crawler;
    }

    public function showDOM($crawler)
    {    
        //foreach ($crawler as $domElement) print $domElement->nodeName;
        $html = '';
        foreach ($crawler as $domElement) $html.= $domElement->ownerDocument->saveHTML();
        return $html;
        /*$doc = new \DOMDocument('1.0');
        foreach ($crawler as $domElement ) {$clone = $doc->importNode($domElement->cloneNode(true), true); $doc->appendChild($clone);}
        return $doc->saveHTML();*/
    }
}
