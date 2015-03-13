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
        
	    //$name = urldecode ( $projectName );
		/*
		// projet courant
		$current = $name == "" ? $this->projectNames [0] [Deploy_ProjectBo::COL_NAME] : $name;
		
		$envs = $this->deployDao->getProjectEnv ( $current );
		$envIds = array ();
		
		foreach ( $envs as $env ) {
			array_push ( $envIds, array (
					"name" => $env->getEnvironment (),
					"value" => $env->getId () 
			) );
		}
		
		$projects = array ();
		foreach ( $this->projectNames as $value ) {
			$name = $value ["name"];
			array_push ( $projects, array (
					"name" => $name,
					"url" => site_url ( "deploy/deployCtrl/project/$name" ) 
			) );
		}
		
		$urls = array (
				array ("name" => "branch", "value" => site_url ( "deploy/deployCtrl/currentBranchJSON" )),
				array ("name" => "tag", "value" => site_url ( "deploy/deployCtrl/currentTagJSON" )),
				array ("name" => "allB", "value" => site_url ( "deploy/deployCtrl/allBranchNameJSON" )),
				array ("name" => "allT", "value" => site_url ( "deploy/deployCtrl/allTagNameJSON" )),
				array ("name" => "changing", "value" => site_url ( "deploy/deployCtrl/changeSourceJSON" )),
				array ("name" => "create", "value" => site_url ( "deploy/deployCtrl/createTagJSON" )),
				array ("name" => "update", "value" => site_url ( "deploy/deployCtrl/updateSourceJSON" )),
				array ("name" => "fetch", "value" => site_url ( "deploy/deployCtrl/fetchDataJSON" )) 
		);
		
		// label de la page
		$lang = lang ( "deploy" );
		$labels = array (
				array (
						"name" => "loading",
						"value" => $lang ["loading"] 
				),
				array (
						"name" => "updating",
						"value" => $lang ["updating"] 
				) 
		);
		
		$_data = array (
				"projects" => $projects,
				"current" => $current,
				// info environement
				"envs" => $envIds,
				// url ajax
				"urls" => $urls,
				// label IHM
				"deployment" => $lang ["deployment"],
				"qualif" 	=> $lang ["qualif"],
				"preprod" 	=> $lang ["preprod"],
				"prod" 		=> $lang ["prod"],
				"branchs" 	=> $lang ["branchs"],
				"changeB" 	=> $lang ["changeBranch"],
				"changeT" 	=> $lang ["changeTag"],
				"activTag" 	=> $lang ["activTag"],
				"newTag" 	=> $lang ["newTag"],
				"activate" 	=> $lang ["activate"],
				"validate" 	=> $lang ["validate"],
				"update" 	=> $lang ["update"],
				"currentB" 	=> $lang ["currentBranch"],
				"currentT" 	=> $lang ["currentTag"],
				"show" 		=> $lang ["show"],
				// label JS
				"labels" => $labels 
		);
        
        */
        $data = array(
            "projects" => $projectNames,
            "current" => "Current Projet"
        );
        
        return array("data" => $data);
    }
}
