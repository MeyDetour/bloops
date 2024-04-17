<?php

namespace App\Controller;

use App\Entity\Bloop;
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
    #[Route('/podcast', name: 'app_audio')]
    public function index(AudioRepository $audioRepository): Response
    {
        return $this->render('client/podcast/index.html.twig', [
            'audios' => $audioRepository->findAll(),
        ]);
    }
    #[Route('/podcast/delete/{id}', name: 'delete_audio')]
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

    #[Route('/podcast/bloop/{id}/new', name: 'add_audio_article')]
    public function create(Request $request, Bloop $article, EntityManagerInterface $manager): Response
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
    #[Route('/podcast/bloop/{id}/editAudio', name: 'edit_audio')]
    public function editPodcastAudio( Request $request, Audio $audio, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(PodcastType::class, $audio);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($audio);
            $manager->flush();
            return $this->redirectToRoute('app_audio');
        };


        return $this->render('client/podcast/editFile.html.twig', ['form'=>$form->createView(),'podcast'=>$audio]);

    }
    #[Route('/podcast/bloop/{id}/editTitleDescription', name: 'edit_titleDescription')]
    public function editPodcastTitleDescription( Request $request, Audio $audio, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(PodcastType::class, $audio);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($audio);
            $manager->flush();
            return $this->redirectToRoute('app_audio');
        };


        return $this->render('client/podcast/editTitleDescription.html.twig', ['form'=>$form->createView(),'podcast'=>$audio]);

    }
    #[Route('/podcast/new', name: 'add_audio')]
    public function addPodcast(Request $request, EntityManagerInterface $manager): Response
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

        return $this->render('client/podcast/add.html.twig',['form'=>$form->createView()]);
    }
}
