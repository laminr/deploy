<?php

namespace Rsv\DeployBundle\Manager;

use Doctrine\ORM\EntityManager;
use Rsv\DeployBundle\Manager\BaseManager;
use Rsv\DeployBundle\Entity\Project;

class ProjectManager extends BaseManager
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function findAll() {
        return $this->getRepository()
                ->findAll();
    }    
    
    public function loadProject($deskId) {
        return $this->getRepository()
                ->findOneBy(array('id' => $deskId));
    }

    public function getProjectName() {
        return  $this->getRepository()->getProjectName();
    }
    
    /**
    * Save Project entity
    *
    * @param Project $project 
    */
    public function saveProject(Project $project)
    {
        $this->persistAndFlush($project);
    }

    public function getRepository()
    {
        return $this->em->getRepository(Project::CLASS_NAME);
    }

}