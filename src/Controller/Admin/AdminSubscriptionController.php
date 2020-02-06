<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Entity\Subscription;
use App\Entity\User;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminSubscriptionController extends AbstractController
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
     * @Route("/admin/subscription", name="admin.subscriptions.list")
     */
    public function list()
    {
        $subscriptions = $this->em->getRepository(Subscription::class)->findAll();
        return $this->render('admin/subscription/list.html.twig', [
            'subscriptions' => $subscriptions,
        ]);
    }
}
