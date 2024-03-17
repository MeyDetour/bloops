<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Audio;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Video;
use App\Form\ArticleCategoryType;
use App\Form\ArticleType;
use App\Form\AudioType;
use App\Form\CategoryType;
use App\Form\CommentType;
use App\Form\ImageType;
use App\Form\VideoType;
use App\Repository\ArticleRepository;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route('/article/show/{id}', name: 'show_article')]
    public function show(Article $article): Response
    {

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'form' => $form
        ]);
    }

    #[Route('/article/new', name: 'new_article')]
    public function create(EntityManagerInterface $manager, \Symfony\Component\HttpFoundation\Request $request): Response

    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article->setCreatedAt(new \DateTimeImmutable());
            $article->setAuthor($this->getUser());
            $article->setStatus('ACTIVE');
            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('new_category_article', ['id' => $article->getId()]);
        }

        return $this->render('article/create.html.twig', [
            'formArticle' => $form->createView(),
        ]);
    }

    #[Route('/article/{id}/new/category', name: 'new_category_article')]
    public function addCategory(EntityManagerInterface $manager, \Symfony\Component\HttpFoundation\Request $request, Article $article): Response

    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $category = new Category();


        $formCategoryForArticles = $this->createForm(ArticleCategoryType::class, $article);
        $formCategory = $this->createForm(CategoryType::class, $category);

        $formCategoryForArticles->handleRequest($request);
        $formCategory->handleRequest($request);
        if ($formCategoryForArticles->isSubmitted() && $formCategoryForArticles->isValid()) {

            $manager->persist($article);
            $manager->flush();
            return $this->redirectToRoute('article_file', ['id' => $article->getId()]);
        }
        if ($formCategory->isSubmitted() && $formCategory->isValid()) {

            $manager->persist($category);
            $manager->flush();
            return $this->redirectToRoute('new_category_article', ['id' => $article->getId()]);
        }

        return $this->render('article/category.html.twig', [
            'formCategoryForArticles' => $formCategoryForArticles->createView(),
            'formCategory' => $formCategory->createView(),
            'article'=>$article
        ]);
    }

    #[Route('/article/edit/{id}', name: 'edit_article')]
    public function edit(EntityManagerInterface $manager, \Symfony\Component\HttpFoundation\Request $request, Article $article): Response

    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if ($this->getUser() != $article->getAuthor()) {
            return $this->redirectToRoute('app_article');
        }
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($article);
            $manager->flush();
            return $this->redirectToRoute('show_article', ['id' => $article->getId()]);
        }
        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView()
        ]);
    }

    #[Route('/article/delete/{id}', name: 'delete_article')]
    public function delete(EntityManagerInterface $manager, Article $article): Response

    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if ($this->getUser() != $article->getAuthor()) {
            return $this->redirectToRoute('app_article');
        }
      $article->setStatus('ARCHIVED');
        return $this->redirectToRoute('app_article');
    }

    #[Route('/article/{id}/file/new', name: 'article_file')]
    public function addFile(Article $article): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if ($this->getUser() != $article->getAuthor()) {
            return $this->redirectToRoute('show_article', ['id' => $article->getId()]);
        }
        $image = new Image();
        $audio = new Audio();
        $video = new Video();
        $formImage = $this->createForm(ImageType::class, $image);
        $formAudio = $this->createForm(AudioType::class, $audio);
        $formVideo = $this->createForm(VideoType::class, $video);
        return $this->render('article/file.html.twig',
            ['formImage' => $formImage->createView(),
                'formAudio' => $formAudio->createView(),
                'formVideo' => $formVideo->createView(),
                'article' => $article]);
    }
    #[Route('/article/{id}/images', name: 'article_images')]
    public function getImages(Article $article): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if ($this->getUser() != $article->getAuthor()) {
            return $this->redirectToRoute('show_article', ['id' => $article->getId()]);
        }
        $liste = [];
        foreach ($article->getImages() as $image){
            $liste[]= [
                $image->getImageName()
            ];
        }

       $data = [
           'images'=>$liste
       ];

        return  $this->json($data,200);
    }


}
