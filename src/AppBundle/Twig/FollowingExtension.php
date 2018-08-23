<?php
/**
 * FollowingExtension, twig used to create a filter for follows between users
 */

//Namespaces
namespace AppBundle\Twig;

use Symfony\Bridge\Doctrine\RegistryInterface;
/**
 * Description of FollowingExtension
 *
 * @author Celia
 */
class FollowingExtension extends \Twig_Extension{
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
			new \Twig_SimpleFilter('following', array($this, 'followingFilter'))
		);
	}
	
	/**
	 * FOLLOWING FILTER
	 * 
	 * @param string $user necessary
	 * @param string $followed necessary
	 * @return result of the filter
	 */
	public function followingFilter($user, $followed){
		$following_repo = $this->doctrine->getRepository('BackendBundle:Following');
		$user_following = $following_repo->findOneBy(array(
			"user" => $user,
			"followed" => $followed
		));
		
		if(!empty($user_following) && is_object($user_following)){
			$result = true;
		}else{
			$result = false;
		}
		
		return $result;
	}
	
//	public function getName() {
//		return 'following_extension';
//	}
}
