<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Entity\User;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminProductController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/admin/product", name="admin.products.list")
     */
    public function list()
    {
        $products = $this->em->getRepository(Product::class)->findAllDesc();
        return $this->render('admin/product/index.html.twig', [
            'products' => $products,
            'controller_name' => 'AdminProductController',
        ]);
    }

    /**
     * @Route("/admin/product/create", name="admin.product.create")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($product);
            $this->em->flush();

            return $this->redirectToRoute('admin.products.list');
//            dump($product);
//            die();
        }

        return $this->render('admin/product/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }


//    /**
//     * @Route("/attribute")
//     */
//    public function attr()
//    {
//        $products = $this->em->getRepository(User::class)->find(1);
//        $roles[] = 'ROLE_ADMIN';
//        $products->setRoles($roles);
//        $this->em->persist($products);
//        $this->em->flush();
//        dump($products);
//        die();
//    }
}
