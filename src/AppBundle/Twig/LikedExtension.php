<?php
/*
 * LikedExtension, twig used to create a filter for likes
 */
namespace AppBundle\Twig;

use Symfony\Bridge\Doctrine\RegistryInterface;
/**
 * Description of LikeExtension
 *
 * @author Celia
 */
class LikedExtension extends \Twig_Extension{
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
			new \Twig_SimpleFilter('liked', array($this, 'likedFilter'))
		);
	}
	
	/**
	 * LIKED FILTER
	 * 
	 * @param string $user necessary
	 * @param string $melody necessary
	 * @return result of the filter
	 */
	public function likedFilter($user, $melody){
		$like_repo = $this->doctrine->getRepository('BackendBundle:Like');
		$melody_liked = $like_repo->findOneBy(array(
			"user" => $user,
			"melody" => $melody
		));
		
		if(!empty($melody_liked) && is_object($melody_liked)){
			$result = true;
		}else{
			$result = false;
		}
		
		return $result;
	}
}
