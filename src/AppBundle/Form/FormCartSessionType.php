<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class FormCartSessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options["data"] as $key => $item) {
          $builder->add($key, 'integer', ['attr' => ['min' => 1, 'max' => 99, 'value' => $item['quantity'] ], 'label' => false, 'mapped' => false]);
        }
    }

    public function getName()
    {
        return 'app_bundle_form_cart_session_type';
    }
}
