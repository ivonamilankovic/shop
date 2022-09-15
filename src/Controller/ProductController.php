<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    /**
     * @Route("/products", name="app_products")
     */
    public function products():Response
    {
        return $this->render('products/all.html.twig');
    }

    /**
     * @Route("/products/get_all", name="app_products_get_all", options={"expose"=true})
     */
    public function getProducts(ProductRepository $productRepository)
    {
        $products = $productRepository->findBy([],['createdAt' => 'DESC']);
        $serializer = $this->container->get('serializer');
        $json = $serializer->serialize($products,'json');

        return new JsonResponse($json);
    }

    /**
     * @Route("/products/get_by_name_sample", name="app_product_search_sample", options={"expose" = true})
     */
    public function searchProductsSample(ProductRepository $productRepository, Request $request)
    {
        $products = $productRepository->findBy(['name' => $request->request->get('sample')]);

        $serializer = $this->container->get('serializer');
        $json = $serializer->serialize($products,'json');

        return new JsonResponse($json);
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