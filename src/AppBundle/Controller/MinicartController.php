<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MinicartController extends Controller
{
    public function indexAction()
    {
        $products = $this->getDoctrine()->getManager()->getRepository('AppBundle:Product')->findAll();

        return $this->render('app/index.html.twig', array(
            'products' => $products
        ));
    }

    public function showAction(Product $product)
    {
        return $this->render('app/show.html.twig', array(
            'product' => $product
        ));
    }
}
