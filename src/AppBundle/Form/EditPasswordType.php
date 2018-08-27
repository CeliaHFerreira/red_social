<?php
/**
 * EditPasswordType, form to change password
 */
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

/**
 * Description of EditPasswordType
 *
 * @author Celia
 */
class EditPasswordType extends AbstractType {

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('changepassword', TextType::class, array(
					'label' => 'Clave',
					'required' => 'required',
					'empty_data' => ' ',
					'attr' => array(
						'class' => 'form-password form-control'
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
				->add('Cambiar', SubmitType::class, array(
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
		return 'backendbundle_user';
	}

}
