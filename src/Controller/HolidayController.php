<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Entity\User;
use App\Repository\RolesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HolidayController extends AbstractController
{
    /**
     * @Route("/holiday", name="holiday")
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

        $org_id = $request->query->get('org_id');

        $organization = $this->getDoctrine()->getRepository(Organization::class)->find($org_id);

        $organizations = array_map($createOrganization, $organizations_id);

        $image_name = $rolesRepository->findUserAvatar($user->getId(), $organizations_id[0]);

        if ($request->query->get('org_id')) {
            $org_id = $request->query->get('org_id');

            $organization = $this->getDoctrine()->getRepository(Organization::class)->find($org_id);

            $image_name = $rolesRepository->findUserAvatar($user->getId(), $org_id);
        }

        $user_role = $rolesRepository->findUserRole($user->getId(), $org_id);

        if($user_role == 'Owner' || $user_role == 'Manager') {
            return $this->redirect($this->generateUrl('main', [
                'org_id' => $org_id
            ]));
        }

        return $this->render('holiday/holiday.html.twig', [
            'organizations' => $organizations,
            'organization' => $organization,
            'org_id' => $org_id,
            'image_name' => $image_name
        ]);
    }
}
