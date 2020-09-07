<?php

namespace App\Controller;

use App\Entity\Agreement;
use App\Entity\Organization;
use App\Entity\Roles;
use App\Entity\User;
use App\Form\AgreementType;
use App\Repository\RolesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TeamController extends AbstractController
{
    /**
     * @Route("/team", name="team")
     */
    public function index(RolesRepository $rolesRepository, Request $request)
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

        $org_id = $organizations_id[0];

        $organization = $this->getDoctrine()->getRepository(Organization::class)->find($org_id);

        $organizations = array_map($createOrganization, $organizations_id);

        $users_id = $rolesRepository->findUsersIdBelongsOrganization($org_id);

        $users = array_map($createUser, $users_id);

        $selected_user = $this->getDoctrine()->getRepository(User::class)->find($user_id);

        $user_roles = $rolesRepository->findUsersRolesBelongsOrganization($organizations_id[0]);

        $selected_user_role = $rolesRepository->findUserRole($user_id, $org_id);

        $image_name = $rolesRepository->findUserAvatar($user->getId(), $organizations_id[0]);

        $role_id = $rolesRepository->findRolesId($user_id, $org_id);

        if ($request->query->get('org_id') || $request->query->get('selected_user_id')) {
            $org_id = $request->query->get('org_id');

            $selected_user_id = $request->query->get('selected_user_id');

            $selected_user = $this->getDoctrine()->getRepository(User::class)->find($selected_user_id);

            $users_id = $rolesRepository->findUsersIdBelongsOrganization($org_id);

            $organization = $this->getDoctrine()->getRepository(Organization::class)->find($org_id);

            $users = array_map($createUser, $users_id);

            $selected_user_role = $rolesRepository->findUserRole($selected_user_id, $org_id);

            $image_name = $rolesRepository->findUserAvatar($user->getId(), $org_id);

            $role_id = $rolesRepository->findRolesId($selected_user_id, $org_id);
        }

        $role = $this->getDoctrine()->getRepository(Roles::class)->find($role_id);

        $form = $this->createForm(AgreementType::class);

        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
            $agreement = new Agreement();
            
            $role->addAgreement($agreement);
            $em->persist($agreement);
            $em->flush();

            return $this->redirectToRoute("team");
        }

        return $this->render('team/team.html.twig', [
            'organizations' => $organizations,
            'organization' => $organization,
            'users' => $users,
            'user_roles' => $user_roles,
            'selected_user' => $selected_user,
            'org_id' => $org_id,
            'selected_user_role' => $selected_user_role,
            'image_name' => $image_name,
            'form' => $form->createView(),
        ]);
    }
}
