<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Form\QuantityProductType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MinicartController extends Controller
{
    public function indexAction()
    {
        dump($this->get('session')->get('cart'));
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

        $form = $this->createForm(new QuantityProductType());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $cart[$product->getId()] =
                [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'price' => $product->getPrice(),
                    'quantity' => $form->get('quantity')->getData()
                ]
            ;
        }

        $this->get('session')->set('cart', $cart);

        dump($this->get('session')->get('cart'));

        $total = $this->getTotal($this->get('session')->get('cart'));
        $this->get('session')->set('total', $total);

        return $this->render(
            'AppBundle:app:index.html.twig',
            [
                'products' => $products = $this->getDoctrine()->getManager()->getRepository('AppBundle:Product')->findAll(),
                'formProducts' => $this->getFormProductsViews($this->getFormProducts($products)),
                'formProductsCart' => $this->getFormProductsViews($this->getFormProducts($this->get('session')->get('cart')))
            ]
        );
    }

    public function checkoutAction()
    {

    }

    public function updateAction()
    {

    }

    public function emptyCartAction()
    {
        $this->get('session')->clear();

        return $this->redirectToRoute('app_index');
    }

    private function getFormProducts($products)
    {
        $formProducts = [];
        foreach ($products  as $key => $product) {
            $formProducts[$key] = $this->createForm(new QuantityProductType());
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

    private function getTotal(array $carts)
    {
        $total = 0;

        foreach($carts as $item){
            $total += $item['quantity'];
        }

        return $total;
    }
}
