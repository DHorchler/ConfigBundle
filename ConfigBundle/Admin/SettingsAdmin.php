<?php

// src/dh/SettingsBundle/Admin/CategoriesAdmin.php

namespace dh\Settings\Bundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class SettingsAdmin extends Admin {

    protected function configureListFields(ListMapper $mapper) {
        $mapper
            ->addIdentifier('name', null, array('label' => 'Name'))
            ->add('defaultValue')
            ->add('currentValue')
            ->add('description')
            ->add('section')
            ->add('updated')
        ;
    }

    /*protected function configureDatagridFilters(DatagridMapper $mapper) {
        $mapper
            ->add('name')
            ->add('section')
        ;
    }*/

    protected function configureFormFields(FormMapper $mapper) {
        $mapper
            ->add('name')
            ->add('defaultValue', null, array('read_only' => true))
            ->add('currentValue', null, array('required' => true))
            ->add('description', null, array('required' => true))
            ->add('section')
            ->add('updated', null, array('read_only' => true))
        ;
    }
}