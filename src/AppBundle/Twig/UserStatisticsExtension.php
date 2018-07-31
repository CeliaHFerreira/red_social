<?php

namespace AppBundle\Twig;

use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Description of UserStatisticsExtension
 *
 * @author Celia
 */
class UserStatisticsExtension extends \Twig_Extension {

	protected $doctrine;

	public function __construct(RegistryInterface $doctrine) {
		$this->doctrine = $doctrine;
	}

	public function getFilters() {
		return array(
			new \Twig_SimpleFilter('user_statistics', array($this, 'userStatisticsFilter'))
		);
	}

	public function userStatisticsFilter($user) {
		$following_repo = $this->doctrine->getRepository('BackendBundle:Following');
		$melody_repo = $this->doctrine->getRepository('BackendBundle:Melody');

		//Sacar las estadisticas de cada categoría (siguiendo, seguidores y melodías)
		$user_following = $following_repo->findBy(array(
			'user' => $user
		));
		$user_followers = $following_repo->findBy(array(
			'followed' => $user
		));
		$user_melodies = $melody_repo->findBy(array(
			'user' => $user
		));

		$result = array(
			'following' => count($user_following),
			'followers' => count($user_followers),
			'melodies' => count($user_melodies)
		);

		return $result;
	}

}
