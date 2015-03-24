<?php

namespace Rsv\DeployBundle\Controller;

use Rsv\DeployBundle\Business\ProjectBusiness;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Rsv\DeployBundle\Manager\CommandManager;
use Rsv\DeployBundle\Manager\ProjectManager;
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

        $project = new ProjectManager($this->getDoctrine()->getEntityManager());
        $cmd = new CommandManager(AjaxController::$logger, $project);

        $data = $cmd->getCurrentSourceDetails($envId, false);
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
	 * 
	 * @param number $envId    
     * @Route("/tag" , name="_ajax_current_tag")
	 */
	public function currentTagAction($envId = "") {
		$values = array (
				"branch" => "?" 
		);
		
		// $envId = (is_integer($envId)) ? (int) $envId : 0;
		
		if ($envId != 0) {
			$values = $this->deployDao->getCurrentSourceFromEnvId ( $envId, true );
		}
		
		echo json_encode ( responseSuccess ( $values ) );
	}
	
	/**
	 * Création d'un nouveau Tag
	 * 
	 * @param string $envId
	 *        	: environnement et projet demandé
	 * @param string $newTag
	 *        	: nom du Tag
     * @Route("/tag/create" , name="_ajax_tag_create")
	 */
	public function createTagAction($envId = "", $newTag = "") {
		$response = "";
		
		log_message ( "info", "createTagJSON envId=" . $envId . " new Tag=" . $newTag );
		
		if ($envId == "" || $newTag == "") {
			$message = "Erreur de paramètre d'appel : envId = $envId ; Tag = $newTag";
			$response = responseError ( $message );
			log_message ( "createTagJSON Error ==> $message" );
		} else {
			$label = urldecode ( $newTag );
			$returns = $this->deployDao->doCreateTag ( $envId, $newTag );
			$response = responseSuccess ( $returns );
			log_message ( "info", "createTagJSON Success ==> " . print_r ( $response, true ) );
		}
		
		echo json_encode ( $response );
	}
	
	/**
	 * retour JSON de la liste des branches existante
	 * 
	 * @param unknown $projectName        	
	 */
	public function allTagNameAction($projectName) {
		$tags = $this->getGitNames ( $projectName, true );
		echo json_encode ( responseSuccess ( $tags ) );
	}
	
	/**
	 * Changement de branche/tag pour un projet et environnement
	 * 
	 * @param string $envId
	 *        	: clé environnement/projet
	 * @param string $branch
	 *        	: le nom de la branche/tag à mettre en place
	 */
	public function changeSourceAction($envId = "", $label = "") {
		$response = "";
		log_message ( "info", "changeSourceJSON envId=" . $envId . " label=" . $label );
		
		if ($envId == "" || $label == "") {
			$message = "Erreur de paramètre d'appel : envId = $envId ; branche/tag = $label";
			$response = responseError ( $message );
			log_message ( "info", "changeSourceJSON Error ==> $message" );
		} else {
			$label = urldecode ( $label );
			$returns = $this->deployDao->doChangeSource ( $envId, $label );
			$response = responseSuccess ( $returns );
			log_message ( "info", "changeSourceJSON Success ==> " . print_r ( $response, true ) );
		}
		
		echo json_encode ( $response );
	}
	
	/**
	 * Demande une mise à jour de source sur un serveur
	 * 
	 * @param string $envId        	
	 * @return Json String : Messages retour du serveur
	 */
	public function fetchDataAction($envId = "") {
		$response = "";
		log_message ( "info", "fetchDataJSON envId=" . $envId );
		
		if ($envId == "") {
			$message = "Erreur de paramètre d'appel : envId = $envId";
			$response = responseError ( $message );
			log_message ( "info", "changeSourceJSON Error ==> $message" );
		} else {
			$returns = $this->deployDao->doFetchData ( $envId );
			$response = responseSuccess ( $returns );
			log_message ( "info", "changeSourceJSON Success ==> " . print_r ( $response, true ) );
		}
		
		echo json_encode ( $response );
	}
	
	/**
	 * Effectue une mise à jour de la branche actuelle d'un projet
	 * 
	 * @param string $envId        	
	 * @param string $current        	
	 */
	public function updateSourceAction($envId, $current = "") {
		$response = "";
		log_message ( "info", "updateSourceJSON envId=" . $envId . " current=" . $current );
		
		if ($envId == "") {
			$message = "Erreur de paramètre d'appel : envId = $envId ; branche/tag = $current";
			$response = responseError ( $message );
			log_message ( "info", "updateSourceJSON Error ==> $message" );
		} else {
			$current = urldecode ( $current );
			$returns = $this->deployDao->doUpdateSource ( $envId, $current );
			$response = responseSuccess ( $returns );
			log_message ( "info", "updateSourceJSON Success ==> " . print_r ( $response, true ) );
		}
		
		echo json_encode ( $response );
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
    
}