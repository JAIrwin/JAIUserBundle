<?php
// src/JAI/UserBundle/Form/Security/LoginType.php
namespace JAI\UserBundle\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class LoginType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
		->add('username', TextType::class, [
			'label' => 'security.username',
			'attr' => [ 'placeholder' => 'security.usernameoremail.placeholder', 'autofocus' => true ] ])
		->add('password', PasswordType::class, [ 
			'label' => 'security.password',
			'attr' => [ 'placeholder' => 'security.password' ] ])
		->add('remember', CheckboxType::class, [ 'label' => 'security.remember.me', 'mapped' => false ])
		->add('login', SubmitType::class, [ 'label' => 'security.login.button' ])
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'JAI\UserBundle\Entity\User',
			'validation_groups' => array('login'),
		));
	}
}
