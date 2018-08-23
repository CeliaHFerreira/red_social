<?php

namespace AppBundle\Twig;

/**
 * Description of TimeExtension
 *
 * @author Celia
 */
class TimeExtension extends \Twig_Extension {

	/**
	 * FILTERS
	 */
	public function getFilters() {
		return array(
			new \Twig_SimpleFilter('time', array($this, 'TimeFilter')),
		);
	}

	/**
	 * TIME FILTER
	 * 
	 * @param string $date necessary
	 * @return result of the filter
	 */
	public function TimeFilter($date) {
		if ($date == null) {
			return "Sin fecha";
		}

		$start_date = $date;
		$since_start = $start_date->diff(new \DateTime(date("Y-m-d") . " " . date("H:i:s")));

		if ($since_start->y == 0) {
			if ($since_start->m == 0) {
				if ($since_start->d == 0) {
					if ($since_start->h == 0) {
						if ($since_start->i == 0) {
							if ($since_start->s == 0) {
								$result = $since_start->s . ' segundos';
							} else {
								if ($since_start->s == 1) {
									$result = $since_start->s . ' segundo';
								} else {
									$result = $since_start->s . ' segundos';
								}
							}
						} else {
							if ($since_start->i == 1) {
								$result = $since_start->i . ' minuto';
							} else {
								$result = $since_start->i . ' minutos';
							}
						}
					} else {
						if ($since_start->h == 1) {
							$result = $since_start->h . ' hora';
						} else {
							$result = $since_start->h . ' horas';
						}
					}
				} else {
					if ($since_start->d == 1) {
						$result = $since_start->d . ' día';
					} else {
						$result = $since_start->d . ' días';
					}
				}
			} else {
				if ($since_start->m == 1) {
					$result = $since_start->m . ' mes';
				} else {
					$result = $since_start->m . ' meses';
				}
			}
		} else {
			if ($since_start->y == 1) {
				$result = $since_start->y . ' año';
			} else {
				$result = $since_start->y . ' años';
			}
		}
		return "Hace " . $result;
	}
	
//	public function getName() {
//		return 'time_extension';
//	}
}
