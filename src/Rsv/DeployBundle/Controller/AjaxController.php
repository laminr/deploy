<?php

namespace Rsv\DeployBundle\Controller;

use Rsv\DeployBundle\Business\ProjectBusiness;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Rsv\DeployBundle\Manager\CommandManager;
use Rsv\DeployBundle\Manager\ProjectManager;

use Doctrine\Common\Persistence\ObjectManager;
use Rsv\DeployBundle\Entity\Project;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * JSON controller.
 *
 * @Route("/ajax")
 */
class AjaxController extends Controller
{

    private static $logger;

    /**
     * retour JSON de la liste des branches existante
     *
     * @param String $projectName
     * @param number $envId
     * @return json
     *
     * @Route("/branches/{projectId}" , name="_ajax_all_branches")
     * @Method("GET")
     * @Template()
     */
    public function allBranchNameAction($projectId = 0) {

        if (AjaxController::$logger == null)
            AjaxController::$logger = $this->get('logger');

        $project = new ProjectManager($this->getDoctrine()->getEntityManager());
        $cmd = new CommandManager(AjaxController::$logger, $project);

        $data = $cmd->getBranchOrTagList($projectId, false);

        $response = new JsonResponse();
        return $response->setData( $data);
    }

    /**
     * retour JSON de la liste des branches existante
     *
     * @param String $projectName
     * @param number $envId
     * @return json
     *
     * @Route("/tags/{projectId}" , name="_ajax_all_tags")
     * @Method("GET")
     * @Template()
     */
    public function allTagsAction($projectId = 0) {

        if (AjaxController::$logger == null)
            AjaxController::$logger = $this->get('logger');

        $project = new ProjectManager($this->getDoctrine()->getEntityManager());
        $cmd = new CommandManager(AjaxController::$logger, $project);

        $data = $cmd->getBranchOrTagList($projectId, true);

        $response = new JsonResponse();
        return $response->setData( $data);
    }

	/**
	 * retour d'information de branche JSON pour un projet
	 * - nom du projet
	 * - environnement demandé
	 * - <strong>branche git actuelle</strong>
	 * 
	 * @param number $envId
     * @return branche actuelle
     * @Route("/current/branch/{envId}" , name="_ajax_current_branch")
     * @Method("GET")
     * @Template()
	 */
	public function currentBranchAction($envId = 0) {

        if (AjaxController::$logger == null)
            AjaxController::$logger = $this->get('logger');

        $project = new ProjectManager($this->getDoctrine()->getManager());
        $cmd = new CommandManager(AjaxController::$logger, $project);

        $data = $cmd->getCurrentSourceDetails($envId, false);

        if ($data == "") $data = $this->get('translator')->trans('rsv3.deploy.notdeployed');
        $data = str_replace("\n", "", $data);

        $response = new JsonResponse();
        return $response->setData( array("branch" => $data));

	}

    /**
     * retour JSON de la liste des branches existante
     *
     * @param String $projectName
     * @param number $envId
     * @return json
     *
     * @Route("/env/{projectId}" , name="_ajax_get_id")
     * @Method("GET")
     * @Template()
     */
    public function getEnvIdsAction($projectId = 0) {

        if (AjaxController::$logger == null)
            AjaxController::$logger = $this->get('logger');

        $data = $this->get("project.service")->getEnvIds($projectId);

        $projects = array();
        foreach ($data as $project) {
            array_push($projects, ProjectBusiness::toShortArray($project));
        }

        $response = new JsonResponse();
        return $response->setData( $projects);
    }
	
	/**
	 * retour d'information de Tag JSON pour un projet
	 * - nom du projet
	 * - environnement demandé
	 * - <strong>Tag git actuelle</strong>
	 * @return current tag
	 * @param number $envId    
     * @Route("/tag" , name="_ajax_current_tag")
	 */
	public function currentTagAction($envId = "") {

        if (AjaxController::$logger == null)
            AjaxController::$logger = $this->get('logger');

        $project = new ProjectManager($this->getDoctrine()->getManager());
        $cmd = new CommandManager(AjaxController::$logger, $project);

        $data = $cmd->getCurrentSourceDetails($envId, true);
        $data = str_replace("\n", "", $data);

        $response = new JsonResponse();
        return $response->setData( array("tag" => $data));
	}
	
	/**
	 * Création d'un nouveau Tag
	 * 
	 * @param string $envId
	 *        	: environnement et projet demandé
	 * @param string $newTag
	 *        	: nom du Tag
     * @Route("/tag/create/{envId}/{tag}" , name="_ajax_tag_create")
	 */
	public function createTagAction($envId = 0, $tag = "") {

        if (AjaxController::$logger == null)
            AjaxController::$logger = $this->get('logger');

        $project = new ProjectManager($this->getDoctrine()->getManager());
        $cmd = new CommandManager(AjaxController::$logger, $project);

        $data = $cmd->getCurrentSourceDetails($envId, true);
        $data = str_replace("\n", "", $data);

        $response = new JsonResponse();
        return $response->setData( array("tag" => $data));

	}
	
	/**
	 * retour JSON de la liste des tag existante
	 * 
	 * @param unknown $projectName        	
	 */
	public function allTagNameAction($projectName) {
		$tags = $this->getGitNames ( $projectName, true );
		echo json_encode ( responseSuccess ( $tags ) );
	}


    /**
     * Récupération de la liste des branches ou Tags pour un projet
     *
     * @param string $projectName
     *        	: nom du projet
     * @param boolean $requestTag
     *        	: demande les Tags (sinon branches)
     * @return tableau de string
     */
    private function getGitNamesAction($projectName, $requestTag) {
        $name = $projectName == "" ? $this->projects [0] [Deploy_ProjectBo::COL_NAME] : urldecode ( $projectName );

        return $this->deployDao->getBranchOrTagList ( $name, $requestTag );
    }
	
	/**
	 * Changement de branche/tag pour un projet et environnement
	 * 
	 * @param string $envId
	 *        	: clé environnement/projet
	 * @param string $branch
	 *        	: le nom de la branche/tag à mettre en place
     * @return msg
     * @Route("/branch/change/{envId}/{target}" , name="_ajax_change_source")
	 */
	public function changeSourceAction($envId = 0, $target = "") {

        if (AjaxController::$logger == null)
            AjaxController::$logger = $this->get('logger');

        $data = array();
        if ($target != "") {
            $project = new ProjectManager($this->getDoctrine()->getManager());
            $cmd = new CommandManager(AjaxController::$logger, $project);
            $data = $cmd->doChangeSource($envId, $target);
        }

        $response = new JsonResponse();
        return $response->setData( $data );
	}
	
	/**
	 * Demande une mise à jour de source sur un serveur
	 * 
	 * @param string $envId        	
	 * @return Json String : Messages retour du serveur
     *
     * @Route("/data/fetch/{envId}" , name="_ajax_fetch_data")
	 */
	public function fetchDataAction($envId = 0) {

        if (AjaxController::$logger == null)
            AjaxController::$logger = $this->get('logger');

        $project = new ProjectManager($this->getDoctrine()->getManager());
        $cmd = new CommandManager(AjaxController::$logger, $project);

        $data = $cmd->doFetchData($envId);

        $response = new JsonResponse();
        return $response->setData( $data );
	}
	
	/**
	 * Effectue une mise à jour de la branche actuelle d'un projet
	 * 
	 * @param string $envId        	
	 * @param string $current
     * @return message
     s* @Route("/data/update/{envId}/{current}" , name="_ajax_update_data")
	 */
	public function updateSourceAction($envId = 0, $current = 0) {
        if (AjaxController::$logger == null)
            AjaxController::$logger = $this->get('logger');

        $project = new ProjectManager($this->getDoctrine()->getManager());
        $cmd = new CommandManager(AjaxController::$logger, $project);

        $data = $cmd->doUpdateSource($envId, $current);

        foreach($data as &$d) {
            $d = str_replace("\n", "", $d);
        }

        $response = new JsonResponse();
        return $response->setData( $data );
	}

    /**
     * Effectue une mise à jour de la branche actuelle d'un projet
     *
     * @param string $envId
     * @param string $current
     * @return message
    s* @Route("/tag/last/{envId}" , name="_ajax_tag_last")
     */
    public function getLastTagAction($envId = 0) {

        if (AjaxController::$logger == null)
            AjaxController::$logger = $this->get('logger');

        $project = $this->get("project.service")->getProject($envId);
        $tag = explode("|", $project->getTag());

        $data = array(
            "g" => (int)$tag[0],
            "r" => (int)$tag[1],
            "c" => (int)$tag[2]
        );

        $response = new JsonResponse();
        return $response->setData( $data );
    }
    
}