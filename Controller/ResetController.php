<?php
// src/JAI\UserBundle/Controller/ResetController.php
namespace JAI\UserBundle\Controller;

use JAI\UserBundle\Form\Security\ResetpwType;
use JAI\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ResetController extends Controller
{
	public function resetAction(Request $request)
	{
		// Check if there is a token
		$token = $request->query->get('token');
		// Check if there's a user with this token
		$foundUser = $this->getDoctrine()
		->getRepository('JAIUserBundle:User')
		->findOneByResetToken($token);
		if ($foundUser) {
			// Check if token is expired
			$expiretime = $foundUser->getResetExpires();
			$currtime = time();
			if ($currtime <= $expiretime) {
				// Display the change password form
				$form = $this->createForm(ResetpwType::class, $foundUser);
				$form->handleRequest($request);
				if ($form->isSubmitted() && $form->isValid()) {
					// Encode the new password
					$plainPassword = $foundUser->getPlainPassword();
					if ($plainPassword) {
						$password = $this->get('security.password_encoder')
						->encodePassword($foundUser, $foundUser->getPlainPassword());
						$foundUser->setPassword($password);
						$foundUser->setResetToken("");
						$foundUser->setResetExpires(0);
					}
					// save the User!
					$em = $this->getDoctrine()->getManager();
					$em->persist($foundUser);
					$em->flush();

					// redirect to login
					return $this->redirectToRoute('jai_user_login', ['message' => 'reset_success']);
				}

				return $this->render(
					'JAIUserBundle:security:reset.html.twig',
					array(
						'form' => $form->createView()
					)
				);
			} else {
				// Expired token - redirect to lost password with a helpful message
				return $this->redirectToRoute('jai_user_forgot', ['message' => 'expired_token']);
			}
		} else {
			// Missing or invalid token - redirect to home
			return $this->redirectToRoute('pageHome');
		}
	}
}
