<?php
/**
  * Description for ContactController, package used to manage contact
  *
  */
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ContactType;
use AppBundle\Form\ActivationType;

/**
 * Description of ContactController
 *
 * @author Celia
 */
class ContactController extends Controller {

	/**
	 * FORMULARIO DE CONTACTO
	 * 
	 * @param Request $request necessary
	 * @return view ContactForm
	 */
	public function contactAction(Request $request) {
		$user = $this->getUser();
		if ($user != null) {
			if ($user->getActive() != "true") {
				return $this->redirect($this->generateUrl('activate'));
			}
		}


		$form = $this->createForm(ContactType::class);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$data = $form->getData();

			//dump($data);

			$message = \Swift_Message::newInstance()
					->setSubject($form->get("subject")->getData())
					->setFrom($form->get("from")->getData())
					->setTo('celiahrrferreira@gmail.com')
					->setBody(
					$form->get("message")->getData(), 'text/plain'
					)
			;
			$this->get('mailer')->send($message);
		}

		return $this->render('@App/Contact/contact.html.twig', array(
					'my_form' => $form->createView()
		));
	}

	/**
	 * ACTIVAR CUENTA
	 * 
	 * @param Request $request necessary
	 * @return view ActivateForm
	 */
	public function activateAction(Request $request) {
		$user = $this->getUser();
		if ($user->getActive() == "true") {
			return $this->redirect('home');
		}

		$form = $this->createForm(ActivationType::class);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$user = $this->getUser();
			$clave = $user->getActive();
			$clave_introducida = $form->get("activate")->getData();
			$data = $form->getData();


			if ($clave == $clave_introducida) {
				$user->setActive("true");
				//volcamos en doctrine todos los datos para que persistan y se puedan grabar en la db
				$em->persist($user);
				//volcamos a la bd
				$flush = $em->flush();

				if ($flush == null) {
					$this->addFlash(
							'notice', 'Cuenta activada correctamente.'
					);
					$message = \Swift_Message::newInstance()
							->setSubject("Activacion de cuenta en MeloDJ")
							->setFrom("rasptfg@gmail.com")
							->setTo("celiahrrferreira@gmail.com")
							->setBody(
							'<html>' .
							'<h1>¡Hola!</h1>' .
							' <body>' .
							'<p>Su cuenta ha sido activada correctamente</p>' .
							'</html>', 'text/html'
							)
					;
					$this->get('mailer')->send($message);
					return $this->redirect("home");
				} else {
					$this->addFlash(
							'error', 'Error. No se ha realizado la activacion.'
					);
				}
			}
		}

		return $this->render('@App/User/activate.html.twig', array(
					"form" => $form->createView(),
					"correo" => $user->getEmail()
		));
	}

}
