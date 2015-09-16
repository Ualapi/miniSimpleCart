<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class QuantityProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', 'integer', ['attr' => ['min' => 0, 'max' => 99, 'value' => 0], 'label' => false])
            ->add('id', 'hidden')
        ;
    }

    public function getName()
    {
        return 'app_bundle_quantity_product_type';
    }
}