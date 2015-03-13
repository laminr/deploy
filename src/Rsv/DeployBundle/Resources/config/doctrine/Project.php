<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity
 */
class Project
{
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
     * @ORM\Column(name="user", type="string", length=40, nullable=false)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=20, nullable=false)
     */
    private $password;


}
