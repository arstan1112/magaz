<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @param ObjectManager $manager
     *
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $grocery = new Category();
        $grocery->setName('grocery');
        $grocery->setCreatedAt(new \DateTime());
        $grocery->addShop($manager->merge($this->getReference('shopA')));

        $clothes = new Category();
        $clothes->setName('clothes');
        $clothes->setCreatedAt(new \DateTime());
        $clothes->addShop($manager->merge($this->getReference('shopA')));

        $house = new Category();
        $house->setName('house');
        $house->setCreatedAt(new \DateTime());
        $house->addShop($manager->merge($this->getReference('shopA')));

        $manager->persist($grocery);
        $manager->persist($clothes);
        $manager->persist($house);

        $manager->flush();

        $this->addReference('grocery-category', $grocery);
        $this->addReference('clothes-category', $clothes);
        $this->addReference('house-category', $house);
    }

    /**
     * @inheritDoc
     */
    public function getDependencies(): array
    {
        return [
          ShopFixtures::class,
        ];
    }
}
