<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Entity\Roles;
use App\Entity\User;
use App\Form\RegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="security_registration")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registration(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $em = $this->getDoctrine()->getManager();

        $user = new User();

        $form = $this->createForm(RegisterType::class, $user);
        $form -> handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);

            $em->persist($user);
            $em->flush();

            $organization = new Organization();

            $em = $this->getDoctrine()->getManager();
            $organization->setName('DefaultOrg' . $user->getId());
            $em->persist($organization);
            $em->flush();

            $role = new Roles();

            $user->addOrganizationRole($role);
            $organization->addOrganizationRole($role);

            $role->getRole();
            $role->setRole('Owner');
            $em->persist($role);

            $em->flush();

            return $this->redirectToRoute("security_login");
        }

        return $this->render('security/register.html.twig', [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/login", name="security_login")
     */
    public function login(){
        return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout(){

    }
}
