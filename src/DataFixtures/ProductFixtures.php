<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $categories = [1 => 'Clothes',2 => 'Shoes'];
        $category ='';
        foreach($categories as $value){
            $category = new Category();
            $category->setName($value);
            $category->setActive(true);
            $manager->persist($category);
        }        
        for ($i = 0; $i < 10; $i++) {
            $randomCategory = array_rand($categories, 1);
            $tags = $this->getTags($randomCategory);
                        
            $product = new Product();
            $product->setName('product '.$i);
            $product->setCategory($category);
            $product->setDescription('Lorem opsium add the following text');
            $product->setTags(implode(',',$tags));
            $product->setPicture('https://picsum.photos/id/237/200/300');
            $manager->persist($product);
        }

        $manager->flush();
    }

    private function getTags($category){

        switch ($category) {
            case 1:
                return ['shirt','jacket','men'];
                break;
            case 2:
                return ['heels','sneakers','men'];
                break;
            default:
                break;
        }

    }
}
