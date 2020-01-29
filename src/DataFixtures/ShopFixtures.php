<?php

namespace App\DataFixtures;

use App\Entity\Shop;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ShopFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
//        for ($i = 0; $i>10; $i++) {
//            $shop = new Shop();
//            $shop->setName('shop ' . $i);
//            $shop->setCreatedAt(new \DateTime());
//            $manager->persist($shop);
//        }

        $shopA = new Shop();
        $shopA->setName('shopA');
        $shopA->setCreatedAt(new \DateTime());

        $shopB = new Shop();
        $shopB->setName('shopB');
        $shopB->setCreatedAt(new \DateTime());

        $shopC = new Shop();
        $shopC->setName('shopC');
        $shopC->setCreatedAt(new \DateTime());

        $manager->persist($shopA);
        $manager->persist($shopB);
        $manager->persist($shopC);

        $manager->flush();

        $this->addReference('shopA', $shopA);
        $this->addReference('shopB', $shopB);
        $this->addReference('shopC', $shopC);
    }
}
