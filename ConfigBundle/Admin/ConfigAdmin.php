<?php

namespace DH\ConfigBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Validator\Constraints\InlineConstraint;

class ConfigAdmin extends Admin {

    protected function configureListFields(ListMapper $mapper) {
        $mapper
            ->addIdentifier('name', null, array('label' => 'Name'))
            ->add('defaultValue')
            ->add('currentValue')
            ->add('description')
            ->add('type')
            ->add('min')
            ->add('max')
            ->add('section')
            ->add('updated')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $mapper) {
        $mapper
            ->add('name')
            ->add('section')
            ->add('type')
        ;
    }

    protected function configureFormFields(FormMapper $mapper) {
        $mapper
            ->add('name', null, array('attr' => array('class' => 'defaultTextActive', 'title' => 'enter name')))
            ->add('defaultValue', null, array('required' => false, 'attr' => array('class' => 'defaultTextActive', 'title' => 'enter default value')))
            ->add('currentValue', null, array('required' => true, 'attr' => array('class' => 'defaultTextActive', 'title' => 'enter current value')))
            ->add('description', null, array('required' => true, 'attr' => array('class' => 'defaultTextActive', 'title' => 'enter description')))
            ->add('type', 'choice', array('choices' => array('string' => 'string', 'integer' => 'integer', 'float' => 'float', 'date' => 'date', 'datetime' => 'datetime', 'timestamp' => 'timestamp', 'choice' => 'choice', 'multiplechoice' => 'multiplechoice')))
            ->add('min', null, array('required' => false, 'attr' => array('class' => 'defaultTextActive', 'title' => 'enter minimum value (optional)')))
            ->add('max', null, array('required' => false, 'attr' => array('class' => 'defaultTextActive', 'title' => 'enter maximum value (optional)')))
            ->add('section', null, array('attr' => array('class' => 'defaultTextActive', 'title' => 'enter section name (optional)')))
            ->add('updated', 'datetime', array('required' => false, 'read_only' => true))
            ->setHelps(array(
                'min' => 'If you enter a minimum value, it will act as a constraint on the default and current value.',
                'max' => 'If you enter a maximum value, it will act as a constraint on the default and current value.',
                'section' => 'The section parameter is provided for your convenience and for better readability if you have a large number of settings.'
            ))
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        switch ((string)$object->getType())
        {
            case 'date':
            case 'datetime':
                if (is_object($object->getDefaultValue())) $object->setDefaultValue($object->getDefaultValue()->getTimestamp());
                elseif ($object->getDefaultValue() != '') {$def = new \DateTime($object->getDefaultValue());$object->setDefaultValue($cur->getTimestamp());}
                if (is_object($object->getCurrentValue())) $object->setCurrentValue($object->getCurrentValue()->getTimestamp());
                elseif ($object->getCurrentValue() != '') {$cur = new \DateTime($object->getCurrentValue());$object->setCurrentValue($cur->getTimestamp());}
                if (is_object($object->getMin())) $object->setMin($object->getMin()->getTimestamp());
                elseif ($object->getMin() != '') {$min = new \DateTime($object->getMin());$object->setMin($min->getTimestamp());}
                if (is_object($object->getMax())) $object->setMax($object->getMax()->getTimestamp());
                elseif ($object->getMax() != '') {$max = new \DateTime($object->getMax());$object->setMax($max->getTimestamp());}
        }
        if ($object->getMin() != '' AND $object->getMax() != '' AND $object->getMin() > $object->getMax())
        {
            $errorElement
                ->with('min')
                ->assertMin(array('limit' => $object->getMin(), 'message' => 'Error: Min / max outside the specified limits'))
                ->addViolation('Max should not be less than min')
                ->end()
            ;
            $errorElement
                ->with('max')
                ->assertMin(array('limit' => $object->getMin(), 'message' => 'Error: Min / max outside the specified limits'))
                ->addViolation('Max should not be less than min')
                ->end()
            ;
        }
        if ($object->getMin() != '' AND $object->getMin() > $object->getDefaultValue())
        {
            $errorElement
                ->with('defaultValue')
                ->assertMin(array('limit' => $object->getMin(), 'message' => 'Error: Default value outside the specified limits'))
                ->addViolation('Default value should not be less than min')
                ->end()
            ;
        }
        if ($object->getMax() != '' AND $object->getMax() < $object->getDefaultValue())
        {
            $errorElement
                ->with('defaultValue')
                ->assertMax(array('limit' => $object->getMax(), 'message' => 'Error: default value outside the specified limits'))
                ->addViolation('Default value should not be greater than max')
                ->end()
            ;
        }
        if ($object->getMin() != '' AND $object->getMin() > $object->getCurrentValue())
        {
            $errorElement
                ->with('currentValue')
                ->assertMin(array('limit' => $object->getMin(), 'message' => 'Error: current value outside the specified limits'))
                ->addViolation('Current value should not be less than min')
                ->end()
            ;
        }
        if ($object->getMax() != '' AND $object->getMax() < $object->getCurrentValue())
        {
            $errorElement
                ->with('currentValue')
                ->assertMin(array('limit' => $object->getMax(), 'message' => 'Error: current value outside the specified limits'))
                ->addViolation('Current value should not be greater than max')
                ->end()
            ;
        }
    }

    public function preUpdate($object)
    {//object to string conversion
        switch ((string)$object->getType())
        {
            case 'date':
            case 'datetime':
                $format = ($object->getType() == 'date')? 'Y-m-d': 'Y-m-d H:i:s';
                if ($object->getCurrentValue() != '') {$cur = new \DateTime();$cur->setTimestamp($object->getCurrentValue());$object->setCurrentValue($cur->format($format));}
                if ($object->getDefaultValue() != '') {$def = new \DateTime();$def->setTimestamp($object->getDefaultValue());$object->setDefaultValue($def->format($format));}
                if ($object->getMin() != '') {$min = new \DateTime();$min->setTimestamp($object->getMin());$object->setMin($min->format($format));}
                if ($object->getMax() != '') {$max = new \DateTime();$max->setTimestamp($object->getMax());$object->setMax($max->format($format));}
                break;
        }        
    }
}