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

    public function updateAction(Request $request)
    {
        $quantityProduct = $request->request->get('app_bundle_quantity_product_type');
        
        $oldProductsCartSession = $this->get('session')->get('cart');
        
        foreach( $oldProductsCartSession as $key => $item ) {

            $newProductsCartSession[$key] = $item;

            if($key == $quantityProduct['id']){
                $newProductsCartSession[$key] = [
                    "id" => $key,
                    "name" => $oldProductsCartSession[$key]['name'],
                    "price" => $oldProductsCartSession[$key]['price'],
                    "quantity" => $quantityProduct['quantity']
                ];
            }
        }
        
        $this->get('session')->set('cart', $newProductsCartSession);
        return $this->redirectToRoute('app_add_product',['id' => $quantityProduct['id']]);
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
