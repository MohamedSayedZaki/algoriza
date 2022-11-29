<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Service\FileUploader;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/product')]
class ProductController extends AbstractController
{
    private $productRepository = '';
    public function __construct(ProductRepository $repo){
        $this->productRepository = $repo;
    }

    #[Route('/', name: 'app_product',methods:['GET'])]
    public function index(): Response
    {
        $products = $this->productRepository->findAll();
        return $this->render('product/index.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('/new', name: 'app_product_new',methods:['POST','GET'])]
    public function new(Request $request, ManagerRegistry $doctrine, ProductRepository $productRepository, FileUploader $fileUploader): Response
    {
        if($request->getMethod() === 'POST'){
            if(!$request->request->get('pcategory')){
                $this->addFlash('notice', 'You Need To add at least one category');
                return $this->redirectToRoute('app_category_new', [], Response::HTTP_SEE_OTHER);     
            }

            if(!$request->files->get('pimage')){
                $this->addFlash('notice', 'Please upload product image');
                return $this->redirectToRoute('app_category_new', [], Response::HTTP_SEE_OTHER);     
            }
            $productFile = $request->files->get('pimage');
            $productFileName = $fileUploader->upload($productFile);     

            $category = $doctrine->getRepository(Category::class)->find($request->request->get('pcategory'));

            $product = new Product();
            $product->setName($request->request->get('pname'));
            $product->setPicture($productFileName);
            $product->setDescription($request->request->get('pdescription'));
            $product->setTags($request->request->get('ptags'));
            $product->setCategory($category);

            $productRepository->add($product, true);
            return $this->redirectToRoute('app_product', [], Response::HTTP_SEE_OTHER);
        }

        $categories = $doctrine->getRepository(Category::class)->findAll();

        return $this->render('product/new.html.twig', [
            'categories' => $categories
        ]);
    } 
    
    #[Route('/{id}/edit', name: 'app_product_edit')]
    public function edit($id, Request $request, ProductRepository $productRepository, ManagerRegistry $doctrine, FileUploader $fileUploader)
    {
        $em = $doctrine->getManager();
        $product = $em->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException('No Product found for id '.$id);
        }

        if($request->getMethod() === 'POST'){
            $category = $doctrine->getRepository(Category::class)->find($request->request->get('pcategory'));

            if($request->files->get('pimage')){
                $productFile = $request->files->get('pimage');
                $productFileName = $fileUploader->upload($productFile);  
                $product->setPicture($productFileName);
            }
              
            $product->setName($request->request->get('pname'));
            $product->setDescription($request->request->get('pdescription'));
            $product->setTags($request->request->get('ptags'));
            $product->setCategory($category);

            $productRepository->add($product, true);
            return $this->redirectToRoute('app_product', [], Response::HTTP_SEE_OTHER);

        }

        $categories = $doctrine->getRepository(Category::class)->findAll();

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'categories' => $categories
        ]);
    } 
    
    #[Route('/search', name: 'app_product_search')]
    public function search(Request $request, ManagerRegistry $doctrine){
        $products = $doctrine->getRepository(Product::class)->search($request->request->get('search'));

        return $this->render('product/index.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('/{id}/remove', name: 'app_product_remove')]
    public function remove($id, ManagerRegistry $doctrine, FileUploader $fileUploader)
    {
        if($id){
            $em = $doctrine->getManager();
            $product = $em->getRepository(Product::class)->find($id);

            if (!$product) {
                throw $this->createNotFoundException('No Product found for id '.$id);
            }

            $doctrine->getRepository(Product::class)->remove($product);
            $em->flush();

            $fileUploader->remove($product->getPicture());     
        }
        return $this->redirectToRoute('app_product', [], Response::HTTP_SEE_OTHER);
    }
}
