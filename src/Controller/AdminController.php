<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Entity\Roles;
use App\Entity\User;
use App\Form\CreateOrganizationType;
use App\Form\EditOrganizationType;
use App\Form\EditUserType;
use App\Form\RoleUserInOrganizationType;
use App\Form\RoleUserInOrganizationWithoutOwnerType;
use App\Repository\RolesRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository(User::class)->findAll();

        $organizations = $em->getRepository(Organization::class)->findAll();

        return $this->render('admin/admin.html.twig', [
            'users' => $users,
            'organizations' => $organizations,
        ]);
    }

    /**
     * @Route("/remove_user/{id}", name="remove_user")
     */
    public function removeUser(User $user, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute("admin");
    }

    /**
     * @Route("/edit_user/{id}", name="edit_user")
     * @param User $user
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editUser(User $user, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute("admin");
        }

        return $this->render('admin/edit_user.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/create_organization", name="create_organization")
     */
    public function createOrganization(Request $request)
    {
        $organization = new Organization();
        $form = $this->createForm(CreateOrganizationType::class, $organization);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $organization->getName();
            $em->persist($organization);
            $em->flush();

            return $this->redirectToRoute("admin");
        }

        return $this->render('admin/create_organization.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/remove_organization/{id}", name="remove_organization")
     */
    public function removeOrganization(Organization $organization, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($organization);
        $em->flush();

        return $this->redirectToRoute("admin");
    }

    /**
     * @Route("/edit_organization/{id}", name="edit_organization")
     */
    public function editOrganization(Organization $organization, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(EditOrganizationType::class, $organization);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $organization = $form->getData();

            $em->persist($organization);
            $em->flush();

            return $this->redirectToRoute("admin");
        }

        return $this->render('admin/edit_organization.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/add_people/{org_id}", name="add_people")
     * @param $org_id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addPeople($org_id, Request $request, RolesRepository $rolesRepository, UserRepository $userRepository)
    {
        $createUser = function ($array) {
            return $this->getDoctrine()->getRepository(User::class)->find($array);
        };

        $role = new Roles();
        $organization = $this->getDoctrine()->getRepository(Organization::class)->find($org_id);
        $organization->addOrganizationRole($role);

        $users_id = $rolesRepository->findUsersIdBelongsOrganization((int)$org_id);

        $all_users_id = $userRepository->findAllUsersId();

        $available_users_id = array_diff($all_users_id, $users_id);

        $users = array_map($createUser, $available_users_id);

        return $this->render('admin/add_people.html.twig', [
            'org_id' => $org_id,
            'users' => $users,
            'organization' => $organization
        ]);
    }

    /**
     * @Route("/settings_people/{user_id}/{org_id}", name="settings_people")
     * @param $user_id
     * @param $org_id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function settingsPeople($user_id, $org_id, Request $request, RolesRepository $rolesRepository)
    {
        $role = new Roles();
        $user = $this->getDoctrine()->getRepository(User::class)->find($user_id);
        $user->addOrganizationRole($role);

        $organization = $this->getDoctrine()->getRepository(Organization::class)->find($org_id);
        $organization->addOrganizationRole($role);

        $users_roles = $rolesRepository->findUsersRolesBelongsOrganization($org_id);

        $isOwner = array_map(function ($array) {
            return $array['role'];
        }, $users_roles);

//        $isOwner = array_search('Owner', array_map(function ($array) {
//            return $array['role'];
//        }, $users_roles));

        if ($isOwner) {
            $form = $this->createForm(RoleUserInOrganizationWithoutOwnerType::class, $role);
        } else {
            $form = $this->createForm(RoleUserInOrganizationType::class, $role);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $role->getRole();
            $em->persist($role);
            $em->flush();

            return $this->redirectToRoute("admin");
        }

        return $this->render('admin/settings_people.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/show_employees/{id}", name="show_employees")
     */
    public function showEmployees($id, RolesRepository $rolesRepository)
    {
        $createUser = function ($array) {
            return $this->getDoctrine()->getRepository(User::class)->find($array);
        };

        $organization = $this->getDoctrine()->getRepository(Organization::class)->find($id);

        $users_id = $rolesRepository->findUsersIdBelongsOrganization((int)$id);

        $users = array_map($createUser, $users_id);

        $user_roles = $rolesRepository->findUsersRolesBelongsOrganization($id);

        return $this->render('admin/show_employees.html.twig', [
            'users' => $users,
            'organization' => $organization,
            'org_id' => $id,
            'user_roles' => $user_roles
        ]);
    }

    /**
     * @Route("/remove_employee/{user_id}/{org_id}", name="remove_employee")
     * @param $user_id
     * @param $org_id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeEmployee($user_id, $org_id, RolesRepository $rolesRepository)
    {
        $em = $this->getDoctrine()->getManager();

        $role_id = $rolesRepository->findRolesId($user_id, $org_id);

        $role = $this->getDoctrine()->getRepository(Roles::class)->find($role_id);

        $em->remove($role);

        $em->flush();

        return $this->redirectToRoute("admin");
    }

    /**
     * @Route("/show_organizations/{id}", name="show_organizations")
     * @param $id
     * @param RolesRepository $rolesRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showOrganizations($id, RolesRepository $rolesRepository)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        $roles_in_organizations = $rolesRepository->findUserRolesAndOrganizations($id);

        $createOrganization = function ($array) {
            return $this->getDoctrine()->getRepository(Organization::class)->find($array['organization_id']);
        };

        $organizations = array_map($createOrganization, $roles_in_organizations);
        $roles = array_map(function ($array) {return $array;}, $roles_in_organizations);

        return $this->render('admin/show_organizations.html.twig', [
            'user' => $user,
            'organizations' => $organizations,
            'roles' => $roles
        ]);
    }
}
