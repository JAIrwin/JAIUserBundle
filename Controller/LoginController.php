<?php
// src/JAI\Bundle\UserBundle/Controller/LoginController.php
namespace JAI\Bundle\UserBundle\Controller;

use JAI\Bundle\UserBundle\Form\Security\LoginType;
use JAI\Bundle\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class LoginController extends Controller
{
	public function loginAction(Request $request)
	{
		// Check if there's a message
		$message = $request->query->get('message');
		switch ($message) {
		case "registration_success":
			$notice = "You have successfully registered your account. An email with an activation link has been sent to the address you provided.";
			break;
		case "activation_success":
			$notice = "You have successfully activated your account. You may now login with your email or username and password.";
			break;
		case "reset_success":
			$notice = "You have successfully changed your password.";
			break;
		default:
			$notice = null;
		}
		// build the form
		$user = new User();
		$form = $this->createForm(LoginType::class, $user);
		// Handle the submit (will only happen on POST)
		$authenticationUtils = $this->get('security.authentication_utils');
		// get the login error if there is one
		$error = $authenticationUtils->getLastAuthenticationError();
		// last username entered by the user. Actually not using this
		$lastUsername = $authenticationUtils->getLastUsername();

		return $this->render(
			'JAIUserBundle:security:login.html.twig',
			array(
				'form' => $form->createView(),
				// last username entered by the user. Actually not using this
				'last_username' => $lastUsername,
				'error'         => $error,
				'notice'  => $notice
			)
		);
	}
}
