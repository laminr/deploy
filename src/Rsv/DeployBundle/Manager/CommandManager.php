<?php
/**
 * Created by PhpStorm.
 * User: thibault
 * Date: 18/03/15
 * Time: 08:51
 */

namespace Rsv\DeployBundle\Manager;

use Rsv\DeployBundle\Business\GitBusiness;
use Rsv\DeployBundle\Business\SshBusiness;
use Rsv\DeployBundle\Manager\ProjectManager;
use Rsv\DeployBundle\Entity\Project;

use Psr\Log\LoggerInterface;

class CommandManager {

    private $logger;
    private $projects;

    public function __construct(LoggerInterface $logger, ProjectManager $projects) {
        $this->logger = $logger;
        $this->projects = $projects;
    }

    public function getBranchOrTagList( $projectId = 0, $requestTag = false) {

        $values = array();

        if ($projectId != 0) {

            $projects = $this->projects->getProject($projectId);

            $path = $projects->getPath();
            $who = $requestTag
                ? GitBusiness::CMD_TAG_ALL
                : GitBusiness::CMD_BRANCH_ALL;

            $command = 'cd '.$path."; ".$who;

            $ssh = new SshBusiness($this->logger);
            $values = $ssh->execute($projects, $command);

            $this->logger->info("getBranchOrTagList returns:".print_r($values, true));

            // retrait de l'asterisque
            foreach ($values as &$value) {
                if (strpos($value, "*") !== false) {
                    $value = trim(str_replace("*", "", $value));
                }
            }
        }

        return $values;
    }


    /**
     * @param $oneProject
     * @param bool $requestTag
     * @return string
     */
    public function getCurrentSourceDetails($projectId = 0, $requestTag = false) {

        $values = array();

        $projects = $this->projects->getProject($projectId);

        $path = $projects->getPath();
        $git = $requestTag
            ? GitBusiness::CMD_TAG_CURRENT
            : GitBusiness::CMD_BRANCH_CURRENT;

        $command = "cd $path; $git";

        $ssh = new SshBusiness($this->logger);
        $values = $ssh->execute($projects, $command);

        return sizeof($values) > 0 ? $values[0] : "";
    }

    /**
     * Recupération des environements pour un projet
     * @param nom du projet
     * @return array
     */
    public function getProjectEnv($projectName = "") {
        $values = array();

        if ($projectName != "") {
            $values = $this->projectDao->getByProjectName($projectName);
        }

        return $values;
    }



    /**
     * retourne un tableau d'information d'un project
     * - nom du projet
     * - environnement demandé
     * - branche git actuelle
     * @param unknown $envId
     * @return Ambigous <string, unknown>
     */
    /*
    public function getCurrentSourceFromEnvId($envId, $requestTag = false) {

        $data = $this->projectDao->read((int) $envId);

        log_message("info", "getBranchOrTagList returns:".print_r($data, true));

        $branch = $data->getPath() == ""
            ? lang("deploy")["notdeployed"]
            : $this->getCurrentSourceDetails($data, $requestTag);

        $values = array(
            "project" 		=> $data->getName(),
            "environment" 	=> $data->getEnvironment(),
            "branch" 		=> $branch
        );

        return $values;
    }
    */

    /**
     * Modification de la source (branche/tag) d'un projet
     * @param string $envId : id de l'environnement et projet
     * @param string $label : le nom de la branche / tag
     * @return tableau message ssh de retour
     */
    public function doChangeSource($envId = "", $label = "") {

        $values = array();
        $project = $this->projects->getProject($envId);

        $path = $project->getPath();

        $command = 'cd '.$path."; ".GitBusiness::CMD_CHANGE_SOURCE.$label;

        $ssh = new SshBusiness($this->logger);
        $values = $ssh->execute($project, $command);

        return sizeof($values) > 0 ? $values : "";
    }

    /**
     * Mise à jour de la source d'un projet
     * @param string $envId : id de l'environnement et projet
     * @param string $branch : le nom de la branche
     * @return tableau message ssh de retour
     */
    public function doUpdateSource($envId = "", $branch = "") {

        $project = $this->projects->getProject($envId);

        $path = $project->getPath();
        $command = 'cd '.$path."; ".GitBusiness::CMD_UPDATE_SOURCE.$branch;

        $ssh = new SshBusiness($this->logger);
        $values = $ssh->execute($project, $command);

        return sizeof($values) > 0 ? $values : "";
    }

    /**
     * Création d'un nouveau Tag d'un projet
     * @param string $envId : id de l'environnement et projet
     * @param string $label : le nom du tag
     * @return tableau message ssh de retour
     */
    public function doCreateTag($envId = "", $newTag = "") {

        $values = array();
        $project = $this->projectDao->read((int) $envId);

        $path = $project->getPath();

        /*
         * Mofidication des 2 labels :
         * - Nom du Tag
         * - Date de la création
         */
        $temp = str_replace("#name#", $newTag, Deploy_GitBo::CMD_TAG_NEW);
        $gitCmd = str_replace("#date#", date("d-m-Y H:i:s"), $temp);

        // la commande Git
        $command = 'cd '.$path."; ".$gitCmd ;

        $values = $this->ssh->execute($project, $command);
        log_message("info", "doCreateTag returns:".print_r($values, true));

        return sizeof($values) > 0 ? $values : "";
    }

    /**
     * Demande une mise à jour de source sur un serveur
     * @param number $envId
     * @return Array String : Messages retour du serveur
     */
    public function doFetchData($envId = 0) {

        $project = $this->projects->getProject($envId);
        $path = $project->getPath();

        // la commande Git
        $command = 'cd '.$path."; ".GitBusiness::CMD_FETCH_DATA ;
        $values = $this->ssh->execute($project, $command);

        return sizeof($values) > 0 ? $values : "";
    }

}