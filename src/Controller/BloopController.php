<?php

namespace App\Controller;

use App\Entity\Bloop;
use App\Entity\Audio;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Video;
use App\Form\ArticleCategoryType;
use App\Form\BloopType;
use App\Form\AudioType;
use App\Form\CategoryType;
use App\Form\CommentType;
use App\Form\ImageType;
use App\Form\VideoType;
use App\Repository\BloopRepository;

use App\Repository\CommentRepository;
use App\Repository\LikeRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Routing\Attribute\Route;

class BloopController extends AbstractController
{
    #[Route('/bloop', name: 'app_bloop')]
    public function index(BloopRepository $repository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }


        return $this->render('client/bloop/index.html.twig', [
            'bloops' => $repository->findBy(['status' => 'ACTIVE'], ['id' => 'ASC']),

        ]);
    }

//CRUD BLOOP (HTML:templates/client/bloop/create.html.twig , JS: , CSS: assets/styles/client/bloop.css)
    #[Route('/bloop/new', name: 'new_bloop')]
    public function create(EntityManagerInterface $manager, \Symfony\Component\HttpFoundation\Request $request): Response

    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $bloop = new Bloop();
        $form = $this->createForm(BloopType::class, $bloop);
        $image = new Image();
        $formImage = $this->createForm(ImageType::class, $image);

        $video = new Video();
        $formVideo = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $bloop->setCreatedAt(new \DateTimeImmutable());
            $bloop->setAuthor($this->getUser());
            $bloop->setStatus('ACTIVE');
            $manager->persist($bloop);
            $manager->flush();

            return $this->redirectToRoute('bloop_file', ['id' => $bloop->getId()]);
        }

        return $this->render('client/bloop/create.html.twig', [
            'formBloop' => $form->createView(),
            'formImage' => $formImage->createView(),
            'formVideo' => $formVideo->createView(),
        ]);
    }

    #[Route('/bloop/{id}/new/category', name: 'new_category_article')]
    public function addCategory(EntityManagerInterface $manager, \Symfony\Component\HttpFoundation\Request $request, Bloop $bloop): Response

    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $category = new Category();


        $formCategoryForArticles = $this->createForm(ArticleCategoryType::class, $bloop);
        $formCategory = $this->createForm(CategoryType::class, $category);

        $formCategoryForArticles->handleRequest($request);
        $formCategory->handleRequest($request);
        if ($formCategoryForArticles->isSubmitted() && $formCategoryForArticles->isValid()) {

            $manager->persist($bloop);
            $manager->flush();
            return $this->redirectToRoute('article_file', ['id' => $bloop->getId()]);
        }
        if ($formCategory->isSubmitted() && $formCategory->isValid()) {

            $manager->persist($category);
            $manager->flush();
            return $this->redirectToRoute('new_category_article', ['id' => $bloop->getId()]);
        }

        return $this->render('client/bloop/category.html.twig', [
            'formCategoryForArticles' => $formCategoryForArticles->createView(),
            'formCategory' => $formCategory->createView(),
            'bloop' => $bloop
        ]);
    }

    #[Route('/bloop/edit/{id}', name: 'edit_bloop')]
    public function edit(EntityManagerInterface $manager, \Symfony\Component\HttpFoundation\Request $request, Bloop $bloop): Response

    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if ($this->getUser() != $bloop->getAuthor()) {
            return $this->redirectToRoute('app_article');
        }

        $video = new Video();
        $formVideo = $this->createForm(VideoType::class, $video);
        $form = $this->createForm(BloopType::class, $bloop);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($bloop);
            $manager->flush();
            return $this->redirectToRoute('bloop_file', ['id' => $bloop->getId()]);
        }
        return $this->render('client/bloop/create.html.twig', [
            'formVideo' => $formVideo->createView(),
            'bloop' => $bloop,
            'formBloop' => $form->createView()
        ]);
    }

    #[Route('/bloop/delete/{id}', name: 'delete_article')]
    public function delete(EntityManagerInterface $manager, Bloop $bloop): Response

    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if ($this->getUser() != $bloop->getAuthor()) {
            return $this->redirectToRoute('app_article');
        }
        $bloop->setStatus('ARCHIVED');
        return $this->redirectToRoute('app_article');
    }

    #[Route('/bloop/{id}/file/new', name: 'bloop_file')]
    public function addFile(Bloop $bloop, EntityManagerInterface $manager, Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(BloopType::class, $bloop);

        $image = new Image();
        $video = new Video();
        $formImage = $this->createForm(ImageType::class, $image);
        $formVideo = $this->createForm(VideoType::class, $video);
        return $this->render('client/bloop/create.html.twig',
            ['formImage' => $formImage->createView(),
                'formVideo' => $formVideo->createView(),
                'formBloop' => $form->createView(),
                'bloop' => $bloop]);
    }

    #[Route('/bloop/archive/{id}', name: 'bloop_archive')]
    public function archiveBloop(Bloop $bloop, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_register');
        }
        if ($this->getUser() != $bloop->getAuthor()) {
            return $this->redirectToRoute('app_bloop');
        }

        $bloop->setStatus('ARCHIVED');
        $manager->persist($bloop);
        $manager->flush();
        return $this->redirectToRoute('app_bloop');
    }

//========================API=====================

//GET ALL INFORMATIONS ABOUT BLOOP ON OVERLAY
    #[Route('/bloop/{id}', name: 'bloop_get')]
    public function getBloop(Bloop $bloop, Request $request, CommentRepository $commentRepository, LikeRepository $likeRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json("no user connected", 400);
        }
        $comments = [];
        foreach ($commentRepository->findBy(['bloop' => $bloop], ['createdAt' => 'DESC']) as $comment) {

            //get images url
            $commentImages = array_map(function ($image) {
                // Retourne l'URL de l'image ou un identifiant unique
                return $image->getImageUrl(); // Ou une autre propriété pertinente
            }, $comment->getImages()->toArray());
            $authorImageUrl = $comment->getAuthor()->getImage() ? $comment->getAuthor()->getImage()->getImageUrl() : 'images/imgProfil.png';

            //format date
            $createdAt = $comment->getCreatedAt();
            $now = Carbon::now($createdAt->getTimezone());
            $diffInSeconds = $createdAt->getTimestamp() - $now->getTimestamp();
            $diffInDays = intval($diffInSeconds / (3600 * 24));
            $diffInHours = intval($diffInSeconds / 3600);
            $diffInHours = abs($diffInHours);
            $diffInDays = abs($diffInDays);
            if ($diffInHours == 0) {
                $humanReadableDate = "il y a moins d'une heure";
            } elseif ($diffInDays == 0) {
                $humanReadableDate = "il y a $diffInHours heure" . ($diffInHours > 1 ? 's' : '');
            } elseif ($diffInDays < 3) {
                $humanReadableDate = "il y a $diffInDays jour" . ($diffInDays > 1 ? 's' : '');
            } else {
                $humanReadableDate = $createdAt->format('j F Y');
            }

            $comments [] = [
                'id' => $comment->getId(),
                'author' => [
                    'id' => $bloop->getAuthor()->getId(),
                    'username' => $bloop->getAuthor()->getUsername(),
                    'email' => $bloop->getAuthor()->getEmail(),
                    'authorImage' => $authorImageUrl,
                ],
                'diffH'=>$diffInHours,
                'diffD'=>$diffInDays,
                'createdAt' => $comment->getCreatedAt()->format('Y-m-d H:i:s'),
                'humanReadableDate' => $humanReadableDate,
                'content' => $comment->getContent(),
                'isLikedByUser' => $comment->isLikedBy($this->getUser()),
                'nbLikes' => count($comment->getLikes()),
                'images' => $commentImages,
            ];


        }
        $bloopImages = array_map(function ($image) {
            return $image->getImageUrl(); // Ou une autre propriété pertinente
        }, $bloop->getImages()->toArray());

        $bloopVideos = array_map(function ($video) {
            return $video->getVideoUrl(); // Ou une autre propriété pertinente
        }, $bloop->getVideos()->toArray());

        $authorImageUrl = $bloop->getAuthor()->getImage() ? $bloop->getAuthor()->getImage()->getImageUrl() : 'images/imgProfil.png';
        $userImageUrl = $this->getUser()->getImage() ? $this->getUser()->getImage()->getImageUrl() : 'images/imgProfil.png';
        $data = ['id' => $bloop->getId(),
            'current_user' => [
                'id' => $this->getUser()->getId(),
                'username' => $this->getUser()->getUsername(),
                'email' => $this->getUser()->getEmail(),
                'authorImage' => $userImageUrl,
            ],
            'author' => [
                'id' => $bloop->getAuthor()->getId(),
                'username' => $bloop->getAuthor()->getUsername(),
                'email' => $bloop->getAuthor()->getEmail(),
                'authorImage' => $authorImageUrl,
            ],

            'createdAt' => $bloop->getCreatedAt()->format('Y-m-d H:i:s'),
            'chapo' => $bloop->getChapo(),
            'nbLikes' => count($bloop->getLikes()),
            'isLikedByUser' => $bloop->isLikedBy($this->getUser()),
            'displayComment' => $bloop->isDisplayComments(),
            'comments' => $comments,
            'images' => $bloopImages,
            'videos' => $bloopVideos,
        ];
        return $this->json($data);
    }


}
