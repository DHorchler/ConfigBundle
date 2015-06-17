<?php

namespace DHorchler\ConfigBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Validator\Constraints\InlineConstraint;

class ConfigAdmin extends Admin {

    protected function configureListFields(ListMapper $mapper) {
        $mapper
            ->addIdentifier('name', null, array('label' => 'Name'))
            ->add('defaultValue')
            ->add('currentValue')
            ->add('description')
            ->add('type')
            ->add('filter')
            ->add('section')
            ->add('created')
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
            ->add('name', null, array('attr' => array('class' => 'defaultText', 'title' => 'enter name')))
            ->add('defaultValue', null, array('required' => false, 'attr' => array('class' => 'defaultText', 'title' => 'enter default value')))
            ->add('currentValue', null, array('required' => true, 'attr' => array('class' => 'defaultText', 'title' => 'enter current value')))
            ->add('description', null, array('required' => true, 'attr' => array('class' => 'defaultText', 'title' => 'enter description')))
            ->add('type', 'choice', array('choices' => array('string' => 'string', 'textarea' => 'textarea', 'html' => 'html', 'integer' => 'integer', 'float' => 'float', 'date' => 'date', 'datetime' => 'datetime', 'choice' => 'choice', 'multiplechoice' => 'multiplechoice')))
            ->add('filter', null, array('required' => false, 'attr' => array('class' => 'defaultText', 'title' => 'optional')))
            ->add('section', null, array('attr' => array('class' => 'defaultText', 'title' => 'enter section name (optional)')))
            ->add('updated', 'datetime', array('required' => false, 'read_only' => true))
            ->setHelps(array(
                'filter' => 'formats: (value: integer, float or string): min:value max:value range:value..value choices:choice1,choice2,choice3 regexp:/regular expresion/',
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
                /*if (is_object($object->getDefaultValue())) $object->setDefaultValue($object->getDefaultValue()->getTimestamp());
                elseif ($object->getDefaultValue() != '') {$def = new \DateTime($object->getDefaultValue());$object->setDefaultValue($cur->getTimestamp());}
                if (is_object($object->getCurrentValue())) $object->setCurrentValue($object->getCurrentValue()->getTimestamp());
                elseif ($object->getCurrentValue() != '') {$cur = new \DateTime($object->getCurrentValue());$object->setCurrentValue($cur->getTimestamp());}
                if (is_object($object->getMin())) $object->setMin($object->getMin()->getTimestamp());
                elseif ($object->getMin() != '') {$min = new \DateTime($object->getMin());$object->setMin($min->getTimestamp());}
                if (is_object($object->getMax())) $object->setMax($object->getMax()->getTimestamp());
                elseif ($object->getMax() != '') {$max = new \DateTime($object->getMax());$object->setMax($max->getTimestamp());}*/
                break;
            case 'multiplechoice':
                if (is_array($object->getDefaultValue())) $object->setDefaultValue(implode(',', $object->getDefaultValue()));
                if (is_array($object->getCurrentValue())) $object->setCurrentValue(implode(',', $object->getCurrentValue()));
                break;
        }
        $filter = $object->getFilter();
        if (!empty($filter) AND $separatorPos = strpos($filter, ':')) {
            $filterType = strtolower(substr($filter, 0, $separatorPos));
            $filterValue = substr($filter, $separatorPos+1);
            switch ($filterType) {
                case 'min':
                    if ($filterValue > $object->getDefaultValue())
                        $errorElement
                            ->with('defaultValue')
                            ->assertRange(array('min' => $object->getFilter()))
                            ->addViolation('defaultValue should not be less than min')
                            ->end();
                    if ($filterValue > $object->getCurrentValue())
                        $errorElement
                            ->with('currentValue')
                            ->assertRange(array('min' => $object->getFilter()))
                            ->addViolation('currentValue should not be less than min')
                            ->end();
                    break;
                case 'max':
                    if ($filterValue < $object->getDefaultValue())
                        $errorElement
                            ->with('defaultValue')
                            ->assertRange(array('max' => $object->getFilter()))
                            ->addViolation('defaultValue should not be major than max')
                            ->end();
                    if ($filterValue < $object->getCurrentValue())
                        $errorElement
                            ->with('currentValue')
                            ->assertRange(array('max' => $object->getFilter()))
                            ->addViolation('currentValue should not be major than max')
                            ->end();
                    break;
                case 'range':default;
                    $filterArray = explode('..', $filterValue);
                    if (count($filterArray)<2) break;
                    if ($filterArray[0] > $object->getDefaultValue())
                        $errorElement
                            ->with('defaultValue')
                            ->assertRange(array('min' => $object->getFilter()))
                            ->addViolation('defaultValue should not be less than min')
                            ->end();
                    if ($filterArray[0] > $object->getCurrentValue())
                        $errorElement
                            ->with('currentValue')
                            ->assertRange(array('min' => $object->getFilter()))
                            ->addViolation('currentValue should not be less than min')
                            ->end();
                    if ($filterArray[1] < $object->getDefaultValue())
                        $errorElement
                            ->with('defaultValue')
                            ->assertRange(array('max' => $object->getFilter()))
                            ->addViolation('defaultValue should not be major than max')
                            ->end();
                    if ($filterArray[1] < $object->getCurrentValue())
                        $errorElement
                            ->with('currentValue')
                            ->assertRange(array('max' => $object->getFilter()))
                            ->addViolation('currentValue should not be major than max')
                            ->end();
                    break;
            }
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