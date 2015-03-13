<?php

namespace Rsv\DeployBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * JSON controller.
 *
 * @Route("/json")
 */
class JsonController extends Controller
{


	/**
	 * retour d'information de branche JSON pour un projet
	 * - nom du projet
	 * - environnement demandé
	 * - <strong>branche git actuelle</strong>
	 * 
	 * @param number $envId  
     * @Route("current/branch/{envId}" , name="json_currentBranch")
	 */
	public function currentBranchJSON($envId = "") {
		$values = array (
				"branch" => "?" 
		);
		
		if ($envId != 0) {
			$values = $this->deployDao->getCurrentSourceFromEnvId ( $envId, false );
		}
		
		echo json_encode ( responseSuccess ( $values ) );
	}
	
	/**
	 * retour d'information de Tag JSON pour un projet
	 * - nom du projet
	 * - environnement demandé
	 * - <strong>Tag git actuelle</strong>
	 * 
	 * @param number $envId    
     * @Route("current/tag/{envId}" , name="json_currentBranch")
	 */
	public function currentTagJSON($envId = "") {
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
     * @Route("create/tag/{envId}" , name="json_currentBranch")
	 */
	public function createTagJSON($envId = "", $newTag = "") {
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
	public function allBranchNameJSON($projectName) {
		$tags = $this->getGitNames ( $projectName, false );
		echo json_encode ( responseSuccess ( $tags ) );
	}
	
	/**
	 * retour JSON de la liste des branches existante
	 * 
	 * @param unknown $projectName        	
	 */
	public function allTagNameJSON($projectName) {
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
	public function changeSourceJSON($envId = "", $label = "") {
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
	public function fetchDataJSON($envId = "") {
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
	public function updateSourceJSON($envId, $current = "") {
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
	private function getGitNames($projectName, $requestTag) {
		$name = $projectName == "" ? $this->projects [0] [Deploy_ProjectBo::COL_NAME] : urldecode ( $projectName );
		
		return $this->deployDao->getBranchOrTagList ( $name, $requestTag );
	}
    
}