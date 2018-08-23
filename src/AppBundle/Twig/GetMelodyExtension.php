<?php
/*
 * GetMelodyExtension, twig used to create a filter for melodies
 */
namespace AppBundle\Twig;

use Symfony\Bridge\Doctrine\RegistryInterface;
/**
 * Description of GetMelodyExtension
 *
 * @author Celia
 */
class GetMelodyExtension extends \Twig_Extension{
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
			new \Twig_SimpleFilter('get_melody', array($this, 'getMelodyFilter'))
		);
	}
	
	/**
	 * GET MELODY FILTER
	 * 
	 * @param string $melody_id necessary
	 * @return result of the filter
	 */
	public function getMelodyFilter($melody_id){
		$melody_repo = $this->doctrine->getRepository('BackendBundle:Melody');
		$melody = $melody_repo->findOneBy(array(
			"id" => $melody_id
		));
		
		if(!empty($melody) && is_object($melody)){
			$result = $melody;
		}else{
			$result = false;
		}
		return $result;
	}
}
