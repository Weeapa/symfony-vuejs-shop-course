<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\EditProductFormType;
use http\Env\Request;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Config\Doctrine\Orm\EntityManagerConfig;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use function Sodium\add;


class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $productList = $entityManager->getRepository(Product::class)->findAll();
        dd($productList);
        return $this->render('main/default/index.html.twig', []);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @Route ("/edit-product/{id}", methods="GET|POST", name="product_edit", requirements = {"id"="\d+"})
      * @Route ("/add-product", methods="GET|POST", name="product-add")
     */
   public function editProduct(\Symfony\Component\HttpFoundation\Request $request, int $id = null,ManagerRegistry $doctrine) : Response
   {
       $entityManager = $doctrine->getManager();
       if ($id){
           $product = $entityManager->getRepository(Product::class)->find($id);
       } else{
           $product = new Product();
       }
       $form = $this->createForm(EditProductFormType::class, $product);
//dump($product->getTitle());
       $form->handleRequest($request);
       if ($form->isSubmitted() && $form->isValid()){
//           $data = $form->getData();
//
           $entityManager->persist($product);
           $entityManager->flush();

           return $this->redirectToRoute('product_edit', ['id'=> $product->getId()]);
       }

      // dd($product,$from);
       return $this->render('main/default/edit_product.html.twig', [
           'form' => $form->createView()
       ]);
   }
}
