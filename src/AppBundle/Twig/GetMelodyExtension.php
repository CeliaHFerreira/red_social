<?php

namespace AppBundle\Twig;

use Symfony\Bridge\Doctrine\RegistryInterface;
/**
 * Description of GetMelodyExtension
 *
 * @author Celia
 */
class GetMelodyExtension extends \Twig_Extension{
	protected $doctrine;
	
	public function __construct(RegistryInterface $doctrine) {
		$this->doctrine = $doctrine;
	}
	
	public function getFilters() {
		return array(
			new \Twig_SimpleFilter('get_melody', array($this, 'getMelodyFilter'))
		);
	}
	
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
