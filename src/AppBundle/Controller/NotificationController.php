<?php
/*
 * NotificationController, package used to manage notifications
 */
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of NotificationController
 *
 * @author Celia
 */
class NotificationController extends Controller {
	
	/**
	 * NOTIFICACIONES
	 * 
	 * @param Request $request necessary
	 * @return view notifications
	 */
	public function indexAction(Request $request) {
		$em = $this->getDoctrine()->getManager();

		$user = $this->getUser();
		$userId = $user->getId();
		$dql = "SELECT n FROM BackendBundle:Notification n WHERE n.user = $userId ORDER BY n.id DESC";
		$query = $em->createQuery($dql);

		$paginator = $this->get('knp_paginator');
		$notifications = $paginator->paginate(
				$query, $request->query->getInt('page', 1), 5
		);

		//Marcamos las notificaciones como leídas (servicio Notification)
		$notification = $this->get('app.notification_service');
		$notification->readed($user);

		return $this->render('@App/Notification/notifications_page.html.twig', array(
					'user' => $user,
					'pagination' => $notifications
		));
	}

	/**
	 * contar notificaciones
	 * 
	 * Contar el número de notifiaciones nuevas (sin leer)
	 * @return response number of notifications
	 */
	public function countNotificationsAction() {
		$em = $this->getDoctrine()->getManager();

		$notification_repo = $em->getRepository('BackendBundle:Notification');
		$notifications = $notification_repo->findBy(array(
			'user' => $this->getUser(),
			'readed' => 0
		));

		return new Response(count($notifications));
	}

}
