<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Image;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Form\ImageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CommentController extends AbstractController
{

    #[Route('/comment/{id}/new', name: 'new_comment')]
    public function create(EntityManagerInterface $manager,Article $article, \Symfony\Component\HttpFoundation\Request $request): Response

    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setArticle($article);
            $comment->setAuthor($this->getUser());
            $comment->setAuthor($this->getUser());
            $comment->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($comment);

            $manager->flush();
            }
        return $this->redirectToRoute('show_article', ['id' => $article->getId()]);

    }
    #[Route('/comment/delete/{id}', name: 'delete_comment')]
    public function delete(EntityManagerInterface $manager, Comment $comment): Response

    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if ($this->getUser() != $comment->getAuthor()) {
            return $this->redirectToRoute('app_article');
        }
        $article = $comment->getArticle();
        $manager->remove($comment);
        $manager->flush();
        return $this->redirectToRoute('show_article',['id'=>$article->getId()]);
    }
    #[Route('/comment/{id}/image/new', name: 'comment_image')]
    public function addImage( Comment $comment): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if ($this->getUser() != $comment->getAuthor()) {
            return $this->redirectToRoute('app_article');
        }
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        return $this->render('comment/file.html.twig', ['form' => $form->createView(),'comment'=>$comment]);
    }
}
