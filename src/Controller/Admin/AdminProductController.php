<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Entity\User;
use App\Form\ProductType;
use Doctrine\Instantiator\Exception\ExceptionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Exception\ApiErrorException;
use Stripe\{Stripe, Plan};
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
     * @throws ApiErrorException
     */
    public function create(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            Stripe::setApiKey('sk_test_Gw22NrsxU6aIlKApdYKsXgN700f1Ww1pAc');

            $productApi = \Stripe\Product::create([
                'name' => $product->getName(),
                'type' => 'service',
            ]);

            $product->setStripeId($productApi->id);

            if (
                $form->get('pricingPlanName')->getData() &&
                $form->get('pricingPlanInterval')->getData() &&
                $form->get('pricingPlanAmount')->getData()
            ) {
                $plan = Plan::create([
                    'currency' => 'usd',
                    'interval' => $form->get('pricingPlanInterval')->getData(),
                    'product'  => $productApi->id,
                    'nickname' => $form->get('pricingPlanName')->getData(),
                    'amount'   => $form->get('pricingPlanAmount')->getData(),
                ]);
                $product->setPricingPlanId($plan->id);
            }

            $this->em->persist($product);
            $this->em->flush();

            return $this->redirectToRoute('admin.products.list');
        }

        return $this->render('admin/product/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/product/{id}/delete", name="admin.product.delete", requirements={"id" = "\d+"})
     *
     * @param $id
     *
     * @return Response
     */
    public function delete($id)
    {
        $productDB = $this->em->getRepository(Product::class)->find($id);
        $productStripeId = $productDB->getStripeId();

        try {
            \Stripe\Stripe::setApiKey('sk_test_Gw22NrsxU6aIlKApdYKsXgN700f1Ww1pAc');
            $product = \Stripe\Product::retrieve(
                $productStripeId
            );
            $product->delete();
        } catch (ApiErrorException $e) {
           $this->addFlash(
                'notice',
                'Error:'. $e->getMessage()
            );
            return $this->redirectToRoute('admin.products.list');
        }

        $productDB = $this->em->getRepository(Product::class)->find($id);
        $this->em->remove($productDB);
        $this->em->flush();

        return $this->redirectToRoute('admin.products.list');
    }
}
