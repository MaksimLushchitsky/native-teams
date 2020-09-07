<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Entity\User;
use App\Repository\RolesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EmployeeDashboardController extends AbstractController
{
    /**
     * @Route("/employee_dashboard", name="employee_dashboard")
     * @param RolesRepository $rolesRepository
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function index(RolesRepository $rolesRepository, Request $request)
    {
        $user = $this->getUser();
        $user_id = $user->getId();

        $org_id = $request->query->get('org_id');

        $user_role = $rolesRepository->findUserRole($user->getId(),  $org_id);

        $createOrganization = function ($array) {
            return $this->getDoctrine()->getRepository(Organization::class)->find($array);
        };

        $organizations_id = $rolesRepository->findOrganizationsId($user_id);

        $organization = $this->getDoctrine()->getRepository(Organization::class)->find($org_id);

        $organizations = array_map($createOrganization, $organizations_id);

        $image_name = $rolesRepository->findUserAvatar($user->getId(), $org_id);

        if ($request->query->get('org_id')) {
            $org_id = $request->query->get('org_id');

            $organization = $this->getDoctrine()->getRepository(Organization::class)->find($org_id);

            $image_name = $rolesRepository->findUserAvatar($user->getId(), $org_id);

            $user_role = $rolesRepository->findUserRole($user->getId(), $org_id);

            if($user_role == 'Owner' || $user_role == 'Manager') {
                return $this->redirect($this->generateUrl('main', [
                    'last_org_id' => $org_id
                ]));
            }
        }

        return $this->render('employee_dashboard/employee_dashboard.html.twig', [
            'organizations' => $organizations,
            'organization' => $organization,
            'image_name' => $image_name,
        ]);
    }
}
