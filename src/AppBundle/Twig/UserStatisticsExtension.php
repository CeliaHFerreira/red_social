<?php
/*
 * UserStatisticsExtension, twig used to create a filter for stats
 */
namespace AppBundle\Twig;

use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Description of UserStatisticsExtension
 *
 * @author Celia
 */
class UserStatisticsExtension extends \Twig_Extension {

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
			new \Twig_SimpleFilter('user_statistics', array($this, 'userStatisticsFilter'))
		);
	}
	
	/**
	 * USER STATISTICS FILTER
	 * 
	 * @param string $user necessary
	 * @return result of the filter
	 */
	public function userStatisticsFilter($user) {
		$following_repo = $this->doctrine->getRepository('BackendBundle:Following');
		$melody_repo = $this->doctrine->getRepository('BackendBundle:Melody');
		$like_repo = $this->doctrine->getRepository('BackendBundle:Like');

		//Sacar las estadisticas de cada categoría (siguiendo, seguidores y melodías)
		$user_following = $following_repo->findBy(array(
			'user' => $user
		));
		$user_followers = $following_repo->findBy(array(
			'followed' => $user
		));
		$user_likes = $like_repo->findBy(array(
			'user' => $user
		));
		$user_melodies = $melody_repo->findBy(array(
			'user' => $user
		));

		$result = array(
			'following' => count($user_following),
			'followers' => count($user_followers),
			'likes' => count($user_likes),
			'melodies' => count($user_melodies)
		);

		return $result;
	}

}
