<?php

namespace DHorchler\ConfigBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MakerLabs\Bundle\PagerBundle\Adapter\DoctrineOrmAdapter;
use JMS\SecurityExtraBundle\Annotation\Secure;

class DefaultController extends Controller
{
    protected $em;

   /**
     * @Route("/s", name="_settings")
     * @Template()
     */

    public function indexAction()
    {
        $this->em = $this->getDoctrine()->getEntityManager();
        $settings1 = $this->em->createQueryBuilder()->select('s.name, s.currentValue')->from('DHConfigBundle:Settings', 's')->getQuery()->getResult();
        foreach ($settings1 AS $setting) $settings[$setting['name']] = $setting['currentValue'];//print_r($settings);
        return array ('settings' =>$settings1);
    }
}
