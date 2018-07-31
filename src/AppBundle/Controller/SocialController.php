<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Services\NotificationService;
//Bundles
use BackendBundle\Entity\User;
use BackendBundle\Entity\Following;

/**
 * Description of SocialController
 *
 * @author Celia
 */
class SocialController extends Controller {

	private $session;

	/**
	 * CONSTRUCTOR
	 */
	public function __construct() {
		$this->session = new Session();
	}

	/**
	 * AÑADIR USUARIOS
	 */
	public function followAction(Request $request) {
		$user = $this->getUser();
		$followed_id = $request->get('followed');

		$em = $this->getDoctrine()->getManager();

		$user_repo = $em->getRepository('BackendBundle:User');
		$followed = $user_repo->find($followed_id);

		$following = new Following();
		$following->setUser($user);
		$following->setFollowed($followed);

		$em->persist($following);
		$flush = $em->flush();

		if ($flush == null) {
			//Guardamos la notificación para poder mostrarla (servicio notification)
			$notification = $this->get('app.notification_service');
			$notification->set($followed, 'follow', $user->getid());
			
			$status = "Se está siguiendo a dicho usuario";
		} else {
			$status = "Se ha producido un error, y no se está siguiendo a dicho usuario";
		}

		return new Response($status);
	}

	/**
	 * ELIMINAR USUARIOS
	 */
	public function unfollowAction(Request $request) {
		$user = $this->getUser();
		$followed_id = $request->get('followed');

		$em = $this->getDoctrine()->getManager();

		$following_repo = $em->getRepository('BackendBundle:Following');
		$followed = $following_repo->findOneBy(array(
			'user' => $user,
			'followed' => $followed_id
		));

		$em->remove($followed);
		$flush = $em->flush();

		if ($flush == null) {
			$status = "Se deja de seguir a dicho usuario";
		} else {
			$status = "Se ha producido un error, y no se ha dejado de seguir a dicho usuario";
		}

		return new Response($status);
	}

	/**
	 * MOSTRAR USUARIOS SIGUIENDO
	 */
	public function followingAction(Request $request, $username = null) {
		$em = $this->getDoctrine()->getManager();

		if ($username != null) {
			$user_repo = $em->getRepository('BackendBundle:User');
			$user = $user_repo->findOneBy(array(
				"username" => $username
			));
		} else {
			$user = $this->getUser();
		}

		if (empty($user) || !is_object($user)) {
			return $this->redirect($this->generateUrl('app_homepage'));
		}

		$user_id = $user->getId();
		$dql = "SELECT f FROM BackendBundle:Following f WHERE f.user = $user_id ORDER BY f.id DESC";
		$query = $em->createQuery($dql);

		$paginator = $this->get('knp_paginator');
		$following = $paginator->paginate(
				$query, $request->query->getInt('page', 1), 5
		);

		return $this->render('@App/Social/following.html.twig', array(
					'type' => 'following',
					'profile_user' => $user,
					'pagination' => $following
		));
	}

	/**
	 * MOSTRAR USUARIOS SEGUIDORES
	 */
	public function followedAction(Request $request, $username = null) {
		$em = $this->getDoctrine()->getManager();

		if ($username != null) {
			$user_repo = $em->getRepository('BackendBundle:User');
			$user = $user_repo->findOneBy(array(
				"username" => $username
			));
		} else {
			$user = $this->getUser();
		}

		if (empty($user) || !is_object($user)) {
			return $this->redirect($this->generateUrl('app_homepage'));
		}

		$user_id = $user->getId();
		$dql = "SELECT f FROM BackendBundle:Following f WHERE f.followed = $user_id ORDER BY f.id DESC";
		$query = $em->createQuery($dql);

		$paginator = $this->get('knp_paginator');
		$followed = $paginator->paginate(
				$query, $request->query->getInt('page', 1), 5
		);

		return $this->render('@App/Social/following.html.twig', array(
					'type' => 'followed',
					'profile_user' => $user,
					'pagination' => $followed
		));
	}
	
	/**
	 * LISTA DE USUARIOS, PAGINACIÓN
	 */
	public function usersAction(Request $request) {
		$user = $this->getUser();
		if ($user->getActive() != "true") {
			return $this->redirect($this->generateUrl('activate'));
		}
		$em = $this->getDoctrine()->getManager();

		$dql = "SELECT u FROM BackendBundle:User u ORDER BY u.id";
		$query = $em->createQuery($dql);

		$paginator = $this->get('knp_paginator');
		$pagination = $paginator->paginate(
				$query, $request->query->getInt('page', 1), 5
		);

		return $this->render('@App/Social/users.html.twig', array(
					'pagination' => $pagination
		));
	}
	
	/**
	 * BUSCAR USUARIOS
	 */
	public function searchAction(Request $request) {
		$user = $this->getUser();
		if ($user->getActive() != "true") {
			return $this->redirect($this->generateUrl('activate'));
		}
		$em = $this->getDoctrine()->getManager();

		$search = trim($request->query->get("search", null));

		if ($search == null) {
			return $this->redirect($this->generateUrl('home_melodies'));
		}

		$dql = "SELECT u FROM BackendBundle:User u "
				. " WHERE u.name LIKE :search OR u.surname LIKE :search OR u.username LIKE :search"
				. " ORDER BY u.id ASC";
		$query = $em->createQuery($dql)->setParameter('search', "%$search%");

		$paginator = $this->get('knp_paginator');
		$pagination = $paginator->paginate(
				$query, $request->query->getInt('page', 1), 5
		);

		return $this->render('@App/Social/users.html.twig', array(
					'pagination' => $pagination
		));
	}
	
	/**
	 * VER PERFIL DE USUARIO
	 */
	public function profileAction(Request $request, $username = null) {
		$user = $this->getUser();
		if ($user->getActive() != "true") {
			return $this->redirect($this->generateUrl('activate'));
		}

		$em = $this->getDoctrine()->getManager();

		if ($username != null) {
			$user_repo = $em->getRepository('BackendBundle:User');
			$user = $user_repo->findOneBy(array(
				"username" => $username
			));
		} else {
			$user = $this->getUser();
		}

		if (empty($user) || !is_object($user)) {
			return $this->redirect($this->generateUrl('app_homepage'));
		}

		$user_id = $user->getId();
		$dql = "SELECT m FROM BackendBundle:Melody m WHERE m.user = $user_id ORDER BY m.id DESC";
		$query = $em->createQuery($dql);

		$paginator = $this->get('knp_paginator');
		$melodies = $paginator->paginate(
				$query, $request->query->getInt('page', 1), 5
		);

		return $this->render('@App/Social/profile.html.twig', array(
					'user' => $user,
					'pagination' => $melodies
		));
	}
	

}
