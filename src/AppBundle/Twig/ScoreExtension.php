<?php
/*
 * ScoresExtension, twig used to create a filter for socres
 */
namespace AppBundle\Twig;

use Symfony\Bridge\Doctrine\RegistryInterface;
/**
 * Description of ScoreExtension
 *
 * @author Celia
 */
class ScoreExtension extends \Twig_Extension{
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
			new \Twig_SimpleFilter('scored', array($this, 'scoredFilter'))
		);
	}
	
	/**
	 * SCORED FILTER
	 * 
	 * @param string $user necessary
	 * @param string $melody necessary
	 * @return result of the filter
	 */
	public function scoredFilter($user, $melody){
		$scored_repo = $this->doctrine->getRepository('BackendBundle:Score');
		$melody_scored = $scored_repo->findOneBy(array(
			"user" => $user,
			"melody" => $melody
		));
		
		if(!empty($melody_scored) && is_object($melody_scored)){
			$result = false;
		}else{
			$result = true;
		}
		
		return $result;
	}
}
