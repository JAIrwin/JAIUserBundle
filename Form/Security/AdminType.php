<?php
// src/JAI/UserBundle/Form/Security/AdminType.php
namespace JAI\Bundle\UserBundle\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdminType extends AbstractType
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
		->add('is_active', CheckboxType::class, [ 
			'label' => 'security.active',
			'required' => false ])
		->add('update', SubmitType::class, [ 'label' => 'security.update.user.info' ])
		->add('send_activate', SubmitType::class, [ 'label' => 'security.send.activate' ])
		->add('send_reset', SubmitType::class, [ 'label' => 'security.send.reset' ])
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'JAI\Bundle\UserBundle\Entity\User',
				'validation_groups' => array('admin'),
			));
	}
}
