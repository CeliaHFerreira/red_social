<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


/**
 * Description of ForgetPasswordType
 *
 * @author Celia
 */
class ForgetPasswordType extends AbstractType {

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('email', TextType::class, array(
					'label' => 'Email asociado a la cuenta de usuario',
					'required' => 'required',
					'attr' => array(
						'class' => 'form-forgotten-password form-control'
					)
				))
				->add('changepassword', TextType::class, array(
					'label' => 'Clave',
					'required' => 'required',
					'empty_data' => ' ',
					'attr' => array(
						'class' => 'form-forgotten-password form-control'
					)
				))
				->add('password', PasswordType::class, array(
					'label' => 'Contraseña',
					'required' => 'required',
					'attr' => array(
						'class' => 'form-forgotten-password form-control'
					)
				))
				->add('Aceptar', SubmitType::class, array(
					"attr" => array(
						"class" => "form-submit  btn btn-success"
					)
				))
		;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array(
            'error_bubbling' => true
        ));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'forgotten_password_form';
	}

}
