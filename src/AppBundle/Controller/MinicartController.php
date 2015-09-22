<?php

namespace AppBundle\Controller;


use AppBundle\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Product;

class MinicartController extends Controller
{
    public function indexAction()
    {

        $cart = $this->get('session')->has('cart') ? $this->get('session')->get('cart') : [];

        return $this->render(
            'AppBundle:app:index.html.twig',
            [
                'cart' => $cart,
                'products' => $products = $this->getDoctrine()->getManager()->getRepository('AppBundle:Product')->findAll(),
                'formProducts' => $this->getFormProductsViews($this->getFormProducts($products)),
                'formsCart' => $this->getFormProductsViews($this->getFormCartProducts($cart)),
                'formProductsCart' => null
//                    empty(!$this->get('session')->get('cart')) ? $this->getFormProductsViews($this->getFormProducts($this->get('session')->get('cart'))) : null
            ]
        );
    }

    public function addAction(Request $request, $id)
    {
        $cart = $this->get('session')->has('cart') ? $this->get('session')->get('cart') : [];
        /** @var Product $product */
        $product = $this->getDoctrine()->getManager()->getRepository('AppBundle:Product')->find($id);

        $form = $this->createForm(new ProductType(), $product);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $cart[$product->getId()] =
                [
                    'product' => $product,
                    'quantity' => $form->get('quantity')->getData()
                ]
            ;
        }

        $this->get('session')->set('cart', $cart);
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

        foreach( $request->request->all()['app_bundle_form_cart_session_type'] as $key => $item ) {

            if(is_integer($key)) {
                $cart[$key]['quantity'] = $item;
            }
        }

        $form = $this->createForm(new FormCartSessionType(), $cart);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->get('session')->set('cart', $cart);

            $total = $this->getTotal($this->get('session')->get('cart'));
            $this->get('session')->set('total', $total);
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

    private function getTotal(array $carts)
    {
        $total = 0;

        foreach($carts as $item){
            $total += $item['quantity'];
        }

        return $total;
    }

    private function getFormCartProducts($cart)
    {
        $formCartProducts = [];
        foreach ($cart as $productId => $productsItems) {
            $formCartProducts[$productId] = $this->createForm(new ProductType($productsItems['quantity']), $productsItems['product']);
        }
        return $formCartProducts;
    }
}
