<?php

namespace Rsv\DeployBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method("GET")
     * @Template("RsvDeployBundle:Default:index.html.twig")
     */
    public function indexAction()
    {
        $projectNames = $this->get("project.service")->getProjectName();
        $data = array(
            "projects" => $projectNames,
            "current" => "Current Projet"
        );
        
        return array("data" => $data);
    }
}
