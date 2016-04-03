<?php
// src/JAI/UserBundle/Entity/User.php
namespace JAI\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;


/**
 * @ORM\Table(name="app_users")
 * @ORM\Entity(repositoryClass="JAI\UserBundle\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="Email already taken",groups={"registration","profile"})
 * @UniqueEntity(fields="username", message="Username already taken",groups={"registration","profile"})
 */
class User implements AdvancedUserInterface, \Serializable
{
	/**
	 * @SecurityAssert\UserPassword(
	 *     message = "Wrong value for your current password",
	 *	   groups={"profile"}
	 * )
	 */
	protected $oldPassword;

	public function getOldPassword() {
		return $this->oldPassword;
	}

	public function setOldPassword($oldPassword) {
		$this->oldPassword= $oldPassword;
	}

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=60, unique=true)
	 * @Assert\NotBlank(groups={"registration","profile","forgot","admin"})
	 * @Assert\Email(groups={"registration","admin"})
	 */
	private $email;

	/**
	 * @ORM\Column(type="string", length=25, unique=true)
	 * @Assert\NotBlank(groups={"login","registration","profile","admin"})
	 */
	private $username;

	/**
	 * @Assert\NotBlank(groups={"registration","resetpw"})
	 * @Assert\Length(max=4096)
	 */
	private $plainPassword;

	/**
	 * @ORM\Column(type="string", length=64)
	 * @Assert\NotBlank(groups={"login","profile"})
	 */
	private $password;

	/**
	 * @ORM\Column(name="is_active", type="boolean")
	 */
	private $isActive;

	/**
	 * @ORM\Column(name="reset_token", type="string", length=32)
	 */
	private $resetToken;

	/**
	 * @ORM\Column(name="reset_expires", type="integer")
	 */
	private $resetExpires;

	/**
	 * @Recaptcha\IsTrue
	 */
	public $recaptcha;

	/**
	 * @ORM\ManyToMany(targetEntity="JAI\UserBundle\Entity\Role", inversedBy="users")
	 */
	private $roles;

	/**
	 * Get roles
	 *
	 * @return Role[] The user roles
	 */
	public function getRoles()
	{
		$fetchedroles = $this->roles;
		$userroles = [];
		$count = 0;
		foreach ($fetchedroles as $role) {
			$userroles[$count] = $role->getRole();
			$count += 1;
		}
		return $userroles;

		//         return $this->roles;
	}

	public function getPlainPassword()
	{
		return $this->plainPassword;
	}

	public function setPlainPassword($plainPassword)
	{
		$this->plainPassword = $plainPassword;
	}

	public function __construct()
	{
		$this->isActive = false;
		$this->roles = new ArrayCollection();
		// may not be needed, see section on salt below
		// $this->salt = md5(uniqid(null, true));
	}

	public function getUsername()
	{
		return $this->username;
	}

	public function getSalt()
	{
		// you *may* need a real salt depending on your encoder
		// see section on salt below
		return null;
	}

	public function getPassword()
	{
		return $this->password;
	}

	public function eraseCredentials()
	{
	}

	/** @see \Serializable::serialize() */
	public function serialize()
	{
		return serialize(array(
				$this->id,
				$this->username,
				$this->password,
				$this->isActive,
				// see section on salt below
				// $this->salt,
			));
	}

	/** @see \Serializable::unserialize() */
	public function unserialize($serialized)
	{
		list (
			$this->id,
			$this->username,
			$this->password,
			$this->isActive,
			// see section on salt below
			// $this->salt
		) = unserialize($serialized);
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
	 * Set username
	 *
	 * @param string $username
	 *
	 * @return User
	 */
	public function setUsername($username)
	{
		$this->username = $username;

		return $this;
	}

	/**
	 * Set password
	 *
	 * @param string $password
	 *
	 * @return User
	 */
	public function setPassword($password)
	{
		$this->password = $password;

		return $this;
	}

	/**
	 * Set email
	 *
	 * @param string $email
	 *
	 * @return User
	 */
	public function setEmail($email)
	{
		$this->email = $email;

		return $this;
	}

	/**
	 * Get email
	 *
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * Set isActive
	 *
	 * @param boolean $isActive
	 *
	 * @return User
	 */
	public function setIsActive($isActive)
	{
		$this->isActive = $isActive;

		return $this;
	}

	/**
	 * Get isActive
	 *
	 * @return boolean
	 */
	public function getIsActive()
	{
		return $this->isActive;
	}

	/**
	 * Add role
	 *
	 * @param \JAI\UserBundle\Entity\Role $role
	 *
	 * @return User
	 */
	public function addRole(\JAI\UserBundle\Entity\Role $role)
	{
		$this->roles[] = $role;

		return $this;
	}

	/**
	 * Remove role
	 *
	 * @param \JAI\UserBundle\Entity\Role $role
	 */
	public function removeRole(\JAI\UserBundle\Entity\Role $role)
	{
		$this->roles->removeElement($role);
	}

    /**
     * Set resetToken
     *
     * @param string $resetToken
     *
     * @return User
     */
    public function setResetToken($resetToken)
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    /**
     * Get resetToken
     *
     * @return string
     */
    public function getResetToken()
    {
        return $this->resetToken;
    }

    /**
     * Set resetExpires
     *
     * @param integer $resetExpires
     *
     * @return User
     */
    public function setResetExpires($resetExpires)
    {
        $this->resetExpires = $resetExpires;

        return $this;
    }

    /**
     * Get resetExpires
     *
     * @return integer
     */
    public function getResetExpires()
    {
        return $this->resetExpires;
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }
}
