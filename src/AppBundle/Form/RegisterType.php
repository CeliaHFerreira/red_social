<?php

/**
 * RegisterType, form to register user
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

/**
 * Description of RegisterType
 *
 * @author Celia
 */
class RegisterType extends AbstractType {

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('name', TextType::class, array(
					'label' => 'Nombre',
					'required' => 'required',
					'attr' => array(
						'class' => 'form-name form-control'
					)
				))
				->add('surname', TextType::class, array(
					'label' => 'Apellidos',
					'required' => 'required',
					'attr' => array(
						'class' => 'form-surname form-control'
					)
				))
				->add('username', TextType::class, array(
					'label' => 'Nombre de ususario',
					'required' => 'required',
					'attr' => array(
						'class' => 'form-username form-control username-input'
					)
				))
				->add('email', EmailType::class, array(
					'label' => 'Correo electrónico',
					'required' => 'required',
					'attr' => array(
						'class' => 'form-email form-control'
					)
				))
				->add('password', RepeatedType::class, array(
					'type' => PasswordType::class,
					'first_options' => array(
						'label' => 'Contraseña',
						'attr' => array(
							'class' => 'form-password form-control'
						)),
					'second_options' => array(
						'label' => 'Repetir Contraseña',
						'attr' => array(
							'class' => 'form-password form-control'
						)),
				))
				->add('role', CheckboxType::class, array(
					'label' => 'Marcar si desea tener opciones de composición  ',
					'value' => true,
					'mapped' => false,
					'required' => false
				))
				->add('Registrarse', SubmitType::class, array(
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
			'data_class' => 'BackendBundle\Entity\User'
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'backendbundle_user';
	}

}
