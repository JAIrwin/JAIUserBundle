<?php
// src/JAI\UserBundle/Controller/ActivateController.php
namespace JAI\UserBundle\Controller;

use JAI\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ActivateController extends Controller
{
	public function activateAction(Request $request)
	{
		// Check if there is a token
		$token = $request->query->get('token');
		// Check if there's a user with this token
		$foundUser = $this->getDoctrine()
		->getRepository('JAIUserBundle:User')
		->findOneByResetToken($token);
		if ($foundUser) {
			// activate the user
			$foundUser->setIsActive(true);
			$foundUser->setResetToken("");
			$foundUser->setResetExpires(0);
			// save the User!
			$em = $this->getDoctrine()->getManager();
			$em->persist($foundUser);
			$em->flush();

			// redirect to login
			return $this->redirectToRoute('jai_user_login', ['message' => 'activation_success']);
		} else {
			// Missing or invalid token - redirect to home
			return $this->redirectToRoute('homepage');
		}
	}
}
