<?php

namespace AppBundle\Controller;

use AppBundle\Form\QuantityProductType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Product;

class MinicartController extends Controller
{
    public function indexAction()
    {
        $cart = $this->get('session')->has('cart') ? $this->get('session')->get('cart') : [];

        if(!empty($cart)) {
            $builder = $this->createFormBuilder();

            foreach ($cart as $key => $item) {
                $builder->add($key, 'integer', ['attr' => ['min' => 1, 'max' => 99, 'value' => $item['quantity'] ], 'label' => false, 'mapped' => false]);
            }
            $form = $builder->getForm();
        }
        
        return $this->render(
            'AppBundle:app:index.html.twig',
            [
                'products' => $products = $this->getDoctrine()->getManager()->getRepository('AppBundle:Product')->findAll(),
                'formProducts' => $this->getFormProductsViews($this->getFormProducts($products)),
                'form' =>
                    empty(!$cart) ? $form->createView() : null
            ]
        );
    }

    public function addAction(Request $request, $id)
    {
        $cart = $this->get('session')->has('cart') ? $this->get('session')->get('cart') : [];
        /** @var Product $product */
        $product = $this->getDoctrine()->getManager()->getRepository('AppBundle:Product')->findOneBy(['id' => $id]);

        $form = $this->createForm(new QuantityProductType());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

        $total = $this->getTotal($this->get('session')->get('cart'));
        $this->get('session')->set('total', $total);

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
