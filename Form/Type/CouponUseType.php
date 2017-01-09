<?php
/*
 * This file is part of the Coupon plugin
 *
 * Copyright (C) 2016 LOCKON CO.,LTD. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\Coupon\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

/**
 * Class CouponUseType.
 */
class CouponUseType extends AbstractType
{
    /**
     * buildForm.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $Coupons = $options['coupons'];
        
        $builder
            ->add('coupon_cd', 'text', array(
                'label' => 'クーポンコード',
                'required' => false,
                'trim' => true,
                'mapped' => false,
            ))
            ->add('coupon_use', 'choice', array(
                'choices' => array(1 => 'クーポンを利用する。', 0 => 'クーポンを利用しない。'),
                'required' => true,
                'expanded' => true,
                'multiple' => false,
                'label' => '',
                'data' => 1,
                'empty_value' => false,
            ))
            ->add('coupon_select', 'entity', array(
                'required' => false,
                'expanded' => false,
                'multiple' => false,
                'empty_value' => '利用するクーポンを選択して下さい',
                'empty_data'  => null,
                'class' => 'Plugin\Coupon\Entity\Coupon',
                'choices' => $Coupons,
                ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'coupons' => array(),
        ));
    }

    /**
     * getName.
     *
     * @return string
     */
    public function getName()
    {
        return 'front_plugin_coupon_shopping';
    }
}
