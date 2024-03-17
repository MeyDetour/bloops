<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Audio;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Nem;
use App\Entity\Video;
use App\Form\AudioType;
use App\Form\ImageType;
use App\Form\PodcastType;
use App\Form\VideoType;
use App\Repository\AudioRepository;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AudioController extends AbstractController
{
    #[Route('/audio', name: 'app_audio')]
    public function index(AudioRepository $audioRepository): Response
    {
        return $this->render('audio/index.html.twig', [
            'audios' => $audioRepository->findAll(),
        ]);
    }
    #[Route('/audio/delete/{id}', name: 'delete_audio')]
    public function delete(EntityManagerInterface $manager, Audio $audio): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $cp = $audio;
        $manager->remove($audio);
        $manager->flush();
        if ($cp->getArticle()) {
            return $this->redirectToRoute('article_file', ['id' => $cp->getArticle()->getId()]);
        } else {
            return $this->redirectToRoute('app_article');
        }
    }

    #[Route('/audio/article/{id}/new', name: 'add_audio_article')]
    public function create( Request $request, Article $article, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $audio = new Audio();
        $form = $this->createForm(AudioType::class, $audio);

        $form->handleRequest($request);
          if ($form->isSubmitted() && $form->isValid()) {

            $audio->setArticle($article);
            $manager->persist($audio);
            $manager->flush();
        };


        return $this->redirectToRoute('article_file', ['id'=>$article->getId()]);

    }
    #[Route('/audio/new', name: 'add_audio')]
    public function addAudio(Request $request, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $audio = new Audio();
        $form = $this->createForm(PodcastType::class, $audio);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($audio);
            $manager->flush();
            return $this->redirectToRoute('user_account');
        };

        return $this->render('audio/add.html.twig',['form'=>$form->createView()]);
    }
}
