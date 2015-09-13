<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MinicartController extends Controller
{
    public function indexAction()
    {
        return $this->render(
            'AppBundle:app:index.html.twig',
            array(
                'products' => $this->getDoctrine()->getManager()->getRepository('AppBundle:Product')->findAll(),
            )
        );
    }

    public function addAction()
    {

    }

    public function checkoutAction()
    {

    }

    public function updateAction()
    {

    }

    public function showAction(Product $product)
    {
        return $this->render(
            'app/show.html.twig',
            array(
                'product' => $product
            )
        );
    }
}
