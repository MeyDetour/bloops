<?php

namespace App\Controller;

use App\Entity\Bloop;
use App\Entity\Audio;
use App\Entity\Comment;
use App\Entity\FriendRequest;
use App\Entity\Image;
use App\Entity\Nem;
use App\Entity\Video;
use App\Form\AudioType;
use App\Form\BloopType;
use App\Form\ImageType;
use App\Form\PodcastType;
use App\Form\VideoType;
use App\Repository\AudioRepository;
use App\Repository\CommentRepository;
use App\Repository\ImageRepository;
use App\Repository\LikeRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AudioController extends AbstractController
{
    #[Route('/podcast', name: 'app_podcast')]
    public function index(AudioRepository $audioRepository): Response
    {
        return $this->render('client/podcast/index.html.twig', [
            'podcasts' => $audioRepository->findBy(['status' => 'ACTIVE']),
        ]);
    }

    #[Route('/podcast/delete/{id}', name: 'delete_podcast')]
    public function delete(EntityManagerInterface $manager, Audio $podcast): Response

    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if ($this->getUser() != $podcast->getAuthor()) {
            return $this->redirectToRoute('app_podcast');
        }
        $manager->remove($podcast);
        $manager->flush();
        return $this->redirectToRoute('app_podcast');
    }

    #[Route('/podcast/archiver/{id}', name: 'archiver_podcast')]
    public function archiver(EntityManagerInterface $manager, Audio $podcast): Response

    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if ($this->getUser() != $podcast->getAuthor()) {
            return $this->redirectToRoute('app_podcast');
        }
        $podcast->setStatus('ARCHIVED');
        $manager->persist($podcast);
        $manager->flush();
        return $this->redirectToRoute('app_podcast');
    }

    #[Route('/podcast/new', name: 'add_podcast')]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $podcast = new Audio();
        $form = $this->createForm(PodcastType::class, $podcast);
        $image = new Image();
        $formImage = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $podcast->setCreatedAt(new \DateTimeImmutable());
            $podcast->setStatus('ACTIVE');
            $podcast->setAuthor($this->getUser());
            $friendRequest = new FriendRequest();
            $friendRequest->setStatus('ACTIVE');
            $friendRequest->setType('AUDIO_POST');
            $friendRequest->setVisible(true);
            $friendRequest->setRequester($this->getUser());
            $friendRequest->setRequested($this->getUser());
            $friendRequest->setCreatedAt(new \DateTimeImmutable());


            $manager->persist($friendRequest);

            $manager->persist($podcast);
            $manager->flush();
            return $this->redirectToRoute('podcast_file', ['id' => $podcast->getId()]);
        }

        return $this->render('client/podcast/create.html.twig', [
            'form' => $form->createView(),
            'formImage' => $formImage->createView(),

        ]);
    }

    #[Route('/podcast/edit/{id}', name: 'edit_podcast')]
    public function edit(EntityManagerInterface $manager, \Symfony\Component\HttpFoundation\Request $request, Audio $podcast): Response

    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if ($this->getUser() != $podcast->getAuthor()) {
            return $this->redirectToRoute('app_article');
        }

        $image = new Image();
        $form = $this->createForm(PodcastType::class, $podcast);
        $formImage = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($podcast);
            $manager->flush();
            return $this->redirectToRoute('podcast_file', ['id' => $podcast->getId()]);
        }
        return $this->render('client/podcast/create.html.twig', ['formImage' => $formImage->createView(),
            'form' => $form->createView(),
            'podcast' => $podcast]);
    }

    #[Route('/podcast/{id}/file/new', name: 'podcast_file')]
    public function addFile(Audio $podcast, EntityManagerInterface $manager, Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $image = new Image();

        $form = $this->createForm(PodcastType::class, $podcast);
        $formImage = $this->createForm(ImageType::class, $image);
        return $this->render('client/podcast/create.html.twig',
            ['formImage' => $formImage->createView(),
                'form' => $form->createView(),
                'podcast' => $podcast]);

    }

    #[Route('/podcast/{id}', name: 'podcast_get')]
    public function getBloop(Audio $podcast, Request $request, CommentRepository $commentRepository, LikeRepository $likeRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json("no user connected", 400);
        }
        $comments = [];
        foreach ($commentRepository->findBy(['audio' => $podcast], ['createdAt' => 'DESC']) as $comment) {

            //get images url
            $imagesPodcast = array_map(function ($image) {
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
                    'id' => $podcast->getAuthor()->getId(),
                    'username' => $podcast->getAuthor()->getUsername(),
                    'email' => $podcast->getAuthor()->getEmail(),
                    'authorImage' => $authorImageUrl,
                ],
                'diffH'=>$diffInHours,
                'diffD'=>$diffInDays,
                'createdAt' => $comment->getCreatedAt()->format('Y-m-d H:i:s'),
                'humanReadableDate' => $humanReadableDate,
                'content' => $comment->getContent(),
                'isLikedByUser' => $comment->isLikedBy($this->getUser()),
                'nbLikes' => count($comment->getLikes()),
                'images' => $imagesPodcast,
            ];
        }

        $authorImageUrl = $podcast->getAuthor()->getImage() ? $podcast->getAuthor()->getImage()->getImageUrl() : 'images/imgProfil.png';
        $userImageUrl = $this->getUser()->getImage() ? $this->getUser()->getImage()->getImageUrl() : 'images/imgProfil.png';

      $imageUrl = null;
          if($podcast->getImage()) {$imageUrl = $podcast->getImage()->getImageUrl();}
        $data = ['id' => $podcast->getId(),
            'current_user' => [
                'id' => $this->getUser()->getId(),
                'username' => $this->getUser()->getUsername(),
                'email' => $this->getUser()->getEmail(),
                'authorImage' => $userImageUrl,
            ],
            'author' => [
                'id' => $podcast->getAuthor()->getId(),
                'username' => $podcast->getAuthor()->getUsername(),
                'email' => $podcast->getAuthor()->getEmail(),
                'authorImage' => $authorImageUrl,
            ],

            'createdAt' => $podcast->getCreatedAt()->format('Y-m-d H:i:s'),
            'titre' => $podcast->getTitre(),
            'description' => $podcast->getDescription(),
            'audioName' => $podcast->getAudioName(),
            'nbLikes' => count($podcast->getLikes()),
            'isLikedByUser' => $podcast->isLikedBy($this->getUser()),
            'comments' => $comments,
            'displayComment' => $podcast->isDisplayComment(),
            'image' => $imageUrl
        ];
        return $this->json($data);
    }
}
