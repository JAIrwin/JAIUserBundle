<?php
// src/JAI\Bundle\UserBundle/Controller/UseradminController.php
namespace JAI\Bundle\UserBundle\Controller;

use JAI\Bundle\UserBundle\Form\Security\AdminType;
use JAI\Bundle\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UseradminController extends Controller
{
	public function useradminAction(Request $request)
	{
		$role_hierarchy = $this->getParameter('security.role_hierarchy.roles');
		$notice = null;

		$all_users = $this->getDoctrine()
		->getRepository('JAIUserBundle:User')
		->findAll();
		if (!$all_users) {
			throw $this->createNotFoundException(
				'No users found'
			);
		}
		$user = new User;
		$userform = $this->createForm(AdminType::class, $user);

		return $this->render(
			'JAIUserBundle:security:useradmin.html.twig',
			array(
				'roles'				=> $this->retrieveRoles(),
				'role_hierarchy'	=> $role_hierarchy,
				'users'				=> $all_users,
				'userform'			=> $userform->createView(),
				'notice'			=> $notice,
				'this_user'			=> $user,
			)
		);
	}

	public function usereditAction($userid, Request $request)
	{
		$role_hierarchy = $this->getParameter('security.role_hierarchy.roles');
		$notice = null;

		$all_users = $this->getDoctrine()
		->getRepository('JAIUserBundle:User')
		->findAll();
		if (!$all_users) {
			throw $this->createNotFoundException(
				'No users found'
			);
		}
		$selected_user = $this->getDoctrine()
		->getRepository('JAIUserBundle:User')
		->findOneById($userid);
		$userform = $this->createForm(AdminType::class, $selected_user);
		$userform->handleRequest($request);
		$oldpassword = $selected_user->getPassword();
		$useremail = $selected_user->getEmail();
		if ($userform->isSubmitted() && $userform->isValid()) {
			if ($userform->get('update')->isClicked()) {
				// clear the reset tokein if it happens to have been set
				$selected_user->setResetToken("");
				$selected_user->setResetExpires(0);
				// save the User
				$em = $this->getDoctrine()->getManager();
				$em->persist($selected_user);
				$em->flush();
				$notice = "The Profile has been updated.";
			}
			if ($userform->get('send_activate')->isClicked()) {
				$token = bin2hex(openssl_random_pseudo_bytes(16));
				$selected_user->setResetToken($token);
				$selected_user->setResetExpires(0);
				// save the User!
				$em = $this->getDoctrine()->getManager();
				$em->persist($selected_user);
				$em->flush();
				$site_name = $this->getParameter('site_name');
				$from_email = $this->getParameter('from_email');
				$toEmail = $selected_user->getEmail();
				$toName = $selected_user->getUsername();
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
				$notice = "The Activation email has been sent to $useremail.";
			}
			if ($userform->get('send_reset')->isClicked()) {
				// Generate Token
				$token = bin2hex(openssl_random_pseudo_bytes(16));
				// 5ave token & timestamp in user
				$expiretime = time() + 86400;
				$selected_user->setResetToken($token);
				$selected_user->setResetExpires($expiretime);
				$em = $this->getDoctrine()->getManager();
				$em->persist($selected_user);
				$em->flush();
				// Send Email
				$site_name = $this->getParameter('site_name');
				$from_email = $this->getParameter('from_email');
				$toEmail = $selected_user->getEmail();
				$toName = $selected_user->getUsername();
				$subject = $this->get('translator')->trans('security.password.subject');
				$link = "/activate?token=".$token;

				$resetEmail = \Swift_Message::newInstance()
				->setSubject($subject.$site_name)
				->setFrom($from_email)
				->setTo($toEmail)
				->setBody(
					$this->renderView(
						'JAIUserBundle:Emails:reset.html.twig',
						array(
							'to_name'	=> $toName,
							'host_name' => $site_name,
							'link'		=> $link
						)
					),
					'text/html'
				);
				$this->get('mailer')->send($resetEmail);
				$notice = "The Reset Password email has been sent to $useremail.";
			}
		}

		return $this->render(
			'JAIUserBundle:security:useradmin.html.twig',
			array(
				'roles'				=> $this->retrieveRoles(),
				'role_hierarchy'	=> $role_hierarchy,
				'users'				=> $all_users,
				'userform'			=> $userform->createView(),
				'notice'			=> $notice,
				'this_user'			=> $selected_user
			)
		);
	}

	public function removeRoleAction($userid, $role, Request $request)
	{
		$role_hierarchy = $this->getParameter('security.role_hierarchy.roles');
		$notice = null;

		$all_users = $this->getDoctrine()
		->getRepository('JAIUserBundle:User')
		->findAll();
		if (!$all_users) {
			throw $this->createNotFoundException(
				'No users found'
			);
		}
		$selected_user = $this->getDoctrine()
		->getRepository('JAIUserBundle:User')
		->findOneById($userid);
		$selected_role = $this->getDoctrine()
		->getRepository('JAIUserBundle:Role')
		->findOneByRole($role);

		$selected_user->removeRole($selected_role);
		$em = $this->getDoctrine()->getManager();
		$em->persist($selected_user);
		$em->flush();

		$userform = $this->createForm(AdminType::class, $selected_user);
		return $this->redirectToRoute(
			'jai_user_admin_edit',
			array(
				'userid'			=> $userid,
				'roles'				=> $this->retrieveRoles(),
				'role_hierarchy'	=> $role_hierarchy,
				'users'				=> $all_users,
				'userform'			=> $userform->createView(),
				'notice'			=> $notice,
				'this_user'			=> $selected_user
			)
		);
	}

	public function addRoleAction($userid, $role, Request $request)
	{
		$role_hierarchy = $this->getParameter('security.role_hierarchy.roles');
		$notice = null;

		$all_users = $this->getDoctrine()
		->getRepository('JAIUserBundle:User')
		->findAll();
		if (!$all_users) {
			throw $this->createNotFoundException(
				'No users found'
			);
		}
		$selected_user = $this->getDoctrine()
		->getRepository('JAIUserBundle:User')
		->findOneById($userid);
		$selected_role = $this->getDoctrine()
		->getRepository('JAIUserBundle:Role')
		->findOneByRole($role);

		$selected_user->addRole($selected_role);
		$em = $this->getDoctrine()->getManager();
		$em->persist($selected_user);
		$em->flush();

		$userform = $this->createForm(AdminType::class, $selected_user);
		return $this->redirectToRoute(
			'jai_user_admin_edit',
			array(
				'userid'			=> $userid,
				'roles'				=> $this->retrieveRoles(),
				'role_hierarchy'	=> $role_hierarchy,
				'users'				=> $all_users,
				'userform'			=> $userform->createView(),
				'notice'			=> $notice,
				'this_user'			=> $selected_user
			)
		);
	}


	private function retrieveRoles() {
		$all_roles = $this->getDoctrine()
		->getRepository('JAIUserBundle:Role')
		->findAll();
		return $all_roles;
	}
}
