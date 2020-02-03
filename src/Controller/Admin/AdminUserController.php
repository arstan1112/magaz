<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminUserController extends AbstractController
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
     * @Route("/admin/user", name="admin.users.list")
     */
    public function list()
    {
        $users = $this->em->getRepository(User::class)->findAll();
        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
        ]);
    }
}
