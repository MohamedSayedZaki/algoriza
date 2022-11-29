<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/category')]
class CategoryController extends AbstractController
{

    private $categoryRepository = '';
    public function __construct(CategoryRepository $repo)
    {
        $this->categoryRepository = $repo;
    }

    #[Route('/', name: 'app_category')]
    public function index(): Response
    {
        $categories = $this->categoryRepository->findAll();
        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/new', name: 'app_category_new',methods:['GET','POST'])]
    public function new(Request $request, ManagerRegistry $doctrine)
    {
        if($request->getMethod() === 'POST'){
            $entityManager = $doctrine->getManager();

            if(!$request->request->get('cname')){
                $this->addFlash('notice', 'You Need To add category name');
                return $this->redirectToRoute('app_category_new', [], Response::HTTP_SEE_OTHER);     
            }
            
            if(!$request->request->get('cactive')){
                $this->addFlash('notice', 'You need to check if category is active or not');
                return $this->redirectToRoute('app_category_new', [], Response::HTTP_SEE_OTHER);     
            }

            $active = $request->request->get('cactive') == 2 ? 0:1;
            $category = $doctrine->getRepository(Category::class)->findBy(['name' => $request->request->get('cname')]);
            if($category){
                $this->addFlash('notice', 'Category Found Before');
                return $this->redirectToRoute('app_category_new', [], Response::HTTP_SEE_OTHER);     
            }

            $category = new Category();
            $category->setName($request->request->get('cname'));
            $category->setActive($active);
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirectToRoute('app_category', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category/new.html.twig');
    }

    #[Route('/{id}/edit', name: 'app_category_edit')]
    public function edit($id, Request $request, CategoryRepository $categoryRepository, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        $category = $entityManager->getRepository(Category::class)->find($id);

        if (!$category) {
            throw $this->createNotFoundException('No Category found for id '.$id);
        }

        if($request->getMethod() === 'POST'){
            $active = $request->request->get('cactive') == 2 ? 0:1;
              
            $category->setName($request->request->get('cname'));
            $category->setActive($active);
            $entityManager->persist($category);
            $entityManager->flush();

            $categoryRepository->add($category, true);
            return $this->redirectToRoute('app_category', [], Response::HTTP_SEE_OTHER);

        }

        $categories = $doctrine->getRepository(Category::class)->findAll();

        return $this->render('category/edit.html.twig', [
            'category' => $category
        ]);
    }
    
    #[Route('/search', name: 'app_category_search')]
    public function search(Request $request, ManagerRegistry $doctrine){
        $categories = $doctrine->getRepository(Category::class)->search($request->request->get('search'));

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/{id}/remove', name: 'app_category_remove')]
    public function remove($id, ManagerRegistry $doctrine)
    {
        if($id){
            $em = $doctrine->getManager();
            $category = $em->getRepository(Category::class)->find($id);

            if (!$category) {
                throw $this->createNotFoundException('No Category found for id '.$id);
            }

            $doctrine->getRepository(Category::class)->remove($category);
            $em->flush();
        }
        return $this->redirectToRoute('app_category', [], Response::HTTP_SEE_OTHER);
    }
}
