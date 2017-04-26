<?php
// src/JAI\Bundle\UserBundle/Controller/ForgotController.php
namespace JAI\Bundle\UserBundle\Controller;

use JAI\Bundle\UserBundle\Form\Security\ForgotType;
use JAI\Bundle\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ForgotController extends Controller
{
	public function forgotAction(Request $request)
	{
		$info = null;
		// build the form
		$user = new User();
		$form = $this->createForm(ForgotType::class, $user);
		// Handle the submit (will only happen on POST)
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$this->handleLogin($user);
			// Notify User
			$info = "Email Sent!";
			$success = true;
		}else{
			$message = $request->query->get('message');
			if ($message == 'expired_token') {
				$info = "Your password reset token has expired. ";
			} else if ($message == 'invalid_token') {
				$info = "The password reset token is invalid. ";
			}
			$info .= "Enter the email you used to register and we will send instructions for resetting your password.";
			$success = false;
		}

		return $this->render(
			'JAIUserBundle:security:forgot.html.twig',
			array(
				'form' => $form->createView(),
				'success' => $success,
				'info' => $info
			)
		);
	}
	
	public function handleLogin($user)
	{
		// Check if email exists in db
		$requestedEmail = $user->getEmail();
		$foundUser = $this->getDoctrine()
		->getRepository('JAIUserBundle:User')
		->findOneByEmail($requestedEmail);
		if ($foundUser) {
			// Generate Token
			$token = bin2hex(openssl_random_pseudo_bytes(16));
			// Save token & timestamp in user
			$expiretime = time() + 86400;
			$foundUser->setResetToken($token);
			$foundUser->setResetExpires($expiretime);
			$em = $this->getDoctrine()->getManager();
			$em->persist($foundUser);
			$em->flush();
			// Send Email
			$site_name = $this->getParameter('site_name');
			$from_email = $this->getParameter('from_email');
			$toEmail = $foundUser->getEmail();
			$toName = $foundUser->getUsername();
			$subject = $this->get('translator')->trans('security.password.subject');
			$link = "/reset?token=".$token;
	
			$resetEmail = \Swift_Message::newInstance()
			->setSubject($subject.$site_name)
			->setFrom($from_email)
			->setTo($toEmail)
			->setBody(
				$this->renderView(
					'JAIUserBundle:Emails:reset.html.twig',
					array(
						'to_name' => $toName,
						'host_name' => $site_name,
						'link' => $link
					)
				),
				'text/html'
			);
			$this->get('mailer')->send($resetEmail);
		}
	}
}
