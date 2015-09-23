<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    private $quantity;

    public function __construct( $quantity  = 1) {
        $this->quantity = $quantity;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', 'integer', ['attr' => ['min' => 1, 'max' => 99, 'value' => $this->quantity ], 'label' => false, 'mapped' => false])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'AppBundle\Entity\Product'
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_product';
    }
}
