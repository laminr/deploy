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
use Rsv\DeployBundle\Entity\Command;
use Rsv\DeployBundle\Manager\ProjectManager;
use Rsv\DeployBundle\Entity\Project;

use Psr\Log\LoggerInterface;

class CommandManager extends BaseManager {

    private $logger;

    protected $em;

    public function __construct(LoggerInterface $logger, ObjectManager $em) {
        $this->logger = $logger;
        $this->em = $em;
    }

    public function executeCommand(Command $command) {

        if ($command->getProject() == NULL) {
            return "";
        }

        $root = $command->getProject()->getPath();
        $folder = $command->getRootPath();
        $path = 'cd '.$root. '; cd '.$folder;

        $command = $path . '; ' . $command->getScript();

        $ssh = new SshBusiness($this->logger);
        $values = $ssh->execute($command->getProject(), $command);

        return sizeof($values) > 0 ? $values : "";

    }

    public function getCommand($commandId = 0) {

    }

    /**
     * Recupération d'une liste des branches ou tags
     * @param null $project
     * @param bool $requestTag
     * @return array
     */
    public function getBranchOrTagList( $project = NULL, $requestTag = false) {

        if ($project == NULL) {
            return "";
        }

        $path = $project->getPath();
        $who = $requestTag
            ? GitBusiness::CMD_TAG_ALL
            : GitBusiness::CMD_BRANCH_ALL;

        $command = 'cd '.$path."; ".$who;

        $ssh = new SshBusiness($this->logger);

        $values = array();
        $values = $ssh->execute($project, $command);

        $this->logger->info("getBranchOrTagList returns:".print_r($values, true));

        // retrait de l'asterisque
        foreach ($values as &$value) {
            if (strpos($value, "*") !== false) {
                $value = trim(str_replace("*", "", $value));
            }
        }

        return $values;
    }

    /**
     * @param null $project
     * @param bool $requestTag
     * @return string
     */
    public function getCurrentSourceDetails($project = NULL, $requestTag = false) {

        if ($project == NULL) {
            return "";
        }

        $path = $project->getPath();

        if ($path == "")
            return $path;

        $git = $requestTag
            ? GitBusiness::CMD_TAG_CURRENT
            : GitBusiness::CMD_BRANCH_CURRENT;

        $command = "cd $path; $git";

        $ssh = new SshBusiness($this->logger);
        $values = array();
        $values = $ssh->execute($project, $command);

        return sizeof($values) > 0 ? $values[0] : "";
    }

    /**
     * Modification de la source (branche/tag) d'un projet
     * @param null $project
     * @param string $target : le nom de la branche / tag
     * @return tableau message ssh de retour
     */
    public function doChangeSource($project = NULL, $target = "") {

        if ($project == NULL) {
            return "";
        }

        $path = $project->getPath();

        $command = 'cd '.$path."; ".GitBusiness::CMD_CHANGE_SOURCE.$target;

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
    public function doUpdateSource($project = NULL, $branch = "") {

        if ($project == NULL) {
            return "";
        }

        $path = $project->getPath();
        $command = 'cd '.$path."; ".GitBusiness::CMD_UPDATE_SOURCE.$branch;

        $ssh = new SshBusiness($this->logger);
        $values = $ssh->execute($project, $command);

        return sizeof($values) > 0 ? $values : "";
    }

    /**
     * Création d'un nouveau Tag d'un projet
     * @param null $project
     * @param string $newTag
     * @return tableau message ssh de retour
     */
    public function doCreateTag($project = NULL, $newTag = "") {

        if ($project == NULL) {
            return "";
        }

        $path = $project->getPath();

        /*
         * Mofidication des 2 labels :
         * - Nom du Tag
         * - Date de la création
         */
        $temp = str_replace("#name#", $newTag, GitBusiness::CMD_TAG_NEW);
        $gitCmd = str_replace("#date#", date("d-m-Y H:i:s"), $temp);

        // la commande Git
        $command = 'cd '.$path."; ".$gitCmd ;

        $ssh = new SshBusiness($this->logger);

        $values = array();
        $values = $ssh->execute($project, $command);

        return sizeof($values) > 0 ? $values : "";
    }

    /**
     * Demande une mise à jour de source sur un serveur
     * @param null $project
     * @return Array String : Messages retour du serveur
     */
    public function doFetchData($project = NULL) {

        if ($project == NULL) {
            return "";
        }

        $path = $project->getPath();

        // la commande Git
        $command = 'cd '.$path."; ".GitBusiness::CMD_FETCH_DATA ;
        $values = $this->ssh->execute($project, $command);

        return sizeof($values) > 0 ? $values : "";
    }

}