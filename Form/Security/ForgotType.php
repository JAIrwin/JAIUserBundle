<?php
// src/JAI/UserBundle/Form/Security/ForgotType.php
namespace JAI\Bundle\UserBundle\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ForgotType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
		->add('email', EmailType::class, [
			'label' => 'Email',
			'attr' => [ 'placeholder' => 'email.placeholder', 'autofocus' => true ] ])
		->add('submit', SubmitType::class, [ 'label' => 'Submit' ])
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'JAI\Bundle\UserBundle\Entity\User',
			'validation_groups' => array('forgot'),
		));
	}
}
