<?php
// src/JAI/UserBundle/Form/Security/RegisterType.php
namespace JAI\UserBundle\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;

class RegisterType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
		->add('email', EmailType::class, [
			'label' => 'Email',
			'attr' => [ 'placeholder' => 'email.placeholder', 'autofocus' => true ] ])
		->add('username', TextType::class, [ 'label' => 'security.username', 'attr' => [ 'placeholder' => 'security.username.placeholder' ] ])
		->add('plainPassword', RepeatedType::class, array(
				'type' => PasswordType::class,
				'invalid_message' => 'security.mismatched.passwords',
				'first_options'  => array('label' => 'security.password', 'attr' => [ 'placeholder' => 'security.password' ] ),
				'second_options' => array('label' => 'security.repeat.password', 'attr' => [ 'placeholder' => 'security.repeat.password' ]),
			)
		)
		->add('recaptcha', EWZRecaptchaType::class, [ 'label' => false ])
		->add('register', SubmitType::class, [ 'label' => 'security.register.button' ])
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'JAI\UserBundle\Entity\User',
				'validation_groups' => array('registration','Default'),
			));
	}
}
