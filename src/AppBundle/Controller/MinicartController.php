<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Request;

class MinicartController extends Controller
{
    public function indexAction()
    {
        if (count($this->get('session')->get('cart')) > 0) {

        }

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

        $form = $this->getFormProducts(
            $product = $this->getDoctrine()->getManager()->getRepository('AppBundle:Product')->findOneBy(['id' => $id])
        );

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

        $cart[$product->getId()] = $product;
        $this->get('session')->set('cart', $cart);
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

    private function getFormProducts($products)
    {
        $formProducts = [];
        foreach ($products  as $key => $product) {
            $formProducts[$key] = $this->createFormBuilder()
                                            ->setAction($this->generateUrl('app_add_product', ['id' => $product->getId()]))
                                            ->add('quantity', 'integer')
                                            ->add('add', 'submit', ['label' => 'add'])
                                            ->getForm()
            ;
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
}
