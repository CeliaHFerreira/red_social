<?php

/*
 * ContactType, form to send contact message
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Description of ContactType
 *
 * @author Celia
 */
class ContactType extends AbstractType {

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
				->add('from', EmailType::class, array(
					'label' => 'Tu correo electrónico',
					'required' => 'required',
					'attr' => array(
						'class' => 'form-email form-control'
					)
				))
				->add('subject', TextType::class, array(
					'label' => 'Asunto',
					'required' => false,
					'attr' => array(
						'class' => 'form-description form-control'
					)
				))
				->add('message', TextareaType::class, array(
					'label' => 'Texto del mensaje',
					'required' => false,
					'attr' => array(
						'class' => 'form-description form-control'
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
	public function getName()
    {
        return 'contact_form';
    }

}
