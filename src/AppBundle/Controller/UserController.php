<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;
//Bundles
use BackendBundle\Entity\User;
use BackendBundle\Entity\Melody;
use BackendBundle\Entity\Following;
use BackendBundle\Entity\Notification;
use BackendBundle\Entity\PrivateMessage;
use BackendBundle\Entity\Like;
use AppBundle\Form\RegisterType;
use AppBundle\Form\UserType;
use AppBundle\Form\ActivationType;
use AppBundle\Form\EditPasswordType;
use AppBundle\Form\ForgetPasswordEmailType;
use AppBundle\Form\ForgetPasswordType;
use AppBundle\Controller\MelodyController;

/**
 * Description of UserController
 *
 * @author Celia
 */
class UserController extends Controller {

	private $session;

	/**
	 * CONSTRUCTOR
	 */
	public function __construct() {
		$this->session = new Session();
	}

	/**
	 * LOGIN DE USUARIOS
	 */
	public function loginAction(Request $request) {
		//Si el método nos devuelve un objeto nos redirijirá a la home porque el usuario ya está logueado
		if (is_object($this->getUser())) {
			$user = $this->getUser();
			if ($user->getActive() == "true") {
				return $this->redirect('home');
			} else {
				$form = $this->createForm(ActivationType::class);
				return $this->redirect($this->generateUrl('activate'));
			}
		}

		$authenticationUtils = $this->get('security.authentication_utils');
		$error = $authenticationUtils->getLastAuthenticationError();
		$lastUsername = $authenticationUtils->getLastUsername();

		return $this->render('@App/User/login.html.twig', array(
					'last_username' => $lastUsername,
					'error' => $error
		));
	}

	/**
	 * REGISTRO DE USUARIOS
	 */
	public function registerAction(Request $request) {

		if (is_object($this->getUser())) {
			$user = $this->getUser();
			if ($user->getActive() == "true") {
				return $this->redirect('home');
			} else {
				$form = $this->createForm(ActivationType::class);
				return $this->redirect($this->generateUrl('activate'));
			}
		}

		$user = new User();
		$form = $this->createForm(RegisterType::class, $user);

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();

			$clave = $this->generatePassword(8);
			$message = \Swift_Message::newInstance()
					->setSubject("Registro en MeloDJ")
					->setFrom("rasptfg@gmail.com")
					->setTo($form->get("email")->getData())
					->setBody(
					'<html>' .
					'<h1>¡Hola!</h1>' .
					' <body>' .
					'<p>Gracias por registrarte en MeloDJ, para finalizar con el registro, por favor introduce la siguiente clave en el enlace</p>' .
					$clave .
					'</html>', 'text/html'
					)
			;
			$this->get('mailer')->send($message);
			$email = $form->get("email")->getData();
			$query = $em->createQuery('SELECT u FROM '
							. 'BackendBundle:User u '
							. 'WHERE u.email = :email '
							. 'OR u.username = :username')
					->setParameter('email', $form->get("email")->getData())
					->setParameter('username', $form->get("username")->getData());

			$new_user = $query->getResult();

			if (count($new_user) == 0) {
				//codificamos la contraseña
				$factory = $this->get("security.encoder_factory");
				$encoder = $factory->getEncoder($user);

				$password = $encoder->encodePassword($form->get("password")->getData(), $user->getSalt());

				$user->setPassword($password);
				if(($form->get("role")->getData()) == "true"){
					$user->setRole("ROLE_COMPOSER");
				}
				else{
					$user->setRole("ROLE_USER");
				}
				
				$user->setImage(null);
				$user->setActive($clave);
				//volcamos en doctrine todos los datos para que persistan y se puedan grabar en la db
				$em->persist($user);
				//volcamos a la bd
				$flush = $em->flush();

				if ($flush == null) {
					$this->addFlash(
							'notice', 'Registro realizado correctamente, por favor active su cuenta.'
					);

					return $this->redirect("login");
				} else {
					$this->addFlash(
							'error', 'Error. No se ha realizado el registro en la base de datos.'
					);
				}
			} else {
				$this->addFlash(
						'error', 'Error. Usuario ya existente en la base de datos'
				);
			}
		}



		return $this->render('@App/User/register.html.twig', array(
					"form" => $form->createView()
		));
	}

	//Encontrar nombre de usuario en lista de usuarios
	public function UsernameTestAction(Request $request) {
		$username = $request->get("username");

		$em = $this->getDoctrine()->getManager();
		$user_repo = $em->getRepository("BackendBundle:User");

		$user_isset = $user_repo->findOneBy(array("username" => $username));

		$result = "used"; //ya esta y no se puede utilizar
		if (count($user_isset) >= 1 && is_object($user_isset)) {
			$result = "used";
		} else {
			$result = "unused";
		}

		return new Response($result);
	}

	/**
	 * OVLIDO DE CONTRASEÑA
	 */
	public function forgetPasswordAction(Request $request) {
		if (is_object($this->getUser())) {
			$user = $this->getUser();
			if ($user->getActive() == "true") {
				return $this->redirect('home');
			} else {
				$form = $this->createForm(ActivationType::class);
				return $this->redirect($this->generateUrl('activate'));
			}
		}

		$form = $this->createForm(ForgetPasswordType::class);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();

			$email = $form->get("email")->getData();
			$user_repo = $em->getRepository("BackendBundle:User");
			$user = $user_repo->findOneBy(array("email" => $email));
			if (!$user) {
				return $this->render('@App/User/error_user_forget_password.html.twig');
			}
			$clave_cambiar = $user->getChangepassword();
			$clave_cambiar_introducida = $form->get("changepassword")->getData();
//			dump($clave_cambiar);
//			dump($clave_cambiar_introducida);

			if ($clave_cambiar == $clave_cambiar_introducida) {
				$factory = $this->get("security.encoder_factory");
				$encoder = $factory->getEncoder($user);

				$password = $encoder->encodePassword($form->get("password")->getData(), $user->getSalt());

				$user->setPassword($password);

				//volcamos en doctrine todos los datos para que persistan y se puedan grabar en la db
				$em->persist($user);
				//volcamos a la bd
				$flush = $em->flush();

				if ($flush == null) {
					$this->addFlash(
							'notice', 'Cambio de contraseña realizado correctamente'
					);
				} else {
					$this->addFlash(
							'error', 'Error. No se ha realizado el cambio de contraseña'
					);
				}
			} else {
				$this->addFlash(
						'error', 'Error. No se ha introducido la clave correcta para el cambio de contraseña'
				);
			}
			return $this->redirect("login");
		}
		return $this->render('@App/User/forget_password.html.twig', array(
					"form" => $form->createView()
		));
	}

	public function keyPasswordAction(Request $request) {
		if (is_object($this->getUser())) {
			$user = $this->getUser();
			if ($user->getActive() == "true") {
				return $this->redirect('home');
			} else {
				$form_activate = $this->createForm(ActivationType::class);
				return $this->redirect($this->generateUrl('activate'));
			}
		}

		$form = $this->createForm(ForgetPasswordEmailType::class);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();

			$email = $form->get("email")->getData();
			$clave = $this->generatePassword(8);

			$user_repo = $em->getRepository("BackendBundle:User");
			$user = $user_repo->findOneBy(array("email" => $email));
			if (!$user) {
				return $this->render('@App/User/error_user_forget_password.html.twig');
			}
			$user->setChangepassword($clave);
			$em->persist($user);
			$flush = $em->flush();

			if ($flush == null) {
				$this->addFlash(
						'notice', 'Mensaje recuperación contraseña enviado.'
				);
				$message = \Swift_Message::newInstance()
						->setSubject("Olvido de contraseña")
						->setFrom("rasptfg@gmail.com")
						->setTo($email)
						->setBody(
						'<html>' .
						'<h2>Clave olvido de contraseña</h2>' .
						' <body>' .
						'<p>Ha seleccionado el olvido de contraseña, para ello se le ha generado la siguiente clave que deberá introducir en el formulario:</p>' .
						$clave .
						'</html>', 'text/html'
						)
				;
				$this->get('mailer')->send($message);
				$next_form = $this->createForm(ForgetPasswordType::class);
				return $this->redirect($this->generateUrl('forget_password'));
			} else {
				$this->addFlash(
						'error', 'Error. No se ha realizado el envío.'
				);
			}
		}
		return $this->render('@App/User/forget_password_email.html.twig', array(
					"form" => $form->createView()
		));
	}

	/**
	 * VER DATOS USUARIO
	 */
	public function seeUserAction(Request $request) {
		$user = $this->getUser();
		if ($user->getActive() != "true") {
			return $this->redirect($this->generateUrl('activate'));
		}

		return $this->render('@App/User/user_data.html.twig', array(
					'user' => $user
		));
	}

	/**
	 * MODIFICAR DATOS USUARIO
	 */
	public function editUserAction(Request $request) {
		$user = $this->getUser();
		if ($user->getActive() != "true") {
			return $this->redirect($this->generateUrl('activate'));
		}
		$user_image = $user->getImage();
		$form = $this->createForm(UserType::class, $user);

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();

			$query = $em->createQuery('SELECT u FROM '
							. 'BackendBundle:User u '
							. 'WHERE u.email = :email '
							. 'OR u.username = :username')
					->setParameter('email', $form->get("email")->getData())
					->setParameter('username', $form->get("username")->getData());

			$user_for_edit = $query->getResult();

			if (count($user_for_edit) == 0 || ($user->getEmail() == $user_for_edit[0]->getEmail() && $user->getUsername() == $user_for_edit[0]->getUsername())) {

				//subir imagen
				$file = $form["image"]->getData();

				if (!empty($file) && $file != null) {
					$ext = $file->guessExtension();
					if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif') {
						$file_name = $user->getId() . time() . '.' . $ext; //nombre fichero
						$file->move("uploads/users", $file_name);

						$user->setImage($file_name);
					}
				} else {
					$user->setImage($user_image);
				}

				//volcamos en doctrine todos los datos para que persistan y se puedan grabar en la db
				$em->persist($user);
				//volcamos a la bd
				$flush = $em->flush();

				if ($flush == null) {
					$this->addFlash(
							'notice', 'Cambios realizados correctamente'
					);
				} else {
					$this->addFlash(
							'error', 'Error. No se ha realizado el cambio de datos en la base de datos'
					);
				}
			} else {
				$this->addFlash(
						'error', 'Error. Usuario ya existente en la base de datos'
				);
			}


			return $this->redirect("user_data");
		}
		return $this->render('@App/User/edit_user.html.twig', array(
					"form" => $form->createView()
		));
	}

	/**
	 * MODIFICAR CONTRASEÑA USUARIO
	 */
	public function editPasswordAction(Request $request) {
		$user = $this->getUser();
		if ($user->getActive() != "true") {
			return $this->redirect($this->generateUrl('activate'));
		}
		$form = $this->createForm(EditPasswordType::class);

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();

			$user_for_edit = $this->getUser();
			$clave_cambiar = $user->getChangepassword();
			$clave_cambiar_introducida = $form->get("changepassword")->getData();
//			dump($clave_cambiar);
//			dump($clave_cambiar_introducida);

			if ($clave_cambiar == $clave_cambiar_introducida) {
				$factory = $this->get("security.encoder_factory");
				$encoder = $factory->getEncoder($user_for_edit);

				$password = $encoder->encodePassword($form->get("password")->getData(), $user_for_edit->getSalt());

				$user_for_edit->setPassword($password);

				//volcamos en doctrine todos los datos para que persistan y se puedan grabar en la db
				$em->persist($user);
				//volcamos a la bd
				$flush = $em->flush();

				if ($flush == null) {
					$this->addFlash(
							'notice', 'Cambio de contraseña realizado correctamente'
					);
				} else {
					$this->addFlash(
							'error', 'Error. No se ha realizado el cambio de contraseña'
					);
				}
			} else {
				$this->addFlash(
						'error', 'Error. No se ha introducido la clave correcta para el cambio de contraseña'
				);
			}


			return $this->redirect("user_data");
		}
		return $this->render('@App/User/edit_password.html.twig', array(
					"form" => $form->createView()
		));
	}

	public function sendKeyAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$user = $this->getUser();
		$email = $user->getEmail();

		$clave = $this->generatePassword(8);
		$user->setChangepassword($clave);
		$em->persist($user);
		$flush = $em->flush();

		if ($flush == null) {
			$message = \Swift_Message::newInstance()
					->setSubject("Cambio de contraseña")
					->setFrom("rasptfg@gmail.com")
					->setTo($email)
					->setBody(
					'<html>' .
					'<h2>Clave cambio de contraseña</h2>' .
					' <body>' .
					'<p>Ha seleccionado el cambio de contraseña, para ello se le ha generado la siguiente clave que deberá introducir en el formulario:</p>' .
					$clave .
					'</html>', 'text/html'
					)
			;
			$this->get('mailer')->send($message);
			$status = $user->getChangepassword();
		} else {
			$status = 'No se ha enviado el correo con la clave';
		}

		return new Response($status);
	}

	/**
	 * BORRAR CUENTA
	 */
	public function removeAccountAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$user = $this->getUser();
		$id = $user->getId();
		$melody_repo = $em->getRepository('BackendBundle:Melody');
		$melodies = $melody_repo->findBy(array(
			'user' => $id
		));
		foreach ($melodies as $melody) {
			MelodyController::removeMelodyAction($request, $melody);
		}
		$following_repo = $em->getRepository('BackendBundle:Following');
		$following = $following_repo->findBy(array(
			'user' => $id
		));
		foreach ($following as $follow) {
			$em->remove($follow);
			$flush = $em->flush();
		}
		$followed = $following_repo->findBy(array(
			'followed' => $id
		));
		foreach ($followed as $follow) {
			$em->remove($follow);
			$flush = $em->flush();
		}
		$assesments_repo = $em->getRepository('BackendBundle:Assesment');
		$assesments = $assesments_repo->findBy(array(
			'user' => $id
		));
		foreach ($assesments as $assesment) {
			$em->remove($assesment);
			$flush = $em->flush();
		}
		$likes_repo = $em->getRepository('BackendBundle:Like');
		$likes = $likes_repo->findBy(array(
			'user' => $id
		));
		foreach ($likes as $like) {
			$em->remove($like);
			$flush = $em->flush();
		}
		$notifitions_repo = $em->getRepository('BackendBundle:Notification');
		$notifications = $notifitions_repo->findBy(array(
			'user' => $id
		));
		foreach ($notifications as $notification) {
			$em->remove($notification);
			$flush = $em->flush();
		}
		$notifications_others = $notifitions_repo->findBy(array(
			'typeId' => $id
		));
		foreach ($notifications_others as $notification_other) {
			$em->remove($notification_other);
			$flush = $em->flush();
		}
		$privateMessages_repo = $em->getRepository('BackendBundle:PrivateMessage');
		$privateMessagesReceiver = $privateMessages_repo->findBy(array(
			'receiver' => $id
		));
		foreach ($privateMessagesReceiver as $receiver) {
			$em->remove($receiver);
			$flush = $em->flush();
		}
		$privateMessagesEmitter = $privateMessages_repo->findBy(array(
			'emitter' => $id
		));
		foreach ($privateMessagesEmitter as $emitter) {
			$em->remove($emitter);
			$flush = $em->flush();
		}
		$scores_repo = $em->getRepository('BackendBundle:Score');
		$scores = $scores_repo->findBy(array(
			'user' => $id
		));
		foreach ($scores as $score) {
			$em->remove($score);
			$flush = $em->flush();
		}
		
		$em->remove($user);
		$flush = $em->flush();

		if ($flush == null) {
			$status = "cuenta borrada";
//			$this->addFlash(
//					'notice', 'Se ha eliminado la cuenta de usuario'
//			);
			return $this->render('@App/User/delete_user.html.twig');
		} else {
			$status = "cuenta no borrada";
//			$this->addFlash(
//					'notice', 'No se ha eliminado la cuenta de usuario'
//			);
//			return $this->render('home');
		}
		return new Response ($status);
	}

	/**
	 * GENERACIÓN CLAVE ACTIVACIÓN, CONTRASEÑA
	 */
	public function generatePassword($long) {
		$cadena_base = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$cadena_base .= '0123456789';

		$password = '';
		$limite = strlen($cadena_base) - 1;

		for ($i = 0; $i < $long; $i++)
			$password .= $cadena_base[rand(0, $limite)];

		return $password;
	}

}
