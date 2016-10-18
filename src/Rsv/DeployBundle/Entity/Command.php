<?php

namespace Rsv\DeployBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Project
 *
 * @ORM\Table(name="command")
 * @ORM\Entity(repositoryClass="Rsv\DeployBundle\Repository\CommandRepository")
 */
class Command
{
    const CLASS_NAME = "RsvDeployBundle:Command";

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=20, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="root_path", type="string", length=100, nullable=false)
     */
    private $rootPath;

    /**
     * @var string
     *
     * @ORM\Column(name="script", type="string", length=200, nullable=false)
     */
    private $script;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="commands")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set rootPath
     *
     * @param string $rootPath
     * @return Command
     */
    public function setRootPath($rootPath)
    {
        $this->rootPath = $rootPath;

        return $this;
    }

    /**
     * Get rootPath
     *
     * @return string 
     */
    public function getRootPath()
    {
        return $this->rootPath;
    }

    /**
     * Set script
     *
     * @param string $script
     * @return Command
     */
    public function setScript($script)
    {
        $this->script = $script;

        return $this;
    }

    /**
     * Get script
     *
     * @return string 
     */
    public function getScript()
    {
        return $this->script;
    }

    /**
     * Set project
     *
     * @param \Rsv\DeployBundle\Entity\Project $project
     * @return Command
     */
    public function setProject(\Rsv\DeployBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \Rsv\DeployBundle\Entity\Project 
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Command
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
}
