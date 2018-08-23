<?php

/**
 * MelodyController, package used to manage melodies
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
//Entidades
use BackendBundle\Entity\Melody;
use BackendBundle\Entity\Assesment;
use BackendBundle\Entity\Like;
use BackendBundle\Entity\Score;
use BackendBundle\Entity\User;
//Formularios
use AppBundle\Form\MelodyType;
use AppBundle\Form\CommentType;

/**
 * Description of MelodyController
 *
 * @author Celia
 */
class MelodyController extends Controller {

	private $session;
	
	/**
	 * CONSTRUCTOR
	 */
	public function __construct() {
		$this->session = new Session();
	}

	/**
	 * CREAR MELODÍA
	 * 
	 * @param Request $request necessary
	 * @return view home
	 */
	public function indexAction(Request $request) {
		$user = $this->getUser();
		if ($user->getActive() != "true") {
			return $this->redirect($this->generateUrl('activate'));
		}
		$em = $this->getDoctrine()->getManager();
		$melody = new Melody();
		$form = $this->createForm(MelodyType::class, $melody);
		$form->handleRequest($request);
		if ($form->isSubmitted()) {
			if ($form->isValid()) {
				//upoad midi
				$midi = $form['melody']->getData();

				if (!empty($midi) && $midi != null) {
					$ext = $midi->guessExtension();

					if ($ext == 'mid' || $ext == 'MID') {
						$file_name = $user->getId() . time() . "." . $ext;
						$midi->move("uploads/melodies/midis", $file_name);

						$melody->setMelody($file_name);

						$melody->setUser($user);
						$melody->setCreationDate(new \DateTime("now"));

						$em->persist($melody);
						$fush = $em->flush();

						if ($fush == null) {
							$this->addFlash(
									'notice', 'La melodía se ha creado correctamentee'
							);
						} else {
							$this->addFlash(
									'error', 'Error al añadir la melodía'
							);
						}
					} else {
						$this->addFlash(
								'error', 'Error al añadir el midi'
						);
					}
				} else {
					$this->addFlash(
							'error', 'Error al añadir la melodía'
					);
				}
			} else {
				$this->addFlash(
						'error', 'La melodía no puede ser creada ya que han existido errores en el formulario'
				);
			}

			return $this->redirectToRoute('app_homepage');
		}

		$melodies = $this->getMelodies($request);

		return $this->render('@App/Melody/home.html.twig', array(
					'form' => $form->createView(),
					'pagination' => $melodies
		));
	}
	
	/**
	 * get melodies
	 * 
	 * @param Request $request necessary
	 * @return pagination necessary for view home
	 */
	public function getMelodies($request) {
		$em = $this->getDoctrine()->getManager();
		$user = $this->getUser();

		$melodies_repo = $em->getRepository('BackendBundle:Melody');
		$following_repo = $em->getRepository('BackendBundle:Following');

		//SELECT melody FROM melodies WHERE user_id = X 
		//OR user_id IN (SELECT followed FROM following WHERE user = X);
		//Primero sacar seguidores (following)
		$following = $following_repo->findBy(array('user' => $user));

		$following_array = array();
		//array con todos los followed
		foreach ($following as $follow) {
			$following_array[] = $follow->getFollowed();
		}

		$query = $melodies_repo->createQueryBuilder('m')
				->where('m.user = (:user_id) OR m.user IN (:following)')
				->setParameter('user_id', $user->getId())
				->setParameter('following', $following_array)
				->orderBy('m.id', 'DESC')
				->getQuery();

		$paginator = $this->get('knp_paginator');
		$pagination = $paginator->paginate(
				$query, $request->query->getInt('page', 1), 5
		);

		return $pagination;
	}

	/**
	 * ELIMINAR MELODÍA
	 * 
	 * @param Request $request necessary
	 * @param string $id null in the beggining
	 * @return response answer with the result
	 */
	public function removeMelodyAction(Request $request, $id = null) {
		$em = $this->getDoctrine()->getManager();
		$melody_repo = $em->getRepository('BackendBundle:Melody');
		$melody = $melody_repo->find($id);
		$assesment_repo = $em->getRepository('BackendBundle:Assesment');
		$assesmenting = $assesment_repo->findBy(array(
			'melody' => $id
		));

		foreach ($assesmenting as $assesment) {
			$em->remove($assesment);
			$flush = $em->flush();
		}
		$like_repo = $em->getRepository('BackendBundle:Like');
		$liking = $like_repo->findBy(array(
			'melody' => $id
		));

		foreach ($liking as $like) {
			$em->remove($like);
			$flush = $em->flush();
		}
		$notification_repo = $em->getRepository('BackendBundle:Notification');
		$notifications = $notification_repo->findBy(array(
			'extra' => $id
		));
		foreach ($notifications as $notification) {
			$em->remove($notification);
			$flush = $em->flush();
		}
		$score_repo = $em->getRepository('BackendBundle:Score');
		$scores = $score_repo->findBy(array(
			'melody' => $id
		));
		foreach ($scores as $score) {
			$em->remove($score);
			$flush = $em->flush();
		}
		$user = $this->getUser();

		//Condición de que solo borraremos la melodía si somos los propietarios de ella
		if ($user->getId() == $melody->getUser()->getId()) {
			$em->remove($melody);
			$flush = $em->flush();

			if ($flush == null) {
				$status = 'La melodía se ha borrado correctamente';
			} else {
				$status = 'La melodía no se ha borrado correctamente';
			}
		} else {
			$status = 'La melodía no se ha borrado';
		}
		return new Response($status);
	}

	/**
	 * COMENTAR MELODÍA
	 * 
	 * @param Request $request necessary
	 * @param strin $id null in the beggining
	 * @return view melody or home
	 */
	public function commentMelodyAction(Request $request, $id = null) {
		$em = $this->getDoctrine()->getManager();
		$user = $this->getUser();
		$melody_repo = $em->getRepository('BackendBundle:Melody');
		$melody = $melody_repo->find($id);

		$comment = new Assesment();
		$form = $this->createForm(CommentType::class);
		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			if ($form->isValid()) {

				$comment->setUser($user);
				$comment->setMelody($melody);
				$comment->setText($form->get("text")->getData());
				$em->persist($comment);
				$fush = $em->flush();

				if ($fush == null) {
					if ($melody->getUser() != $user) {
						//Guardamos la notificación para poder mostrarla (servicio notification)
						$notification = $this->get('app.notification_service');
						$notification->set($melody->getUser(), 'assest', $user->getid(), $comment->getId(), $melody->getId());
					}
					$this->addFlash(
							'notice', 'El comentario se ha guardado correctamente'
					);
				} else {
					$this->addFlash(
							'error', 'Error al añadir el comentario'
					);
				}
			} else {
				$this->addFlash(
						'error', 'El comentario no puede ser creado ya que han existido errores en el formulario'
				);
			}
			$comments = $this->getComments($request, $melody);

			return $this->render('@App/Melody/melody.html.twig', array(
						'melody' => $melody,
						'form' => $form->createView(),
						'pagination' => $comments
			));
		}

		$comments = $this->getComments($request, $melody);

		if (!empty($melody)) {
			return $this->render('@App/Melody/melody.html.twig', array(
						'melody' => $melody,
						'form' => $form->createView(),
						'pagination' => $comments
			));
		} else {
			return $this->redirectToRoute('app_homepage');
		}
	}

	/**
	 * get comments
	 * 
	 * @param Request $request necessary
	 * @return pagination necessary for view melody
	 */
	public function getComments($request, $id = null) {
		$em = $this->getDoctrine()->getManager();
		$user = $this->getUser();

		$melody_repo = $em->getRepository('BackendBundle:Melody');
		$comment_repo = $em->getRepository('BackendBundle:Assesment');
		$melody = $melody_repo->find($id);
		$commented_melody = $melody->getId();

		$dql = "SELECT c from BackendBundle:Assesment c WHERE c.melody = $commented_melody ORDER BY c.id DESC";
		$query = $em->createQuery($dql);
		$paginator = $this->get('knp_paginator');
		$pagination = $paginator->paginate(
				$query, $request->query->getInt('page', 1), 5
		);

		return $pagination;
	}

	/**
	 * BORRAR COMENTARIO
	 * 
	 * @param Request $request necessary
	 * @param strin $id null in the beggining
	 * @return response answer with the result
	 */
	public function deleteCommentAction(Request $request, $id = null) {
		$user = $this->getUser();
		$em = $this->getDoctrine()->getManager();
		$comment_repo = $em->getRepository('BackendBundle:Assesment');
		$comment = $comment_repo->findOneBy(array(
			'user' => $user,
			'id' => $id
		));
		$em->remove($comment);
		$flush = $em->flush();

		if ($flush == null) {
			$status = 'Has borrado la valoración';
		} else {
			$status = 'No se ha podido eliminar la valoración';
		}
		//Borramos también la notificación
		$notification_repo = $em->getRepository('BackendBundle:Notification');
		$notification = $notification_repo->findOneBy(array(
			'assestId' => $id
		));

		if ($notification != null) {
			$em->remove($notification);
			$flush = $em->flush();
			if ($flush == null) {
				$status = 'La notificatión se ha borrado correctamente';
			} else {
				$status = 'La notificatión no se ha borrado correctamente';
			}
		}


		return new Response($status);
	}

	/**
	 * PUBLICAR MELODÍA
	 * 
	 * @param Request $request necessary
	 * @return view music
	 */
	public function musicAction(Request $request) {
		$user = $this->getUser();
		$em = $this->getDoctrine()->getManager();
		$melody = new Melody();
		$form = $this->createForm(MelodyType::class, $melody);
		$form->handleRequest($request);
		if ($form->isSubmitted()) {
			if ($form->isValid()) {
				//upoad midi
				$midi = $form['melody']->getData();

				if (!empty($midi) && $midi != null) {
					$ext = $midi->guessExtension();

					if ($ext == 'mid' || $ext == 'MID') {
						$file_name = $user->getId() . time() . "." . $ext;
						$midi->move("uploads/melodies/midis", $file_name);

						$melody->setMelody($file_name);
					} else {
						$melody->setMelody(null);
					}
				} else {
					$melody->setMelody(null);
				}

				$melody->setUser($user);
				$melody->setCreationDate(new \DateTime("now"));

				$em->persist($melody);
				$fush = $em->flush();

				if ($fush == null) {
					$this->addFlash(
							'notice', 'La melodía se ha creado correctamentee'
					);
				} else {
					$this->addFlash(
							'error', 'Error al añadir la melodía'
					);
				}
			} else {
				$this->addFlash(
						'error', 'La melodía no puede ser creada ya que han existido errores en el formulario'
				);
			}

			return $this->redirectToRoute('app_homepage');
		}
		return $this->render('@App/Melody/music.html.twig', array(
					'form' => $form->createView()
		));
	}

	/**
	 * DAR A LIKE
	 * 
	 * @param string $id null in the beggining
	 * @return response answer with the result
	 */
	public function likeAction($id = null) {
		$user = $this->getUser();
		$em = $this->getDoctrine()->getManager();
		$melody_repo = $em->getRepository('BackendBundle:Melody');
		$melody = $melody_repo->find($id);

		$like = new Like();
		$like->setUser($user);
		$like->setMelody($melody);

		$em->persist($like);
		$flush = $em->flush();

		if ($flush == null) {
			if ($melody->getUser() != $user) {
				//Guardamos la notificación para poder mostrarla (servicio notification)
				$notification = $this->get('app.notification_service');
				$notification->set($melody->getUser(), 'like', $user->getid(), 0, $melody->getId());
			}
			$status = 'Te ha gustado una melodía';
		} else {
			$status = 'No se ha podido guardar el me gusta';
		}

		return new Response($status);
	}

	/**
	 * QUITAR LIKE
	 * 
	 * @param string $id null in the beggining
	 * @return response answer with the result
	 */
	public function unlikeAction($id = null) {
		$user = $this->getUser();
		$em = $this->getDoctrine()->getManager();
		$like_repo = $em->getRepository('BackendBundle:Like');
		$like = $like_repo->findOneBy(array(
			'user' => $user,
			'melody' => $id
		));
		$em->remove($like);
		$flush = $em->flush();

		if ($flush == null) {
			$status = 'Has borrado el me gusta';
		} else {
			$status = 'No se ha podido eliminar el me gusta';
		}
		//Borramos también la notificación
		$notification_repo = $em->getRepository('BackendBundle:Notification');
		$notification = $notification_repo->findOneBy(array(
			'extra' => $id
		));

		if ($notification != null) {
			$em->remove($notification);
			$flush = $em->flush();
			if ($flush == null) {
				$status = 'La notificatión se ha borrado correctamente';
			} else {
				$status = 'La notificatión no se ha borrado correctamente';
			}
		}
		return new Response($status);
	}

	/**
	 * VER LIKES
	 * 
	 * @param Request $request necessary
	 * @param string $username null in the beggining
	 * @return view likes
	 */
	public function likedAction(Request $request, $username = null) {
		$em = $this->getDoctrine()->getManager();

		if ($username != null) {
			$user_repo = $em->getRepository("BackendBundle:User");
			$user = $user_repo->findOneBy(array("username" => $username));
		} else {
			$user = $this->getUser();
		}
		if (empty($user) || !is_object($user)) {
			return $this->redirect($this->generateUrl('app_homepage'));
		}

		$user_id = $user->getId();
		$dql = "SELECT l FROM BackendBundle:Like l WHERE l.user = $user_id ORDER BY l.id DESC";
		$query = $em->createQuery($dql);

		$paginator = $this->get('knp_paginator');
		$like = $paginator->paginate(
				$query, $request->query->getInt('page', 1), 5
		);

		return $this->render('@App/Melody/likes.html.twig', array(
					'user' => $user,
					'pagination' => $like
		));
	}

	
	/**
	 * VALORAR MELODÍA
	 * 
	 * @param Request $request necessary
	 * @param string $id null in the beggining
	 * @param string $value null in the beggining
	 * @return response answer with the result
	 */
	public function scoreAction(Request $request, $id = null, $value = null) {
		$user = $this->getUser();
		$em = $this->getDoctrine()->getManager();
		$melody_repo = $em->getRepository('BackendBundle:Melody');
		$melody = $melody_repo->find($id);

		$score = new Score();
		$score->setUser($user);
		$score->setMelody($melody);
		$score->setScore($value);
		$role = $user->getRoles();

		if ($role == "ROLE_USER") {
			$pondered = 35 / 100;
			$score->setPonderedScore($value * $pondered);
		} else if ($role == "ROLE_ADMIN") {
			$pondered = 40 / 100;
			$score->setPonderedScore($value * $pondered);
		} else if ($role == "ROLE_COMPOSER") {
			$pondered = 70 / 100;
			$score->setPonderedScore($value * $pondered);
		}

		$score_repo = $em->getRepository('BackendBundle:Score');
		$score_exist = $score_repo->findOneBy(array(
			'user' => $user,
			'melody' => $id
		));
		if ($score_exist == null) {
			$em->persist($score);
			$flush = $em->flush();

			if ($flush == null) {
				if ($melody->getUser() != $user) {
					//Guardamos la notificación para poder mostrarla (servicio notification)
					$notification = $this->get('app.notification_service');
					$notification->set($melody->getUser(), 'score', $user->getid(), 0, $melody->getId());
				}
				$status = 'Has puntuado una melodía';
			} else {
				$status = 'No se ha podido puntuar la melodía';
			}
		} else {
			$status = 'La melodía ya estaba puntuada';
		}

		return new Response($status);
	}

	/**
	 * VALORAR MELODÍA
	 * 
	 * @param Request $request necessary
	 * @param string $id null in the beggining
	 * @return view stats
	 */
	public function statsMelodyAction(Request $request, $id = null) {
		$em = $this->getDoctrine()->getManager();
		$user = $this->getUser();
		$melody_repo = $em->getRepository('BackendBundle:Melody');
		$melody = $melody_repo->find($id);
		$owner = $melody->getUser();

		$likes = $this->getLikes($request, $melody);
		$scores = $this->getScores($request, $melody);

		if (!empty($melody) && $owner == $user) {
			return $this->render('@App/Melody/stats_melody.html.twig', array(
						'melody' => $melody,
						'pagination_likes' => $likes,
						'pagination_scores' => $scores
			));
		} else {
			return $this->redirectToRoute('app_homepage');
		}
	}

	/**
	 * get likes
	 * 
	 * @param Request $request necessary
	 * @return pagination necessary for view stats
	 */
	public function getLikes($request, $id = null) {
		$em = $this->getDoctrine()->getManager();
		$user = $this->getUser();

		$melody_repo = $em->getRepository('BackendBundle:Melody');
		$like_repo = $em->getRepository('BackendBundle:Like');
		$melody = $melody_repo->find($id);
		$liked_melody = $melody->getId();

		$dql = "SELECT l from BackendBundle:Like l WHERE l.melody = $liked_melody ORDER BY l.id DESC";
		$query = $em->createQuery($dql);
		$paginator = $this->get('knp_paginator');
		$pagination = $paginator->paginate(
				$query, $request->query->getInt('page', 1), 50
		);

		return $pagination;
	}

	/**
	 * get comments
	 * 
	 * @param Request $request necessary
	 * @return pagination necessary for view stats
	 */
	public function getScores($request, $id = null) {
		$em = $this->getDoctrine()->getManager();
		$user = $this->getUser();

		$melody_repo = $em->getRepository('BackendBundle:Melody');
		$like_repo = $em->getRepository('BackendBundle:Score');
		$melody = $melody_repo->find($id);
		$scored_melody = $melody->getId();

		$dql = "SELECT s from BackendBundle:Score s WHERE s.melody = $scored_melody ORDER BY s.id DESC";
		$query = $em->createQuery($dql);
		$paginator = $this->get('knp_paginator');
		$pagination = $paginator->paginate(
				$query, $request->query->getInt('page', 1), 50
		);

		return $pagination;
	}

}
