<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Entity\Roles;
use App\Entity\User;
use App\Repository\RolesRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/main", name="main")
     * @param RolesRepository $rolesRepository
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(RolesRepository $rolesRepository, Request $request)
    {
        $user = $this->getUser();
        $user_id = $user->getId();

        $organizations_id = $rolesRepository->findOrganizationsId($user_id);

        $createOrganization = function ($array) {
            return $this->getDoctrine()->getRepository(Organization::class)->find($array);
        };

        $createUser = function ($array) {
            return $this->getDoctrine()->getRepository(User::class)->find($array);
        };

        $image_name = $rolesRepository->findUserAvatar($user->getId(), $organizations_id[0]);

        $user_role = $rolesRepository->findUserRole($user->getId(),  $organizations_id[0]);

        if ($organizations_id) {

            $organizations = array_map($createOrganization, $organizations_id);

            $org_id = $organizations_id[0];

            $organization = $this->getDoctrine()->getRepository(Organization::class)->find($organizations_id[0]);

            $users_id = $rolesRepository->findUsersIdBelongsOrganization($organizations_id[0]);

            $user_roles = $rolesRepository->findUsersRolesBelongsOrganization($organizations_id[0]);

            $users = array_map($createUser, $users_id);

            $users_count = count($users);

            if ($request->query->get('org_id')) {
                $org_id = $request->query->get('org_id');

                $users_id = $rolesRepository->findUsersIdBelongsOrganization($org_id);

                $organization = $this->getDoctrine()->getRepository(Organization::class)->find($org_id);
                $users = array_map($createUser, $users_id);
                $users_count = count($users);

                $user_roles = $rolesRepository->findUsersRolesBelongsOrganization($org_id);

                $image_name = $rolesRepository->findUserAvatar($user->getId(), $org_id);

                $user_role = $rolesRepository->findUserRole($user->getId(),  $org_id);
            }

            if($user_role == 'Employee') {
                return $this->redirect($this->generateUrl('employee_dashboard', [
                    'org_id' => $org_id
                ]));
            }

            return $this->render('main/dashboard.html.twig', [
                'user' => $user,
                'users' => $users,
                'organizations' => $organizations,
                'organization' => $organization,
                'users_count' => $users_count,
                'user_roles' => $user_roles,
                'image_name' => $image_name
            ]);
        } else {
            $users = [];
            $organizations = [];
            $users_count = 0;
            $user_roles = [];
            $organization = null;

            return $this->render('main/dashboard.html.twig', [
                'user' => $user,
                'users' => $users,
                'organization' => $organization,
                'organizations' => $organizations,
                'users_count' => $users_count,
                'user_role' => $user_roles,
                'image_name' => $image_name
            ]);
        }
    }
}
