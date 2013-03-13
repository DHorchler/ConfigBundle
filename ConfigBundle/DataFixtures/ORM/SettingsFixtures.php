<?php
// src/dh/ConfigBundle/DataFixtures/ORM/SettingsFixtures.php

namespace DH\ConfigBundle\DataFixtures\ORM;

use DH\ConfigBundle\Entity\Settings;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class SettingFixtures implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $setting1 = new Settings();
        $setting1->setName('sitename');
        $setting1->setDefaultValue('dh Settings');
        $setting1->setCurrentValue('dh Settings');
        $setting1->setDescription('site name to appear in header');
        $setting1->setSection('layout');
        $setting1->setType('string');
        $manager->persist($setting1);

        $setting2 = new Settings();
        $setting2->setName('show_left_column');
        $setting2->setDefaultValue('yes');
        $setting2->setCurrentValue('yes');
        $setting2->setDescription('if set to yes, left column is shown');
        $setting2->setSection('layout');
        $setting2->setType('boolean');
        $manager->persist($setting2);     

        $setting3 = new Settings();
        $setting3->setName('show_right_column');
        $setting3->setDefaultValue('yes');
        $setting3->setCurrentValue('yes');
        $setting3->setDescription('if set to yes, right column is shown');
        $setting3->setSection('layout');
        $setting3->setType('boolean');
        $manager->persist($setting3);     

        $setting4 = new Settings();
        $setting4->setName('num_articles_per_page');
        $setting4->setDefaultValue('10');
        $setting4->setCurrentValue('10');
        $setting4->setDescription('number ot articles to be shown per page');
        $setting4->setSection('layout');
        $setting4->setSection('layout');
        $setting4->setType('integer');
        $manager->persist($setting4);

        $setting5 = new Settings();
        $setting5->setName('user_block_top_left');
        $setting5->setDefaultValue('');
        $setting5->setCurrentValue('');
        $setting5->setDescription('HTML code to be placed in the upper area of the left column');
        $setting5->setSection('layout');
        $setting5->setType('string');
        $manager->persist($setting5);     

        $setting6 = new Settings();
        $setting6->setName('user_block_bottom_left');
        $setting6->setDefaultValue('');
        $setting6->setCurrentValue('');
        $setting6->setDescription('HTML code to be placed in the bottom area of the left column');
        $setting6->setSection('layout');
        $setting6->setType('string');
        $manager->persist($setting6);     

        $manager->flush();
    }
}
