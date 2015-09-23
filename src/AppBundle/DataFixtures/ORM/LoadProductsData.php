<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Product;
use Symfony\Component\Yaml\Parser;

class LoadProductsData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $parser = new Parser();
        $products = $parser->parse(file_get_contents(__DIR__.'/../products.yml'));

        foreach($products['products'] as $item){

            $product = new Product();
            $product->setName($item[0]);
            $product->setDescription($item[1]);
            $product->setPrice($item[2]);

            $manager->persist($product);
        }

        $manager->flush();
    }
}