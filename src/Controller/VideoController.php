<?php

namespace App\Controller;

use App\Entity\Bloop;
use App\Entity\Audio;
use App\Entity\Video;
use App\Form\AudioType;
use App\Form\VideoType;
use App\Repository\AudioRepository;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class VideoController extends AbstractController
{
    #[Route('/video', name: 'app_video')]
    public function index(VideoRepository $videoRepository): Response
    {
        return $this->render('video/index.html.twig', [
            'videos' => $videoRepository->findAll(),
        ]);
    }
    #[Route('/video/delete/{id}', name: 'delete_video')]
    public function delete(EntityManagerInterface $manager, Video $video): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $cp = $video;
        $manager->remove($video);
        $manager->flush();
        if ($cp->getBloop()) {
            return $this->redirectToRoute('bloop_file', ['id' => $cp->getBloop()->getId()]);
        } else {
            return $this->redirectToRoute('app_bloop');
        }
    }

    #[Route('/video/bloop/{id}/new', name: 'add_video_bloop')]
    public function create(Request $request, Bloop $bloop, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }


        $video = new Video();
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $video->setBloop($bloop);
            $manager->persist($video);
            $manager->flush();
        };

        return $this->redirectToRoute('bloop_file', ['id' => $bloop->getId()]);
    }
    #[Route('/video/new', name: 'add_video')]
    public function addVideo(Request $request, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $video = new Video();
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($video);
            $manager->flush();
            return $this->redirectToRoute('user_account');
        };

        return $this->render('video/add.html.twig',['form'=>$form->createView()]);
    }
}
