<?php
/**
 * MelodyType, form to publish melody
 */
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


/**
 * Description of MelodyType
 *
 * @author Celia
 */
class MelodyType extends AbstractType {

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('name', TextType::class, array(
					'label' => 'Título',
					'required' => "required",
					'attr' => array(
						'class' => 'form-control'
					)
				))
				->add('melody', FileType::class, array(
					'label' => 'Mifi File (.mid)',
					'required' => true,
					'data_class' => null,
					'attr' => array(
						'class' => 'form-control'
					)
				))
				->add('Crear', SubmitType::class, array(
					"attr" => array(
						"class" => "btn btn-success"
					)
				))
		;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array(
			'data_class' => 'BackendBundle\Entity\Melody'
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'backendbundle_user';
	}

}
