<?php
/*
 * NotificationService, service to manage notifications
 */

namespace AppBundle\Services;

use BackendBundle\Entity\Notification;

/**
 * Description of NotificationService
 *
 * @author Celia
 */
class NotificationService {

	private $manager;

	/**
	 * CONSTRUCTOR
	 * 
	 * @param EntityManager $manager
	 */
	public function __construct(\Doctrine\ORM\EntityManager $manager) {
		$this->manager = $manager;
	}

	/**
	 * SET NOTIFICATION
	 * 
	 * @param string $user user to notificate
	 * @param string $type type of notification
	 * @param string $type_id id item notification
	 * @param string $assest_id id assest
	 * @param string $extra information extra
	 * 
	 * @return string status of notifications
	 */
	public function set($user, $type, $typeId, $assest_id = null, $extra = null) {
		$em = $this->manager;

		$notification = new Notification();
		$notification->setUser($user);
		$notification->setType($type);
		$notification->setTypeId($typeId);
		$notification->setAssestId($assest_id);
		$notification->setReaded(0);
		$notification->setCreationDate(new \DateTime("now"));
		$notification->setExtra($extra);

		$em->persist($notification);
		$flush = $em->flush();

		if ($flush == null) {
			$status = true;
		} else {
			$status = false;
		}

		return $status;
	}

	/**
	 * READED
	 * 
	 * @param string $user user to notificate
	 * 
	 * @return string status of notification whether readed or nots
	 */
	public function readed($user) {
		$em = $this->manager;

		$notification_repo = $em->getRepository('BackendBundle:Notification');
		$notification_user = $notification_repo->findBy(array(
			'user' => $user
		));

		foreach ($notification_user as $notification) {
			$notification->setReaded(1);
			$em->persist($notification);
		}
		$flush = $em->flush();
		if ($flush == null) {
			return true;
		} else {
			return false;
		}
	}

}
