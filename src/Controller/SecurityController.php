<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    #[Route('/user', name: 'app_user')]
    public function index(UserRepository $userRepository): Response
    {
        if (!$this->getUser() || in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/user/account', name: 'user_account')]
    public function userAccount(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('user/show.html.twig', [

        ]);
    }

    #[Route('/user/update/username/{id}/{username}', name: 'user_update')]
    public function updateUsername(User $user, string $username, EntityManagerInterface $manager): Response
    {

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if ($user) {
            $user->setUsername($username);
            $manager->persist($user);
            $manager->flush();
            $data = [
                'user' => $user
            ];
            return $this->json($data);
        }
        $data = [
            'user' => $user
        ];
        return $this->json($data);
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_article');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
