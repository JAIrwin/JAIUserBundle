<?php
// src/JAI/UserBundle/Form/Security/ProfileType.php
namespace JAI\Bundle\UserBundle\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ProfileType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
		->add('email', EmailType::class, [
			'label' => 'Email',
			'attr' => [ 'placeholder' => 'email.placeholder' ] ])
		->add('username', TextType::class, [
			'label' => 'security.username',
			'attr' => [ 'placeholder' => 'security.username.placeholder' ] ])
		->add('plainPassword', RepeatedType::class, array(
				'type' => PasswordType::class,
				'invalid_message' => 'security.mismatched.passwords',
				'first_options'  => array(
					'label' => 'security.change.password',
					'attr' => [ 'placeholder' => 'security.newpw.placeholder']
				),
				'second_options' => array(
					'label' => 'security.change.repeat.password',
					'attr' => [ 'placeholder' => 'security.newrepeat.placeholder']
				),
			)
		)
		->add('oldPassword', PasswordType::class, [
			'label' => 'security.password',
			'attr' => [ 'placeholder' => 'security.current.password'] ]
		)
		->add('update', SubmitType::class, [ 'label' => 'Update' ])
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'JAI\Bundle\UserBundle\Entity\User',
				'validation_groups' => array('profile'),
			));
	}
}
