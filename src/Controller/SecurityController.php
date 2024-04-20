<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\User;
use App\Form\ImageType;
use App\Repository\UserRepository;
use Cassandra\Type\UserType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends AbstractController
{

    #[Route('/user', name: 'app_user')]
    public function index(UserRepository $userRepository): Response
    {
        if (!$this->getUser() || !in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }


    #[Route('/user/image', name: 'add_user_image')]
    public function addUserImage(Request $request, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $image = new Image();
        $formImage = $this->createForm(ImageType::class, $image);
        $formImage->handleRequest($request);
        if ($formImage->isSubmitted() && $formImage->isValid()) {
            $image->setOwner($this->getUser());
            $manager->persist($image);
            $manager->flush();
            return $this->redirectToRoute('user_account');
        }
        return $this->render('image/create.html.twig',
            ['formImage' => $formImage->createView()]);
    }


    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_bloop');
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->redirectToRoute('app_register');
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

//===========================API============================

//CHANGE USERNAME CLIENT (html: templates/client/user/show.html.twig , JS : assets/js/client/user.js , CSS :assets/styles/client/user.css )
    #[Route('/user/profil/update', name: 'user_update')]
    public function updateUsername(EntityManagerInterface $manager, Request $request): Response
    {

        $user = $this->getUser();
        if (!$user) {
            return $this->json("no user connected", 400);
        }
        $form = $this->createForm(\App\Form\UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('image')->getData(); // 'image' est le nom du champ de formulaire
            if ($uploadedFile) {
                $image = new Image($uploadedFile);
                // Configurez l'entité Image comme nécessaire...
                $user->setImage($image);
            }
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('show_user', ['id' => $user->getId()]);

        }
        return $this->render('client/user/edit.html.twig', ['form' => $form->createView()]);

    }

//VERYFIYNG IS EMAIL OR USERNAME IS TAKEN CLIENT (html: templates/client/security/base.html.twig , JS : assets/js/client/register.js , CSS :assets/styles/client/form.css)

    #[Route('/user/username/taken', name: 'is_username_taken')]
    public function isUsernameTaken(UserRepository $repository, Request $request): Response
    {


        // Récupérer les données JSON de la requête
        $data = json_decode($request->getContent(), true);

        // Vérifier si les données sont bien reçues
        if ($data) {
            $username = $data['username'];
            $user = $repository->findOneBy(['username' => $username]);
            if ($user) {

                return $this->json(['message' => 'taken']);
            }
            return $this->json(['message' => 'free']);


        }
        return $this->json(['message' => 'Aucune data trouvée']);
    }

    #[Route('/user/email/taken', name: 'is_email_taken')]
    public function isEmailTaken(UserRepository $repository, Request $request): Response
    {
        // Récupérer les données JSON de la requête
        $data = json_decode($request->getContent(), true);

        // Vérifier si les données sont bien reçues
        if ($data) {
            $username = $data['email'];
            $user = $repository->findOneBy(['email' => $username]);
            if ($user) {

                return $this->json(['message' => 'taken']);
            }
            return $this->json(['message' => 'free']);


        }
        return $this->json(['message' => 'Aucune data trouvée']);
    }

    #[Route('/user/get/information', name: 'get_information')]
    public function showUser(UserRepository $repository, Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json("no user connected", 400);
        }

        // Récupérer les données JSON de la requête
        $data = json_decode($request->getContent(), true);

        // Vérifier si les données sont bien reçues
        if ($data) {
            $id = $data['id'];
            $user = $repository->findOneBy(['id' => $id]);

            if (!$user) {

                return $this->json(['message' => 'user not found']);
            }
            return $this->json(['id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
            ]);


        }
        return $this->json(['message' => 'Aucune data trouvée']);
    }

    #[Route('/user/get/current', name: 'get_current_user')]
    public function getCurrentUser(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json("no user connected", 400);
        }

        // Récupérer les données JSON de la requête

        return $this->json(['id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
        ]);
    }


//=> UPDATE PASSWORD IN   PROFIL CLIENT (HTMH : templates/client/user/show.html.twig , JS :  , css : assets/styles/client/profil.css )
    #[Route('user/last/password', name: 'verify_password')]
    public function isGoodPassword(UserRepository $repository, UserPasswordHasherInterface $userPasswordHasher, Request $request, ValidatorInterface $validator): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(["message" => "No user connected"], Response::HTTP_UNAUTHORIZED);
        }

        // Récupérer les données JSON de la requête
        $data = json_decode($request->getContent(), true);

        // Vérifier si les données sont bien reçues
        if ($data) {

            $isValidPassword = $userPasswordHasher->isPasswordValid($user, $data['password']);

            if ($isValidPassword) {
                return $this->json(['message' => 'ok'], Response::HTTP_OK);
            } else {
                return $this->json(['message' => 'Invalid password']);
            }

        }

        return $this->json(['message' => 'Aucune data trouvée',

        ]);
    }

    #[Route('user/profil/update/username/email', name: 'api_profil_update')]
    public function updateProfil(EntityManagerInterface $manager, Request $request, ValidatorInterface $validator): Response
    {

        $user = $this->getUser();
        if (!$user) {
            return $this->json("no user connected", 400);
        }
        $data = json_decode($request->getContent(), true);
        if ($data) {

            $user->setUsername($data['username'] ?? $user->getUsername());
            $user->setEmail($data['email'] ?? $user->getEmail());

            // Valider les modifications
            $errors = $validator->validate($user);
            if (count($errors) > 0) {
                // Traiter les erreurs de validation, par exemple, en retournant un message d'erreur
                $errorsString = (string)$errors;

                return $this->json(['message' => 'Validation failed', 'errors' => $errorsString], Response::HTTP_BAD_REQUEST);
            }

            // Enregistrer les modifications dans la base de données
            $manager->persist($user);
            $manager->flush();

            // Retourner une réponse
            return $this->json([
                'email' => $user->getEmail(),
                'username' => $user->getUsername(),
                'message' => 'ok'
            ]);
        }

        return $this->json(['message' => 'Aucune data trouvée',

        ]);

    }

    #[Route('user/new/password', name: 'api_new_password')]
    public function newPassword(UserRepository $repository, UserPasswordHasherInterface $userPasswordHasher, Request $request, ValidatorInterface $validator, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json("no user connected", 400);
        }

        // Récupérer les données JSON de la requête
        $data = json_decode($request->getContent(), true);

        // Vérifier si les données sont bien reçues
        if ($data) {


            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $data['password']
                )
            );
            $errors = $validator->validate($user);
            if (count($errors) > 0) {
                // Traiter les erreurs de validation, par exemple, en retournant un message d'erreur
                $errorsString = (string)$errors;

                return $this->json(['message' => 'Validation failed', 'errors' => $errorsString], Response::HTTP_BAD_REQUEST);
            }
            $manager->flush();

            return $this->json(['message' => 'ok']);


        }

        return $this->json(['message' => 'Aucune data trouvée',

        ]);
    }

}
