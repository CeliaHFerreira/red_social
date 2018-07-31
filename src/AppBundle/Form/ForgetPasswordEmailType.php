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
 * Description of ForgetPasswordEmailType
 *
 * @author Celia
 */
class ForgetPasswordEmailType extends AbstractType {

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('email', TextType::class, array(
					'label' => 'Email',
					'required' => 'required',
					'attr' => array(
						'class' => 'form-email form-control'
					)
				))
				->add('Enviar', SubmitType::class, array(
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
		return 'email_form';
	}

}
