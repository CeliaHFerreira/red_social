<?php
/**
 * GetUserExtension, twig used to create a filter for users
 */
namespace AppBundle\Twig;

use Symfony\Bridge\Doctrine\RegistryInterface;
/**
 * Description of GetUserExtension
 *
 * @author Celia
 */
class GetUserExtension extends \Twig_Extension{
	protected $doctrine;
	
	/**
	 * CONSTRUCTOR
	 * 
	 * @param ORM $doctrine
	 */
	public function __construct(RegistryInterface $doctrine) {
		$this->doctrine = $doctrine;
	}
	
	/**
	 * FILTERS
	 */
	public function getFilters() {
		return array(
			new \Twig_SimpleFilter('get_user', array($this, 'getUserFilter'))
		);
	}
	
	/**
	 * GET USER FILTER
	 * 
	 * @param string $user_id necessary
	 * @return result of the filter
	 */
	public function getUserFilter($user_id){
		$user_repo = $this->doctrine->getRepository('BackendBundle:User');
		$user = $user_repo->findOneBy(array(
			"id" => $user_id
		));
		
		if(!empty($user) && is_object($user)){
			$result = $user;
		}else{
			$result = false;
		}
		return $result;
	}
}
