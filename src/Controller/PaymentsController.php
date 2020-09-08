<?php

declare(strict_types = 1);
namespace App\Controller;

use App\Entity\Organization;
use App\Entity\User;
use App\Repository\RolesRepository;
use Stripe\Charge;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PaymentsController extends AbstractController
{
    /**
     * @Route("/payments", name="payments")
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

        $org_id = $organizations_id[0];

        $organization = $this->getDoctrine()->getRepository(Organization::class)->find($org_id);

        $organizations = array_map($createOrganization, $organizations_id);

        $image_name = $rolesRepository->findUserAvatar($user->getId(), $organizations_id[0]);

        $users_id = $rolesRepository->findUsersIdBelongsOrganization($organizations_id[0]);

        //Roles for all users in organization
        $user_roles = $rolesRepository->findUsersRolesBelongsOrganization($organizations_id[0]);

        //Amounts for all users in organization
        $user_amounts = $rolesRepository->findUsersAmountBelongsOrganization($organizations_id[0]);

        $users = array_map($createUser, $users_id);

        if ($request->query->get('org_id')) {
            $org_id = $request->query->get('org_id');

            $organization = $this->getDoctrine()->getRepository(Organization::class)->find($org_id);

            $image_name = $rolesRepository->findUserAvatar($user->getId(), $org_id);

            $users_id = $rolesRepository->findUsersIdBelongsOrganization($org_id);

            $user_roles = $rolesRepository->findUsersRolesBelongsOrganization($org_id);

            $user_amounts = $rolesRepository->findUsersAmountBelongsOrganization($org_id);

            $users = array_map($createUser, $users_id);
        }

        $users_count = count($users);

        //Role for current user
        $user_role = $rolesRepository->findUserRole($user->getId(), $org_id);

        if ($user_role == 'Employee') {
            return $this->redirect($this->generateUrl('employee_dashboard', [
                'org_id' => $org_id
            ]));
        }

//        Stripe::setApiKey('sk_test_51HOTKXDQSTjfKNCRdQIJyIBqo3227zKUia5n1Qh2ocjFny0ALDj5QWyKXU8r3O2tO03m8MALbWB1zSz6M1JI2Rye00fHlxhrak');
//
//        $charge = Charge::create([
//            'amount' => 2000,
//            'currency' => 'eur',
//            'source' => $request->request->get('stripeToken'),
//            'description' => 'Test payment'
//        ]);

        return $this->render('payments/payments.html.twig', [
            'organizations' => $organizations,
            'organization' => $organization,
            'org_id' => $org_id,
            'users' => $users,
            'user_amounts' => $user_amounts,
            'user_roles' => $user_roles,
            'image_name' => $image_name,
            'users_count' => $users_count,
            'org_wallet' => $organization->getOrgWallet()
        ]);
    }
}
