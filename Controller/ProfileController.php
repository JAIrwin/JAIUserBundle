<?php
// src/JAI\Bundle\UserBundle/Controller/ProfileController.php
namespace JAI\Bundle\UserBundle\Controller;

use JAI\Bundle\UserBundle\Form\Security\ProfileType;
use JAI\Bundle\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends Controller
{
	public function profileAction(Request $request)
	{
		$user = $this->get('security.token_storage')->getToken()->getUser();
		$oldpassword = $user->getPassword();
		$notice = null;

		// build the form
		$form = $this->createForm(ProfileType::class, $user);
		// handle the submit (will only happen on POST)
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			// Encode the new password if it's changed
			$plainPassword = $user->getPlainPassword();
			if ($plainPassword) {
				$password = $this->get('security.password_encoder')
				->encodePassword($user, $user->getPlainPassword());
				$user->setPassword($password);
			}else{
				$user->setPassword($oldpassword);
			}
			// Clear the reset token (if it was ever set)
			$user->setResetToken("");
			$user->setResetExpires(0);
			// save the User!
			$em = $this->getDoctrine()->getManager();
			$em->persist($user);
			$em->flush();

			$notice = "Your Profile has been updated.";
		}

		return $this->render(
			'JAIUserBundle:security:profile.html.twig',
			array(
				'form' => $form->createView(),
				'notice' => $notice
			)
		);
	}
}
