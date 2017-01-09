<?php
/*
 * This file is part of the Coupon plugin
 *
 * Copyright (C) 2016 LOCKON CO.,LTD. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\Coupon\Repository;

use Doctrine\ORM\EntityRepository;
use Eccube\Application;
use Eccube\Entity\Customer;
use Eccube\Common\Constant;
use Plugin\Coupon\Service\CouponService;

/**
 * CouponRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CouponRepository extends EntityRepository
{
    /**
     * 有効なクーポンを1件取得する.
     *
     * @param $couponCd
     *
     * @return $result
     */
    public function findActiveCoupon($couponCd)
    {
        $currenDateTime = new \DateTime();

        // 時分秒を0に設定する
        $currenDateTime->setTime(0, 0, 0);

        $qb = $this->createQueryBuilder('c')->setMaxResults(1)->select('c')->Where('c.del_flg = 0');

        // クーポンコード
        $qb->andWhere('c.coupon_cd = :coupon_cd')
            ->setParameter('coupon_cd', $couponCd);

        // クーポンコード有効
        $qb->andWhere('c.enable_flag = :enable_flag')
            ->setParameter('enable_flag', Constant::ENABLED);

        // 有効期限内 or 無期限
        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->between(':cur_date_time', 'c.available_from_date', 'c.available_to_date'),
                $qb->expr()->eq('c.no_expire_date', ':no_expire_date')
            )
        )
        ->setParameter('cur_date_time', $currenDateTime)
        ->setParameter('no_expire_date', Constant::ENABLED);
        
        // 実行
        $result = null;
        $results = $qb->getQuery()->getResult();
        if (!is_null($results) && count($results) > 0) {
            $result = $results[0];
        }

        return $result;
    }

    /**
     * 有効なクーポンを全取得する.
     *
     * @return array
     */
    public function findActiveCouponAll()
    {
        $currenDateTime = new \DateTime();

        // 時分秒を0に設定する
        $currenDateTime->setTime(0, 0, 0);

        $qb = $this->createQueryBuilder('c')->select('c')->Where('c.del_flg = 0');

        // クーポンコード有効
        $qb->andWhere('c.enable_flag = :enable_flag')
            ->setParameter('enable_flag', Constant::ENABLED);

        // 有効期間(FROM)
        $qb->andWhere('c.available_from_date <= :cur_date_time OR c.available_from_date IS NULL')
            ->setParameter('cur_date_time', $currenDateTime);

        // 有効期間(TO)
        $qb->andWhere(':cur_date_time <= c.available_to_date OR c.available_to_date IS NULL')
            ->setParameter('cur_date_time', $currenDateTime);

        // 実行
        return $qb->getQuery()->getResult();
    }
    
    /**
     * 会員が選択可能なクーポンを全取得する.
     *
     * @return array
     */
    public function findSelectableCouponAllByCustomer(Application $app, Customer $Customer)
    {
        // 有効なクーポンを取得
        $ActiveCoupons = $this->findActiveCouponAll();
        
        // 利用条件で絞り込み
        $ActiveCouponsByCustomer = array();
        foreach ($ActiveCoupons as $Coupon) {
            $isAvailable = $app['eccube.plugin.coupon.service.coupon']->checkCouponAvailableCondition($Coupon->getCouponCd(), $Customer);
            if (!$isAvailable) {
                continue;
            }
            if (!$Coupon->getSelectable()) {
               continue;
            }
            $ActiveCouponsByCustomer[] = $Coupon;
        }

        return $ActiveCouponsByCustomer;
    }
    
    /**
     * ゲストが選択可能なクーポンを全取得する.
     *
     * @return array
     */
    public function findSelectableCouponAllByGuest()
    {
        // 有効なクーポンを取得
        $ActiveCoupons = $this->findActiveCouponAll();
        
        // 利用条件で絞り込み
        $SelectableCoupon = array();
        foreach ($ActiveCoupons as $Coupon) {
            if ($Coupon->getCouponMember()) {
                continue;
            }
            if (!$Coupon->getSelectable()) {
               continue;
            }
            $SelectableCoupon[] = $Coupon;
        }

        return $SelectableCoupon;
    }
}
