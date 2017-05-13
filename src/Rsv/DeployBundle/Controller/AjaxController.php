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
     * @param int $projectId
     * @return json
     * @throws \Exception
     * @internal param String $projectName
     * @internal param number $envId
     *
     * @Route("/branches/{projectId}" , name="_ajax_all_branches")
     * @Method("GET")
     * @Template()
     */
    public function allBranchNameAction($projectId = 0) {

        if (AjaxController::$logger == null)
            AjaxController::$logger = $this->get('logger');

        $project = $this->get("project.service")->getProject($projectId);
        $cmd = new CommandManager(AjaxController::$logger);

        $data = $cmd->getBranchOrTagList($project, false);

        $response = new JsonResponse();
        return $response->setData( $data);
    }

    /**
     * retour JSON de la liste des branches existante
     *
     * @param int $commandId
     * @return json
     * @throws \Exception
     * @internal param int $projectId
     * @internal param String $projectName
     * @internal param number $envId
     *
     * @Route("/command/{commandId}" , name="_ajax_command")
     * @Method("GET")
     * @Template()
     */
    public function executeCommandAction($commandId = 0) {

        if (AjaxController::$logger == null)
            AjaxController::$logger = $this->get('logger');

        $project = $this->get("project.service")->getProject($projectId);
        $cmd = new CommandManager(AjaxController::$logger);

        $data = $cmd->getBranchOrTagList($project, false);

        $response = new JsonResponse();
        return $response->setData( $data);
    }

    /**
     * retour JSON de la liste des branches existante
     *
     * @param int $projectId
     * @return json
     * @throws \Exception
     * @internal param String $projectName
     * @internal param number $envId
     *
     * @Route("/commands/{projectId}" , name="_ajax_command_all")
     * @Method("GET")
     * @Template()
     */
    public function allCommandsAction($projectId = 0) {

        if (AjaxController::$logger == null)
            AjaxController::$logger = $this->get('logger');

        $project = $this->get("project.service")->getProject($projectId);
        $response = new JsonResponse();
        return $response->setData($project);
    }

    /**
     * retour JSON de la liste des branches existante
     *
     * @param int $projectId
     * @return json
     * @throws \Exception
     * @internal param String $projectName
     * @internal param number $envId
     *
     * @Route("/tags/{projectId}" , name="_ajax_tag_all")
     * @Method("GET")
     * @Template()
     */
    public function allTagsAction($projectId = 0) {

        if (AjaxController::$logger == null)
            AjaxController::$logger = $this->get('logger');

        $cmd = new CommandManager(AjaxController::$logger);

        $project = $this->get("project.service")->getProject($projectId);
        $data = $cmd->getBranchOrTagList($project, true);

        $response = new JsonResponse();
        return $response->setData( $data);
    }

    /**
     * retour d'information de branche JSON pour un projet
     * - nom du projet
     * - environnement demandé
     * - <strong>branche git actuelle</strong>
     *
     * @param int|number $envId
     * @return branche actuelle
     * @throws \Exception
     *
     * @Route("/current/branch/{envId}" , name="_ajax_current_branch")
     * @Method("GET")
     * @Template()
     */
	public function currentBranchAction($envId = 0) {

        if (AjaxController::$logger == null)
            AjaxController::$logger = $this->get('logger');

        $project = $this->get("project.service")->getProject($envId);

        $cmd = new CommandManager(AjaxController::$logger);
        $data = $cmd->getCurrentSourceDetails($project, false);

        if ($data == "") $data = $this->get('translator')->trans('rsv3.deploy.notdeployed');
        $data = str_replace("\n", "", $data);

        $response = new JsonResponse();
        return $response->setData( array("branch" => $data));

	}

    /**
     * @param string $envId
     * @return JsonResponse
     * @throws \Exception
     * @Route("/current/tag/{envId}" , name="_ajax_current_tag")
     */
    public function currentTagAction($envId = "") {

        if (AjaxController::$logger == null)
            AjaxController::$logger = $this->get('logger');

        $project = $this->get("project.service")->getProject($envId);

        $cmd = new CommandManager(AjaxController::$logger);
        $data = $cmd->getCurrentSourceDetails($project, true);
        $data = str_replace("\n", "", $data);

        $response = new JsonResponse();
        return $response->setData( array("tag" => $data));
    }

    /**
     * @param int $projectId
     * @return JsonResponse
     * @throws \Exception
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
     * @param int $envId
     * @param string $g
     * @param string $r
     * @param string $c
     * @return JsonResponse
     * @throws \Exception
     *
     * @Route("/tag/create/{envId}/{g}/{r}/{c}" , name="_ajax_tag_create")
     */
	public function createTagAction($envId = 0, $g = "", $r = "", $c = "") {

        if (AjaxController::$logger == null)
            AjaxController::$logger = $this->get('logger');

        $cmd = new CommandManager(AjaxController::$logger);

        $project = $this->get("project.service")->getProject($envId);

        $data = $cmd->doCreateTag($project, "TAG-G".$g."R".$r."C".$c);
        $data = str_replace("\n", "", $data);

        if ($data != "") {
            $project->setTag($g."|".$r."|".$c);
            $this->get("project.service")->updateProjectTag($project);
        }
        $response = new JsonResponse();
        return $response->setData( array("tag" => $data));

	}

    /**
     * Changement de branche/tag pour un projet et environnement
     *
     * @param int|string $envId
     *            : clé environnement/projet
     * @param string $target
     * @return msg
     * @throws \Exception
     * @internal param string $branch : le nom de la branche/tag à mettre en place
     *
     * @Route("/source/change/{envId}/{target}" , name="_ajax_change_source")
     */
	public function changeSourceAction($envId = 0, $target = "") {

        if (AjaxController::$logger == null)
            AjaxController::$logger = $this->get('logger');

        $data = array();
        if ($target != "") {
            $cmd = new CommandManager(AjaxController::$logger);

            $project = $this->get("project.service")->getProject($envId);
            $data = $cmd->doChangeSource($project, $target);
        }

        $response = new JsonResponse();
        return $response->setData( $data );
	}

    /**
     * Demande une mise à jour de source sur un serveur
     *
     * @param int|string $envId
     * @return Json String : Messages retour du serveur
     * @throws \Exception
     *
     * @Route("/data/fetch/{envId}" , name="_ajax_fetch_data")
     */
	public function fetchDataAction($envId = 0) {

        if (AjaxController::$logger == null)
            AjaxController::$logger = $this->get('logger');

        $cmd = new CommandManager(AjaxController::$logger);

        $project = $this->get("project.service")->getProject($envId);
        $data = $cmd->doFetchData($project);

        $response = new JsonResponse();
        return $response->setData( $data );
	}

    /**
     * Effectue une mise à jour de la branche actuelle d'un projet
     *
     * @param int|string $envId
     * @param int|string $current
     * @return message
     * @throws \Exception
     *
     * @Route("/data/update/{envId}/{current}" , name="_ajax_update_data")
     */
	public function updateSourceAction($envId = 0, $current = 0) {
        if (AjaxController::$logger == null)
            AjaxController::$logger = $this->get('logger');

        $cmd = new CommandManager(AjaxController::$logger);

        $project = $this->get("project.service")->getProject($envId);
        $data = $cmd->doUpdateSource($project, $current);

        foreach($data as &$d) {
            $d = str_replace("\n", "", $d);
        }

        $response = new JsonResponse();
        return $response->setData( $data );
	}

    /**
     * Effectue une mise à jour de la branche actuelle d'un projet
     *
     * @param int|string $envId
     * @return message
     * @throws \Exception
     * @internal param string $current
     *
     * @Route("/tag/last/{envId}" , name="_ajax_tag_last")
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