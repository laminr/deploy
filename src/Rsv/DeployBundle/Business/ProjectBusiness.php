<?php
/**
 * Created by PhpStorm.
 * User: thibault
 * Date: 24/03/15
 * Time: 14:02
 */

namespace Rsv\DeployBundle\Business;

use Rsv\DeployBundle\Entity\Project;

class ProjectBusiness {

    public static function toArray(Project $project) {
        return array(
            "id"        => $project->getId(),
            "name"      => $project->getName(),
            "env"       => $project->getEnvironment(),
            "domain"    => $project->getDomain(),
            "path"      => $project->getPath(),
            "user"      => $project->getUser(),
            "password"  => $project->getPassword(),
        );
    }

    public static function toShortArray(Project $project) {
        return array(
            "id"        => $project->getId(),
            "name"      => $project->getName(),
            "env"       => $project->getEnvironment()
        );
    }
}