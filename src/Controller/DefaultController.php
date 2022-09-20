<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $productList = $entityManager->getRepository(Product::class)->findAll();
        dd($productList);
        return $this->render('main/default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }


    #[Route('/product-edit/{id}', name: 'product.edit',methods: ["get|post"],requirements: ["id"=>'\d+'])]
    #[Route('/product-add', name: 'product.add',methods: ["get|post"])]
    public function editProduct(Request $request,int $id = null):Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        if ($id){
            $product = $entityManager->getRepository(Product::class)->find($id);
        } else {
            $product = new Product();
        }
        $form = $this->createFormBuilder($product)->add("title",TextType::class)->getForm();
        //dd($product,$form);
        $form->handleRequest($request);
        if ($form->isSubmitted()&& $form->isValid()){
            $data = $form->getData();
            $entityManager->persist($product);
            $entityManager->flush();
            return $this->redirectToRoute("product.edit",["id"=> $product->getId()]);
        }
        return $this->render('main/default/edit_product.html.twig', [
            "form" => $form->createView()
        ]);
    }
}

