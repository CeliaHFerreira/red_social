<?php
/*
 * PrivateMessageController, package used to manage private messages
 */
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;
//Bundles
use BackendBundle\Entity\User;
use BackendBundle\Entity\PrivateMessage;
use AppBundle\Form\PrivateMessageType;

/**
 * Description of PrivateMessageController
 *
 * @author Celia
 */
class PrivateMessageController extends Controller {

	private $session;

	/**
	 * CONSTRUCTOR
	 */
	public function __construct() {
		$this->session = new Session();
	}

	/**
	 * ENVIAR MENSAJE, VER RECIBIDOS Y ENVIADOS
	 * 
	 * @param Request $request necessary
	 * @return view private messages
	 */
	public function indexAction(Request $request) {

		$em = $this->getDoctrine()->getManager();
		$user = $this->getUser();
		$private_message = new PrivateMessage();
		$form = $this->createForm(PrivateMessageType::class, $private_message, array(
			'empty_data' => $user
		));

		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			if ($form->isValid()) {
				//subir la imagen
				$file = $form['image']->getData();

				if (!empty($file) && $file != null) {
					$ext = $file->guessExtension();

					if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif') {
						$file_name = $user->getId() . time() . "." . $ext;
						$file->move("uploads/messages/images", $file_name);

						$private_message->setImage($file_name);
					} else {
						$private_message->setImage(null);
					}
				} else {
					$private_message->setImage(null);
				}

				//subir el documento
				$doc = $form['file']->getData();

				if (!empty($doc) && $doc != null) {
					$ext = $doc->guessExtension();

					if ($ext == 'mid' || $ext == 'MID') {
						$file_name = $user->getId() . time() . "." . $ext;
						$doc->move("uploads/messages/midis", $file_name);

						$private_message->setFile($file_name);
					} else {
						$private_message->setFile(null);
					}
				} else {
					$private_message->setFile(null);
				}
				$private_message->setEmitter($user);
				$private_message->setCreationDate(new \DateTime("now"));
				$private_message->setReaded(0);

				$em->persist($private_message);
				$flush = $em->flush();

				if ($flush == null) {
					$this->addFlash(
							'notice', 'El mensaje se ha enviado correctamente'
					);
				} else {
					$this->addFlash(
							'error', 'Error al enviar el mensaje'
					);
				}
			} else {
				$this->addFlash(
						'error', 'El mensaje privado no se ha enviado'
				);
			}

			return $this->redirectToRoute("private_message_index");
		}

		$private_messages_received = $this->getPrivateMessages($request);
		$private_messages_sended = $this->getPrivateMessages($request, "sended");
		$this->setReadedMessagesAction($em, $user);

		return $this->render('@App/PrivateMessage/index.html.twig', array(
					'form' => $form->createView(),
					'pagination_receive' => $private_messages_received,
					'pagination_sended' => $private_messages_sended
		));
	}

	/**
	 * get melodies
	 * 
	 * mostrar mensajes enviados o recibidos
	 * 
	 * @param Request $request necessary
	 * @param string &type null in the beggining
	 * @return pagination necessary for view private messages
	 */
	public function getPrivateMessages($request, $type = null) {
		$em = $this->getDoctrine()->getManager();
		$user = $this->getUser();
		$user_id = $user->getId();

		if ($type == "sended") {
			$dql = "SELECT p from BackendBundle:PrivateMessage p WHERE p.emitter = $user_id ORDER BY p.id DESC";
		} else {
			$dql = "SELECT p from BackendBundle:PrivateMessage p WHERE p.receiver = $user_id ORDER BY p.id DESC";
		}

		$query = $em->createQuery($dql);

		$paginator = $this->get('knp_paginator');
		$pagination = $paginator->paginate(
				$query, $request->query->getInt('page', 1), 50
		);
		return $pagination;
	}

	/**
	 * contar mensajes privados no leidos
	 * 
	 * @return response number of private messages not readed
	 */
	public function notReadedMessagesAction() {
		$em = $this->getDoctrine()->getManager();
		$user = $this->getUser();

		$private_message_repo = $em->getRepository('BackendBundle:PrivateMessage');
		$count_not_readed = count($private_message_repo->findBy(array(
					'receiver' => $user,
					'readed' => 0
		)));

		return new Response($count_not_readed);
	}

	/**
	 * marcar mensaje como leido
	 * 
	 * @param EntityManage $em necessary
	 * @param string $user necessary
	 * @return result private messages readed
	 */
	private function setReadedMessagesAction($em, $user) {
		$private_message_repo = $em->getRepository('BackendBundle:PrivateMessage');
		$messages = $private_message_repo->findBy(array(
			'receiver' => $user,
			'readed' => 0
		));

		foreach ($messages as $msg) {
			$msg->setReaded(1);

			$em->persist($msg);
		}

		$flush = $em->flush();

		if ($flush == null) {
			$result = true;
		} else {
			$result = false;
		}
		return $result;
	}

}
