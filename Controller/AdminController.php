<?php
// src/DHorchler\ConfigBundle/Controller/AdminController.php
namespace DHorchler\ConfigBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends \Sonata\AdminBundle\Controller\CRUDController
{
    public function editAction($id = null, Request $request = null)
    {
        // the key used to lookup the template
        $templateKey = 'edit';

        $id = $this->get('request')->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('EDIT', $object)) {
            throw new AccessDeniedException();
        }

        $this->admin->setSubject($object);

        /** @var $form \Symfony\Component\Form\Form */
        $form = $this->admin->getForm();
        $form->setData($object);
        if (strpos($object->__toString(), 'Settings:') === 0) $this->manageTypes($object, $form);
        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                $this->admin->update($object);
                $this->get('session')->getFlashBag()->add('sonata_flash_success', 'update success');
                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(array(
                        'result'    => 'ok',
                        'objectId'  => $this->admin->getNormalizedIdentifier($object)
                    ));
                }

                // redirect to edit mode
                return $this->redirectTo($object);
            }

            // show an error message if the form failed validation
            if (!$isFormValid) {
                if (!$this->isXmlHttpRequest()) {
                    $this->get('session')->getFlashBag()->add('sonata_flash_error', 'edit error');
                }
            } elseif ($this->isPreviewRequested()) {
                // enable the preview template if the form was valid and preview was requested
                $templateKey = 'preview';
            }
        }

        $view = $form->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());
        if ($object->getType() == 'html') return $this->render($this->admin->getTemplate($templateKey), array('ckeditor' => true, 'action' => 'edit', 'form'   => $view,'action' => 'edit', 'object' => $object));
        return $this->render($this->admin->getTemplate($templateKey), array(
            'action' => 'edit',
            'form'   => $view,'action' => 'edit',
            'object' => $object,
        ));
    }

    private function manageTypes($object, $form)
    {
            $form->remove('type');
            $form->add('type', 'choice', array('disabled' => true, 'choices' => array($object->getType(), 'string', 'integer', 'float', 'textarea', 'html', 'date', 'datetime', 'choice', 'multiplechoice')));
            $type = (string)$object->getType();
            switch ($type)
            {
                case 'date':
                case 'datetime':
                    $separator = '-';
                    $format = ($object->getType() == 'date')? 'YYYY-MM-DD': 'YYYY-MM-DD HH:MM:SS';
                    $form->remove('defaultValue');
                    if ($object->getDefaultValue() == '')
                    {
                        $form->add('defaultValue', null, array('required' => true, 'attr' => array('class' => 'defaultText', 'title' => $format)));
                    }
                    else
                    {
                        if ($type == 'date') $object->setDefaultValue(new \DateTime(str_replace(' ', $separator, $object->getDefaultValue())));
                        else $object->setDefaultValue(new \DateTime(preg_replace('%(\d{4}) (\d{2}) (\d{2}) (\d{2}) (\d{2}) (\d{2})%', '$1-$2-$3 $4:$5:$6', $object->getDefaultValue())));
                        $form->add('defaultValue', 'date');
                    }                    
                    $form->remove('currentValue');
                    if ($object->getCurrentValue() == '')
                    {
                        $form->add('currentValue', null, array('required' => true, 'attr' => array('class' => 'defaultText', 'title' => $format)));
                    }
                    else
                    {
                        if ($type == 'date') $object->setCurrentValue(new \DateTime(str_replace(' ', $separator, $object->getCurrentValue())));
                        else $object->setCurrentValue(new \DateTime(preg_replace('%(\d{4}) (\d{2}) (\d{2}) (\d{2}) (\d{2}) (\d{2})%', '$1-$2-$3 $4:$5:$6', $object->getCurrentValue())));
                        $form->add('currentValue', 'date');
                    }                    
                    break;
                case 'choice':
                    $choices = array();
                    $choicesRaw = explode(',', str_replace('(', '', str_replace(')', '', $object->getFilter())));
                    foreach ($choicesRaw AS $cr) $choices[$cr] = $cr;
                    $defChoices = array('choices' => $choices);
                    $form->remove('defaultValue');
                    $form->add('defaultValue', 'choice', $defChoices);
                    $curChoices = array('choices' => $choices);
                    $form->remove('currentValue');
                    $form->add('currentValue', 'choice', $curChoices);
                    break;
                case 'multiplechoice':
                    $choices = array();
                    $choicesRaw = explode(',', str_replace('(', '', str_replace(')', '', $object->getFilter())));
                    foreach ($choicesRaw AS $cr) $choices[$cr] = $cr;
                    $defChoicesRaw = array();
                    $defChoicesRaw = explode(',', $object->getDefaultValue());
                    foreach ($defChoicesRaw AS $dcr) $defChoices[$dcr] = $dcr;
                    $object->setDefaultValue($defChoices);
                    $defChoices = array('multiple' => true, 'choices' => $choices);
                    $form->remove('defaultValue');
                    $form->add('defaultValue', 'choice', $defChoices);
                    $curChoicesRaw = array();
                    $curChoices = explode(',', $object->getCurrentValue());
                    foreach ($curChoicesRaw AS $ccr) $curChoices[$ccr] = $ccr;
                    $object->setCurrentValue($curChoices);
                    $curChoices = array('multiple' => true, 'choices' => $choices);
                    $form->remove('currentValue');
                    $form->add('currentValue', 'choice', $curChoices);
                    break;
                case 'textarea':
                    $form->remove('defaultValue');
                    $form->add('defaultValue', 'textarea');
                    $form->remove('currentValue');
                    $form->add('currentValue', 'textarea');
                    break;
                case 'html':
                    $form->remove('defaultValue');
                    $form->add('defaultValue', 'textarea', array('attr' => array('class' => 'ckeditor')));
                    $form->remove('currentValue');
                    $form->add('currentValue', 'textarea', array('attr' => array('class' => 'ckeditor')));
                    break;
                default://had to do this to show the violation text
                    $form->remove('defaultValue')->add('defaultValue', null, array('required' => false, 'attr' => array('class' => 'defaultTextActive', 'title' => 'enter default value')))
                        ->remove('currentValue')->add('currentValue', null, array('required' => true, 'attr' => array('class' => 'defaultTextActive', 'title' => 'enter current value')));
                    break;
            }            
            //echo $this->admin->getFormFieldDescription('filter')->getHelp();    
            /*$form->remove('filter');
            $form->add('filter', 'hidden');*/
    }
}