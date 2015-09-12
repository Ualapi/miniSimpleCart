<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $products = $this->getDoctrine()->getManager()->getRepository('AppBundle:Product')->findAll();

        return $this->render('app/index.html.twig', array(
            'products' => $products
        ));
    }

    /**
     * @Route("/show/{id}", name="show-product")
     */
    public function showAction(Product $product)
    {
        return $this->render('app/show.html.twig', array(
            'product' => $product
        ));
    }
}
