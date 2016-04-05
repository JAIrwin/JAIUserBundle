<?php

namespace JAI\Bundle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Role
 *
 * @ORM\Table(name="JAI_UserBundle_role")
 * @ORM\Entity(repositoryClass="JAI\Bundle\UserBundle\Repository\RoleRepository")
 */
class Role
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=255, unique=true)
     */
    private $role;
    
	/**
	 * @ORM\ManyToMany(targetEntity="JAI\Bundle\UserBundle\Entity\User",mappedBy="roles")
	 */
    private $users;
    
	public function __construct()
	{
	    $this->users = new ArrayCollection();
	}

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
     * Set role
     *
     * @param string $role
     *
     * @return Role
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Add user
     *
     * @param \JAI\Bundle\UserBundle\Entity\User $user
     *
     * @return Role
     */
    public function addUser(\JAI\Bundle\UserBundle\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \JAI\Bundle\UserBundle\Entity\User $user
     */
    public function removeUser(\JAI\Bundle\UserBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }
}
