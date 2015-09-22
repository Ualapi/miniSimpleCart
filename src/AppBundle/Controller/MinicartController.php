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
            $cart[$product->getId()] = ['product' => $product, 'quantity' => $form->get('quantity')->getData()];
            $this->get('session')->set('cart', $cart);
        }

/*
        $total = $this->getTotal($this->get('session')->get('cart'));

        $this->get('session')->set('total', $total);
*/
        return $this->redirectToRoute('app_index');
    }

    public function checkoutAction()
    {

    }

    public function updateAction(Request $request)
    {
        $cart = $this->get('session')->get('cart');

        $arrayKeys = $request->request->all()['form'];

        foreach( $arrayKeys as $key => $item ) {
            if(is_integer($key)) {
                $cart[$key]['quantity'] = $item;
            }
        }
        
        $this->get('session')->set('cart', $cart);

        $total = $this->getTotal($this->get('session')->get('cart'));
        $this->get('session')->set('total', $total);

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

    private function getTotal(array $carts)
    {
        $total = 0;

        foreach($carts as $item){
            $total += $item['quantity'];
        }

        return $total;
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
