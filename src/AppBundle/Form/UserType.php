<?php
/*
 * UserType, form to change user data
 */
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;


/**
 * Description of UserType
 *
 * @author Celia
 */
class UserType extends AbstractType {

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
						'class' => 'form-username form-control'
					)
				))
				->add('email', EmailType::class, array(
					'label' => 'Correo electrónico',
					'required' => 'required',
					'attr' => array(
						'class' => 'form-email form-control'
					)
				))
				->add('description', TextareaType::class, array(
					'label' => 'Descripción',
					'required' => false,
					'attr' => array(
						'class' => 'form-description form-control'
					)
				))
				->add('image', FileType::class, array(
					'label' => 'Foto',
					'required' => false,
					'data_class' => null,
					'attr' => array(
						'class' => 'form-image form-control'
					)
				))
				->add('Guardar datos', SubmitType::class, array(
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
