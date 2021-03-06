<?php

namespace Rsv\DeployBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity(repositoryClass="Rsv\DeployBundle\Repository\ProjectRepository")
 */
class Project
{
    const CLASS_NAME = "RsvDeployBundle:Project";

    const ENV_REPO 		= "REPO";
    const ENV_QUALIF 	= "QUALIF";
    const ENV_PREPROD	= "PREPROD";
    const ENV_PROD 		= "PROD";

    const COL_ID 		= "id";
    const COL_NAME		= "name";
    const COL_ENV 		= "environment";
    const COL_DOMAIN 	= "domain";
    const COL_PATH 		= "path";
    const COL_USER 		= "user";
    const COL_PASSWD	= "password";
    
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
     * @ORM\Column(name="environment", type="string", length=10, nullable=false)
     */
    private $environment;

    /**
     * @var string
     *
     * @ORM\Column(name="domain", type="string", length=40, nullable=false)
     */
    private $domain;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=50, nullable=false)
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="port", type="integer", nullable=false)
     */
    private $port = 22;

    /**
     * @var string
     *
     * @ORM\Column(name="user", type="string", length=40, nullable=false)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=20, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string", length=6, nullable=false)
     */
    private $tag;


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
     * Set name
     *
     * @param string $name
     * @return Project
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

    /**
     * Set environment
     *
     * @param string $environment
     * @return Project
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * Get environment
     *
     * @return string 
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Set domain
     *
     * @param string $domain
     * @return Project
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get domain
     *
     * @return string 
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Project
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set port
     *
     * @param integer $port
     * @return Project
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Get port
     *
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set user
     *
     * @param string $user
     * @return Project
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Project
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set Last Tag
     *
     * @param string $tag
     * @return Project
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get $tag
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }
}
