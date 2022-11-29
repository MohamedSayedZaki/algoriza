<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $categories = [1 => 'Clothes',2 => 'Shoes'];
        // foreach($categories as $value){
        //     $category = new Category();
        //     $category->setName($value);
        //     $category->setIsActive(true);
        //     $manager->persist($category);
        // }

        // $manager->flush();
    }
}
