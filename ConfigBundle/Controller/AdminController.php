<?php
// src/DH\ConfigBundle/Controller/AdminController.php
namespace DH\ConfigBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminController extends \Sonata\AdminBundle\Controller\CRUDController
{
    public function editAction($id = null)
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
        if (strpos($object->__toString(), 'Settings:') === 0) $this->manageTypes($object, $form);//DH
        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                $this->admin->update($object);
                $this->get('session')->setFlash('sonata_flash_success', 'flash_edit_success');

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
                    $this->get('session')->setFlash('sonata_flash_error', 'flash_edit_error');
                }
            } elseif ($this->isPreviewRequested()) {
                // enable the preview template if the form was valid and preview was requested
                $templateKey = 'preview';
            }
        }

        $view = $form->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());

        return $this->render($this->admin->getTemplate($templateKey), array(
            'action' => 'edit',
            'form'   => $view,
            'object' => $object,
        ));
    }

    private function manageTypes($object, $form)
    {
            $form->remove('type');
            $form->add('type', 'choice', array('disabled' => true, 'choices' => array($object->getType(), 'string', 'integer', 'float', 'date', 'datetime', 'timestamp', 'range', 'choice', 'multiplechoice')));
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
                    $form->remove('min');
                    if ($object->getMin() == '')
                    {
                        $form->add('min', null, array('required' => false, 'attr' => array('class' => 'defaultText', 'title' => $format)));
                    }
                    else
                    {
                        if ($type == 'date') $object->setMin(new \DateTime(str_replace(' ', $separator, $object->getMin())));
                        else $object->setMin(new \DateTime(preg_replace('%(\d{4}) (\d{2}) (\d{2}) (\d{2}) (\d{2}) (\d{2})%', '$1-$2-$3 $4:$5:$6', $object->getMin())));
                        $form->add('min', 'date', array('required' => false));
                    }                    
                    $form->remove('max');
                    if ($object->getMax() == '')
                    {
                        $form->add('max', null, array('required' => false, 'attr' => array('class' => 'defaultText', 'title' => $format)));
                    }
                    else
                    {
                        if ($type == 'date') $object->setMax(new \DateTime(str_replace(' ', '-', $object->getMax())));
                        else $object->setMin(new \DateTime(preg_replace('%(\d{4}) (\d{2}) (\d{2}) (\d{2}) (\d{2}) (\d{2})%', '$1-$2-$3 $4:$5:$6', $object->getMin())));
                        $form->add('max', 'date', array('required' => false));
                    }
                    //echo $this->admin->getFormFieldDescription('min')->getHelp();
                    break;
                case 'url':
                case 'email':
                case 'string';
                    $form->remove('min');
                    $form->add('min', 'hidden');
                    $form->remove('max');
                    $form->add('max', 'hidden');
                    break;
                default://had to do this to show the violation text
                    $form->remove('defaultValue')->add('defaultValue', null, array('required' => false, 'attr' => array('class' => 'defaultTextActive', 'title' => 'enter default value')))
                        ->remove('currentValue')->add('currentValue', null, array('required' => true, 'attr' => array('class' => 'defaultTextActive', 'title' => 'enter current value')))
                        ->remove('min')->add('min', null, array('required' => false, 'attr' => array('class' => 'defaultTextActive', 'title' => 'enter minimum value (optional)')))
                        ->remove('max')->add('max', null, array('required' => false, 'attr' => array('class' => 'defaultTextActive', 'title' => 'enter maximum value (optional)')));
                    break;
            }            
    }
}