<?php

//Bundles

namespace BackendBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Serializable;

/**
 * User
 */
class User implements AdvancedUserInterface {

	/**
	 * id
	 * 
	 * @var integer
	 */
	private $id;

	/**
	 * name user
	 * 
	 * @var string
	 */
	private $name;

	/**
	 * surname user
	 * 
	 * @var string
	 */
	private $surname;

	/**
	 * username user
	 * 
	 * @var string
	 */
	private $username;

	/**
	 * email user
	 * 
	 * @var string
	 */
	private $email;

	/**
	 * password
	 * 
	 * @var string
	 */
	private $password;

	/**
	 * role
	 * 
	 * @var string
	 */
	private $role;

	/**
	 * description
	 * 
	 * @var string
	 */
	private $description;

	/**
	 * image
	 * 
	 * @var string
	 */
	private $image;

	/**
	 * activate
	 * 
	 * @var string
	 */
	private $active;
	
	/**
	 * changePassword key
	 * @var string
	 */
	private $changepassword;

	////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function getNameUser() {
		return $this->email;
	}

	public function getSalt() {
		return null;
	}

	public function getRoles() {
		return array('ROLE_USER', 'ROLE_ADMIN', 'ROLE_COMPOSER');
	}

	public function eraseCredentials() {
		
	}

	public function __toString() {
		return $this->name;
	}

	public function serialize() {
		return serialize(array(
			$this->getId(),
			$this->email,
			$this->password
		));
	}

	public function unserialize($serialized) {
		list(
				$this->id,
				$this->email,
				$this->password
				) = unserialize($serialized);
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
        return $this->getActive();
    }

    // serialize and unserialize must be updated - see below
//    public function serialize()
//    {
//        return serialize(array(
//            // ...
//            $this->isActive,
//        ));
//    }
//    public function unserialize($serialized)
//    {
//        list (
//            // ...
//            $this->getActive(),
//        ) = unserialize($serialized);
//    }

	////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return User
	 */
	public function setName($name) {
		$this->name = $name;

		return $this;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Set surname
	 *
	 * @param string $surname
	 *
	 * @return User
	 */
	public function setSurname($surname) {
		$this->surname = $surname;

		return $this;
	}

	/**
	 * Get surname
	 *
	 * @return string
	 */
	public function getSurname() {
		return $this->surname;
	}

	/**
	 * Set username
	 *
	 * @param string $username
	 *
	 * @return User
	 */
	public function setUsername($username) {
		$this->username = $username;

		return $this;
	}

	/**
	 * Get username
	 *
	 * @return string
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * Set email
	 *
	 * @param string $email
	 *
	 * @return User
	 */
	public function setEmail($email) {
		$this->email = $email;

		return $this;
	}

	/**
	 * Get email
	 *
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * Set password
	 *
	 * @param string $password
	 *
	 * @return User
	 */
	public function setPassword($password) {
		$this->password = $password;

		return $this;
	}

	/**
	 * Get password
	 *
	 * @return string
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * Set role
	 *
	 * @param string role
	 *
	 * @return User
	 */
	public function setRole($role) {
		$this->role = $role;

		return $this;
	}

	/**
	 * Get role
	 *
	 * @return string
	 */
	public function getRole() {
		return $this->role;
	}

	/**
	 * Set description
	 *
	 * @param string $description
	 *
	 * @return User
	 */
	public function setDescription($description) {
		$this->description = $description;

		return $this;
	}

	/**
	 * Get description
	 *
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Set image
	 *
	 * @param string $image
	 *
	 * @return User
	 */
	public function setImage($image) {
		$this->image = $image;

		return $this;
	}

	/**
	 * Get image
	 *
	 * @return string
	 */
	public function getImage() {
		return $this->image;
	}

	/**
	 * Set active
	 *
	 * @param string $active
	 *
	 * @return User
	 */
	public function setActive($active) {
		$this->active = $active;

		return $this;
	}

	/**
	 * Get active
	 *
	 * @return string
	 */
	public function getActive() {
		return $this->active;
	}
	
	/**
     * Set changepassword
     *
     * @param string $changepassword
     *
     * @return User
     */
    public function setChangepassword($changepassword)
    {
        $this->changepassword = $changepassword;

        return $this;
    }

    /**
     * Get changepassword
     *
     * @return string
     */
    public function getChangepassword()
    {
        return $this->changepassword;
    }



}
