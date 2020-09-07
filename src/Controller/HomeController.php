<?php

namespace App\Controller;

use App\Form\HomeSignUpType;
use App\Form\InviteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private function createMultipleForm()
    {
        return $this->createForm(InviteType::class, null);
    }

    public function getFilteredListAction(Request $request)
    {
        $email = $request->request->get('email');
        $result = [];
        return new JsonResponse($result);
    }

    /**
     * @Route("/home", name="home")
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function homePage(Request $request, \Swift_Mailer $mailer)
    {
        $tryItNowForm = $this->createMultipleForm();

        $tryItNowForm->handleRequest($request);

        $invite_email = $request->request->get('email');

        if ($invite_email) {
            $message = (new \Swift_Message('TryItNow email'))
                ->setFrom('no-reply@nativeteams.com')
                ->setTo('assa.job@gmail.com')
                ->setBody(
                    $this->renderView('emails/try_it_now.html.twig', [
                        'invite_email' => $invite_email
                    ]),
                    'text/html'
                );

            $mailer->send($message);
       }

        $signUpForm = $this->createForm(HomeSignUpType::class, null);

        $signUpForm->handleRequest($request);

        if ($signUpForm->isSubmitted() && $signUpForm->isValid()) {

            $name = $signUpForm->get('name')->getData();
            $email = $signUpForm->get('email')->getData();
            $phone = $signUpForm->get('phone')->getData();
            $numberOfEmployees = $signUpForm->get('number_of_employees')->getData();

            $message = (new \Swift_Message('SignUp email'))
                ->setFrom('no-reply@nativeteams.com')
                ->setTo('maks.lushchitsky.99@gmail.com')
                ->setBody(
                    $this->renderView('emails/sign_up.html.twig', [
                        'email' => $email,
                        'name' => $name,
                        'phone' => $phone,
                        'number_of_employees' => $numberOfEmployees
                    ]),
                    'text/html'
                );

            $mailer->send($message);

            return $this->redirectToRoute("home");
        }

        return $this->render('home/home.html.twig', [
            'formObject' => $tryItNowForm,
            'signUpForm' => $signUpForm->createView()
        ]);
    }

    /**
     * @Route("/for_employees", name="for_employees")
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function forEmployees(Request $request, \Swift_Mailer $mailer)
    {
        $tryItNowForm = $this->createMultipleForm();

        $tryItNowForm->handleRequest($request);

        $invite_email = $request->request->get('email');

        if ($invite_email) {
            $message = (new \Swift_Message('TryItNow email'))
                ->setFrom('no-reply@nativeteams.com')
                ->setTo('assa.job@gmail.com')
                ->setBody(
                    $this->renderView('emails/try_it_now.html.twig', [
                        'invite_email' => $invite_email
                    ]),
                    'text/html'
                );

            $mailer->send($message);
        }

        $signUpForm = $this->createForm(HomeSignUpType::class, null);

        $signUpForm->handleRequest($request);

        if ($signUpForm->isSubmitted() && $signUpForm->isValid()) {

            $name = $signUpForm->get('name')->getData();
            $email = $signUpForm->get('email')->getData();
            $phone = $signUpForm->get('phone')->getData();
            $numberOfEmployees = $signUpForm->get('number_of_employees')->getData();

            $message = (new \Swift_Message('SignUp email'))
                ->setFrom('no-reply@nativeteams.com')
                ->setTo('assa.job@gmail.com')
                ->setBody(
                    $this->renderView('emails/sign_up.html.twig', [
                        'email' => $email,
                        'name' => $name,
                        'phone' => $phone,
                        'number_of_employees' => $numberOfEmployees
                    ]),
                    'text/html'
                );

            $mailer->send($message);

            return $this->redirectToRoute("home");
        }

        return $this->render('home/for_employees.html.twig', [
            'formObject' => $tryItNowForm,
            'signUpForm' => $signUpForm->createView()
        ]);
    }

    /**
     * @Route("/for_employers", name="for_employers")
     */
    public function forEmployers(Request $request, \Swift_Mailer $mailer)
    {
        $tryItNowForm = $this->createMultipleForm();

        $tryItNowForm->handleRequest($request);

        $invite_email = $request->request->get('email');

        if ($invite_email) {
            $message = (new \Swift_Message('TryItNow email'))
                ->setFrom('no-reply@nativeteams.com')
                ->setTo('assa.job@gmail.com')
                ->setBody(
                    $this->renderView('emails/try_it_now.html.twig', [
                        'invite_email' => $invite_email
                    ]),
                    'text/html'
                );

            $mailer->send($message);
        }

        $signUpForm = $this->createForm(HomeSignUpType::class, null);

        $signUpForm->handleRequest($request);

        if ($signUpForm->isSubmitted() && $signUpForm->isValid()) {

            $name = $signUpForm->get('name')->getData();
            $email = $signUpForm->get('email')->getData();
            $phone = $signUpForm->get('phone')->getData();
            $numberOfEmployees = $signUpForm->get('number_of_employees')->getData();

            $message = (new \Swift_Message('SignUp email'))
                ->setFrom('no-reply@nativeteams.com')
                ->setTo('assa.job@gmail.com')
                ->setBody(
                    $this->renderView('emails/sign_up.html.twig', [
                        'email' => $email,
                        'name' => $name,
                        'phone' => $phone,
                        'number_of_employees' => $numberOfEmployees
                    ]),
                    'text/html'
                );

            $mailer->send($message);

            return $this->redirectToRoute("home");
        }

        return $this->render('home/for_employers.html.twig', [
            'formObject' => $tryItNowForm,
            'signUpForm' => $signUpForm->createView()
        ]);
    }
}
