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

    /**
     * AdminProductController constructor.
     *
     * @param EntityManagerInterface $em
     */
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
        return $this->render('admin/product/list.html.twig', [
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
     *
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function create(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            \Stripe\Stripe::setApiKey('sk_test_Gw22NrsxU6aIlKApdYKsXgN700f1Ww1pAc');

            $productApi = \Stripe\Product::create([
                'name' => $product->getName(),
                'type' => 'service',
            ]);

            $plan = \Stripe\Plan::create([
                'currency' => 'usd',
                'interval' => $form->get('pricingPlanInterval')->getData(),
                'product'  => $productApi->id,
                'nickname' => $form->get('pricingPlanName')->getData(),
                'amount'   => $form->get('pricingPlanAmount')->getData(),
            ]);

            $product->setPricingPlanId($plan->id);

            $this->em->persist($product);
            $this->em->flush();

            return $this->redirectToRoute('admin.products.list');
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
