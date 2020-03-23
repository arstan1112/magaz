<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Exception\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="products.list")
     */
    public function list()
    {
//        $user = $this->em->getRepository(User::class)->find(4);
//        $user->setRoles(['ROLE_ADMIN']);
//        $this->em->persist($user);
//        $this->em->flush();
//        dump($user);
//        die();
//        dd(__DIR__);

        $products = $this->em->getRepository(Product::class)->findAllDesc();
        return $this->render('product/list.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/show/{id}", name = "product.show", requirements = {"id" = "\d+"})
     *
     * @param int $id
     *
     * @return Response
     */
    public function show(int $id)
    {
        $product = $this->em->getRepository(Product::class)->find($id);
//        $product->setPricingPlanId('plan_GgQebDnr5PqPVh');
//        $this->em->persist($product);
//        $this->em->flush();
//        dump($product);
//        die();

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }
}
