<?php
// src/JAI\Bundle\UserBundle/Controller/RegistrationController.php
namespace JAI\Bundle\UserBundle\Controller;

use JAI\Bundle\UserBundle\Form\Security\RegisterType;
use JAI\Bundle\UserBundle\Entity\User;
use JAI\Bundle\UserBundle\Entity\Role;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends Controller
{
	public function registerAction(Request $request)
	{
		// build the form
		$user = new User();
		$form = $this->createForm(RegisterType::class, $user);
		// handle the submit (will only happen on POST)
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			return $this->handleRegistration($user);
		}else{
			return $this->render(
				'JAIUserBundle:security:register.html.twig',
				array(
					'form' => $form->createView()
				)
			);
		}
	}

	public function handleRegistration($user)
	{
		// get the user role
		$role = new Role();
		$userRole = $this->getDoctrine()->getRepository('JAIUserBundle:Role')->findOneByRole('ROLE_USER');
		// Encode the password (you could also do this via Doctrine listener)
		$password = $this->get('security.password_encoder')
		->encodePassword($user, $user->getPlainPassword());
		$user->setPassword($password);
		$user->addRole($userRole);
		// Generate activation Token. For now storing it in resetToken. Should have just named it token.
		$token = bin2hex(openssl_random_pseudo_bytes(16));
		$user->setResetToken($token);
		$user->setResetExpires(0);
		// save the User!
		$em = $this->getDoctrine()->getManager();
		$em->persist($user);
		$em->flush();
		// send activation email
		$site_name = $this->getParameter('site_name');
		$from_email = $this->getParameter('from_email');
		$toEmail = $user->getEmail();
		$toName = $user->getUsername();
		$subject = $this->get('translator')->trans('security.activation.subject');
		$link = "/activate?token=".$token;

		$activateEmail = \Swift_Message::newInstance()
		->setSubject($subject.$site_name)
		->setFrom($from_email)
		->setTo($toEmail)
		->setBody(
			$this->renderView(
				'JAIUserBundle:Emails:activate.html.twig',
				array(
					'to_name' => $toName,
					'host_name' => $site_name,
					'link' => $link
				)
			),
			'text/html'
		);
		$this->get('mailer')->send($activateEmail);
		// redirect to login
		return $this->redirectToRoute('jai_user_login', ['message' => 'registration_success']);
	}
}
