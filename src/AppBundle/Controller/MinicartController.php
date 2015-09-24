<?php

namespace AppBundle\Controller;

use AppBundle\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MinicartController extends Controller
{
    public function indexAction()
    {
        $cart = $this->get('session')->has('cart') ? $this->get('session')->get('cart') : [];

        return $this->render(
            'AppBundle:app:index.html.twig',
            [
                'formsCart' => $this->getFormCart($cart)->createView(),
                'formProducts' => $this->getFormProductsViews($this->getFormProducts($this->getDoctrine()->getManager()->getRepository('AppBundle:Product')->findAll())),
            ]
        );
    }

    public function addAction(Request $request, $id)
    {
        $product = $this->getDoctrine()->getManager()->getRepository('AppBundle:Product')->find($id);

        $form = $this->createForm(new ProductType(), $product);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $cart = $this->get('session')->has('cart') ? $this->get('session')->get('cart') : [];
            if($form->get('quantity')->getData() != 0){
                $cart[$product->getId()] = ['product' => $product, 'quantity' => $form->get('quantity')->getData()];
            }
            $this->get('session')->set('cart', $cart);
        }
        return $this->redirectToRoute('app_index');
    }

    public function checkoutAction()
    {
        // todo your checkout method
    }

    public function updatequantitycartAction(Request $request)
    {
        $cart = $this->get('session')->get('cart');

        $formCart = $this->getFormCart($cart);

        $formCart->handleRequest($request);

        if ($formCart->isValid()) {
            foreach ($formCart->getData() as $productId => $product) {
                if($formCart->get($productId)->get('quantity')->getData() == 0) {
                    unset($cart[$productId]);
                }
                else{
                    $cart [$productId]['quantity'] = $formCart->get($productId)->get('quantity')->getData();
                }
            }
            $this->get('session')->set('cart', $cart);
        }

        return $this->redirectToRoute('app_index');
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
            $formProducts[$key] = $this->createForm(new ProductType(), $product);
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

    private function getFormCart($cart)
    {
        $builder = $this->createFormBuilder();

        foreach ($cart as $productId => $cartProduct) {
            $builder->add($productId, new ProductType($cartProduct['quantity']), ['data' => $cartProduct['product']]);
        }

        return $builder->getForm();
    }
}
