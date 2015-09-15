<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MinicartController extends Controller
{
    public function indexAction()
    {
        return $this->render(
            'AppBundle:app:index.html.twig',
            [
                'products' => $products = $this->getDoctrine()->getManager()->getRepository('AppBundle:Product')->findAll(),
                'formProducts' => $this->getFormProductsViews($this->getFormProducts($products)),
            ]
        );
    }

    public function addAction(Request $request, $id)
    {
        $cart = $this->get('session')->has('cart') ? $this->get('session')->get('cart') : [];

        $product = $this->getDoctrine()->getManager()->getRepository('AppBundle:Product')->findOneBy(['id' => $id]);

        $form = $this->getForm($product);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $cart[$product->getId()] =
                [
                    'name' => $product->getName(),
                    'price' => $product->getPrice(),
                    'quantity' => $form->get('quantity')->getData()
                ]
            ;
        }

        $this->get('session')->set('cart', $cart);

        return $this->render(
            'AppBundle:app:index.html.twig',
            [
                'products' => $products = $this->getDoctrine()->getManager()->getRepository('AppBundle:Product')->findAll(),
                'formProducts' => $this->getFormProductsViews($this->getFormProducts($products)),
            ]
        );
    }

    public function checkoutAction()
    {

    }

    public function updateAction()
    {

    }

    private function getFormProducts($products)
    {
        $formProducts = [];
        foreach ($products  as $key => $product) {
            $formProducts[$key] = $this->getForm($product);
        }
        return $formProducts;
    }

    private function getFormProductsViews($formProducts)
    {
        $formProductsViews = [];
        foreach ($formProducts  as $key => $form) {
            $formProductsViews[$key] = $form->createView();
        }
        return $formProductsViews;
    }

    private function getForm($product)
    {
        return $this->createFormBuilder()
                                   ->setAction($this->generateUrl('app_add_product', ['id' => $product->getId()]))
                                   ->add('quantity', 'integer', ['attr' => ['min' => 0, 'max' => 99, 'value' => 0], 'label' => false])
                                   ->add('add', 'submit', ['label' => 'Add to cart'])
                                   ->getForm()
        ;
    }
}
