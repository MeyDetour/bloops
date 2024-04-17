<?php

namespace App\Controller;

use App\Entity\Bloop;
use App\Entity\Category;
use App\Form\BloopType;
use App\Form\CategoryType;
use App\Repository\BloopRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route('/admin/category', name: 'admin_app_category')]
    public function index(CategoryRepository $repository, Request $request, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser() || !in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
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
        return $this->render('category/index.html.twig', [
            'cats' => $repository->findAll(),
            'formCategory'=>$form->createView()
        ]);
    }

    #[Route('/category/filter/{id}', name: 'filter_category')]
    public function filter(Category $category): Response
    {    if (!$this->getUser() ) {
        return $this->redirectToRoute('app_login');
    }
        return $this->render('client/bloop/filterByCategory.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/category/delete/{id}', name: 'delete_category')]
    public function delete(Category $category, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()  ) {
            return $this->redirectToRoute('app_login');
        }
        $manager->remove($category);
        $manager->flush();
        return $this->redirectToRoute('app_category');
    }


}
