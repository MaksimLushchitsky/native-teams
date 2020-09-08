<?php

declare(strict_types = 1);
namespace App\Controller;

use App\Entity\Agreement;
use App\Entity\Organization;
use App\Entity\Roles;
use App\Entity\User;
use App\Form\AgreementType;
use App\Form\EditRolesSettingsProfileType;
use App\Form\EditUserSettingsProfileType;
use App\Form\EditUserType;
use App\Form\InviteType;
use App\Repository\RolesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SettingsController extends AbstractController
{
    /**
     * @Route("/settings_profile", name="settings_profile")
     * @param RolesRepository $rolesRepository
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function settingsProfile(RolesRepository $rolesRepository, Request $request, UserPasswordEncoderInterface $encoder)
    {
        /** @var User $user */
        $user = $this->getUser();

        $createOrganization = function ($array) {
            return $this->getDoctrine()->getRepository(Organization::class)->find($array);
        };

        $organizations_id = $rolesRepository->findOrganizationsId($user->getId());

        $role_id = $rolesRepository->findRolesId($user->getId(), $organizations_id[0]);

        $organization = $this->getDoctrine()->getRepository(Organization::class)->find($organizations_id[0]);

        $organizations = array_map($createOrganization, $organizations_id);

        $image_name = $rolesRepository->findUserAvatar($user->getId(), $organizations_id[0]);

        if ($request->query->get('org_id')) {
            $org_id = $request->query->get('org_id');
            $organization = $this->getDoctrine()->getRepository(Organization::class)->find($org_id);
            $role_id = $rolesRepository->findRolesId($user->getId(), $org_id);
            $image_name = $rolesRepository->findUserAvatar($user->getId(), $org_id);
        }

        $role = $this->getDoctrine()->getRepository(Roles::class)->find($role_id);

        $role->getUser();

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(EditRolesSettingsProfileType::class, $role);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $role = $form->getData();

            if ($form->ge0t('user')->get('password')->getData()) {
                $oldPassword = $form->get('user')->get('password')->getData();
                $newPassword = $form->get('user')->get('newPassword')->getData();

                if ($encoder->isPasswordValid($role->getUser(), $oldPassword)) {
                    $hash = $encoder->encodePassword($user, $newPassword);
                    $user->setPassword($hash);
                }
            }

            $em->persist($role);
            $em->flush();

            return $this->redirectToRoute("settings_profile");
        }

        return $this->render('settings/settings_profile.html.twig', [
            'form' => $form->createView(),
            'organization' => $organization,
            'organizations' => $organizations,
            'image_name' => $image_name
        ]);
    }

    /**
     * @Route("/settings_organization", name="settings_organization")
     * @param RolesRepository $rolesRepository
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function settingsOrganization(RolesRepository $rolesRepository, Request $request, \Swift_Mailer $mailer)
    {
        $user = $this->getUser();
        $user_id = $user->getId();

        $createOrganization = function ($array) {
            return $this->getDoctrine()->getRepository(Organization::class)->find($array);
        };

        $createUser = function ($array) {
            return $this->getDoctrine()->getRepository(User::class)->find($array);
        };

        $organizations_id = $rolesRepository->findOrganizationsId($user_id);

        $organization = $this->getDoctrine()->getRepository(Organization::class)->find($organizations_id[0]);

        $organizations = array_map($createOrganization, $organizations_id);

        $users_id = $rolesRepository->findUsersIdBelongsOrganization($organizations_id[0]);

        $users = array_map($createUser, $users_id);

        $user_roles = $rolesRepository->findUsersRolesBelongsOrganization($organizations_id[0]);

        $image_name = $rolesRepository->findUserAvatar($user->getId(), $organizations_id[0]);

        if ($request->query->get('org_id')) {
            $org_id = $request->query->get('org_id');

            $users_id = $rolesRepository->findUsersIdBelongsOrganization($org_id);

            $organization = $this->getDoctrine()->getRepository(Organization::class)->find($org_id);

            $users = array_map($createUser, $users_id);

            $image_name = $rolesRepository->findUserAvatar($user->getId(), $org_id);
        }

        $form = $this->createForm(InviteType::class);

        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {

            $message = (new \Swift_Message('Hello Email'))
                ->setFrom('no-reply@nativeteams.com')
                ->setTo('maks.lushchitsky.99@gmail.com')
                ->setBody(
                    $this->renderView('emails/invite.html.twig'),
                    'text/html'
                );

            $mailer->send($message);

            return $this->redirectToRoute("settings_organization");
        }


        return $this->render('settings/settings_organization.html.twig', [
            'form' => $form->createView(),
            'organizations' => $organizations,
            'organization' => $organization,
            'users' => $users,
            'user_roles' => $user_roles,
            'image_name' => $image_name,
        ]);
    }

    private function createMultipleForm()
    {
        return $this->createForm(AgreementType::class, null);
    }

    /**
     * @Route("/settings_agreements", name="settings_agreements")
     */
    public function settingsAgreements(RolesRepository $rolesRepository, Request $request)
    {
        $user = $this->getUser();
        $user_id = $user->getId();

        $createOrganization = function ($array) {
            return $this->getDoctrine()->getRepository(Organization::class)->find($array);
        };

        $createUser = function ($array) {
            return $this->getDoctrine()->getRepository(User::class)->find($array);
        };

        $organizations_id = $rolesRepository->findOrganizationsId($user_id);

        $organization = $this->getDoctrine()->getRepository(Organization::class)->find($organizations_id[0]);

        $organizations = array_map($createOrganization, $organizations_id);

        $users_id = $rolesRepository->findUsersIdBelongsOrganization($organizations_id[0]);

        $users = array_map($createUser, $users_id);

        $role_id = $rolesRepository->findRolesId($user->getId(), $organizations_id[0]);

        $image_name = $rolesRepository->findUserAvatar($user->getId(), $organizations_id[0]);

        if ($request->query->get('org_id')) {
            $org_id = $request->query->get('org_id');

            $users_id = $rolesRepository->findUsersIdBelongsOrganization($org_id);

            $organization = $this->getDoctrine()->getRepository(Organization::class)->find($org_id);

            $users = array_map($createUser, $users_id);

            $role_id = $rolesRepository->findRolesId($user->getId(), $org_id);

            $image_name = $rolesRepository->findUserAvatar($user->getId(), $org_id);
        }

        $role = $this->getDoctrine()->getRepository(Roles::class)->find($role_id);

        $agreement = new Agreement();

        $em = $this->getDoctrine()->getManager();

        $form = $this->createMultipleForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $role = $form->getData();
            $role->addAgreement($agreement);

            $em->persist($role);
            $em->flush();

            return $this->redirectToRoute("settings_agreements");
        }

        return $this->render('settings/settings_agreements.html.twig',[
            'formObject' => $form,
            'organizations' => $organizations,
            'organization' => $organization,
            'users' => $users,
            'image_name' => $image_name
        ]);
    }
}
