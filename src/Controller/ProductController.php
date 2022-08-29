<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    /**
     * @Route("/products", name="app_products")
     */
    public function products(ProductRepository $productRepository):Response
    {
        $products = $productRepository->findBy([],['createdAt' => 'DESC']);

        return $this->render('products/all.html.twig',[
            'products' => $products
        ]);
    }

    /**
     * @Route("/products/{id}", name="app_products_one")
     */
    public function oneProduct(ProductRepository $productRepository, int $id):Response
    {
        $product = $productRepository->findOneBy(['id'=>$id]);

        return $this->render('products/one.html.twig',[
            'product' => $product
        ]);
    }

}