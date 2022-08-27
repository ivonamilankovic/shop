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
        $products = $productRepository->findAll();

        return $this->render('products/all.html.twig',[
            'products' => $products
        ]);
    }


}