<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Form\ArticleType;
use App\Form\CategoryType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(CategoryRepository $repository): Response
    {

        return $this->render('category/index.html.twig', [
            'cats' => $repository->findAll(),
        ]);
    }
    #[Route('/category/filter/{id}', name: 'filter_category')]
    public function filter(Category $category): Response
    {
        return $this->render('category/filterByCategory.html.twig', [
            'category' => $category,
        ]);
    }
    #[Route('/category/new', name: 'new_category')]
    public function create(EntityManagerInterface $manager, \Symfony\Component\HttpFoundation\Request $request): Response

    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
             $manager->persist($category);

            $manager->flush();
            return $this->redirectToRoute('app_category');
        }
        return $this->render('category/create.html.twig', [
            'form' => $form,
        ]);
    }
}
