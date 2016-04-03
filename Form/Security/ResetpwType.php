<?php
// src/JAI/UserBundle/Form/Security/ResetpwType.php
namespace JAI\Bundle\UserBundle\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ResetpwType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
		->add('plainPassword', RepeatedType::class, array(
				'type' => PasswordType::class,
				'invalid_message' => 'security.mismatched.passwords',
				'first_options'  => array(
					'label' => 'security.change.password',
					'attr' => [ 'placeholder' => 'security.change.password']
				),
				'second_options' => array(
					'label' => 'security.change.repeat.password',
					'attr' => [ 'placeholder' => 'security.change.repeat.password']
				),
			)
		)
		->add('reset', SubmitType::class, [ 'label' => 'security.reset.password' ])
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'JAI\Bundle\UserBundle\Entity\User',
				'validation_groups' => array('resetpw'),
			));
	}
}
