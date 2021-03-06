<?php

namespace Rsv\DeployBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Rsv\DeployBundle\Manager\BaseManager;
use Rsv\DeployBundle\Entity\Project;

class ProjectManager extends BaseManager
{
    protected $em;

    public function __construct(ObjectManager $em)
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

    public function getProject($id = 0) {
        return $this->getRepository()->getProject($id);
    }

    public function getRepoFrom($name = "") {
        return  $this->getRepository()->getRepoFrom();
    }

    public function getEnvIds($envRepoId = 0) {
        return  $this->getRepository()->getEnvIds($envRepoId);
    }

    public function updateProject(Project $project)
    {
        $current = $this->getProject($project->getId());

        $current->setName($project->getName());
        $current->setEnvironment($project->getEnvironment());
        $current->setDomain($project->getDomain());
        $current->setPath($project->getPath());
        $current->setUser($project->getUser());
        $current->setPassword($project->getPassword());
        $current->setTag($project->getTag());

        $this->em->flush();
    }

    public function updateProjectTag(Project $project)
    {
        $current = $this->getProject($project->getId());
        $current->setTag($project->getTag());

        $this->em->flush();
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