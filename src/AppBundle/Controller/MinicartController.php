<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MinicartController extends Controller
{
    public function indexAction()
    {
        if (count($this->get('session')->get('cart_products')) > 0) {

        }

        return $this->render(
            'AppBundle:app:index.html.twig',
            array(
                'products' => $products = $this->getDoctrine()->getManager()->getRepository('AppBundle:Product')->findAll(),
                'formProducts' => $this->getFormProductsViews($products),
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

    private function getFormProductsViews($products)
    {
        foreach ($products  as $key => $product) {
            $formProductsViews[$key] = $this->createFormBuilder()
                                       ->setAction('asdf')
                                       ->add('quantity', 'integer')
                                       ->add('add', 'submit', array('label' => 'add'))
                                       ->getForm()
                                       ->createView();
        }
        return $formProductsViews;
    }
}
