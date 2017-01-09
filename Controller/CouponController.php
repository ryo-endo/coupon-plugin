<?php
/*
 * This file is part of the Coupon plugin
 *
 * Copyright (C) 2016 LOCKON CO.,LTD. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\Coupon\Controller;

use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\Customer;
use Eccube\Entity\Order;
use Eccube\Entity\Shipping;
use Plugin\Coupon\Entity\Coupon;
use Plugin\Coupon\Entity\CouponAvailableCondition;
use Plugin\Coupon\Service\CouponService;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CouponController.
 */
class CouponController
{
    /**
     * @var string 非会員用セッションキー
     */
    private $sessionKey = 'eccube.front.shopping.nonmember';

    /**
     * クーポン設定画面表示.
     *
     * @param Application $app
     * @param Request     $request
     *
     * @return Response
     */
    public function index(Application $app, Request $request)
    {
        // クーポン削除時のtokenで使用
        $searchForm = $app['form.factory']->createBuilder('admin_plugin_coupon_search')->getForm();
        $pagination = $app['eccube.plugin.coupon.repository.coupon']->findBy(
            array(),
            array('id' => 'DESC')
        );

        return $app->render('Coupon/Resource/template/admin/index.twig', array(
            'searchForm' => $searchForm->createView(),
            'pagination' => $pagination,
            'totalItemCount' => count($pagination),
        ));
    }

    /**
     * クーポンの新規作成/編集確定.
     *
     * @param Application $app
     * @param Request     $request
     * @param int         $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function edit(Application $app, Request $request, $id = null)
    {
        $Coupon = null;
        $CouponAvailableCondition = null;
        if (!$id) {
            // 新規登録
            $Coupon = new Coupon();
            $Coupon->setEnableFlag(Constant::ENABLED);
            $Coupon->setDelFlg(Constant::DISABLED);
            
            // 利用条件
            $CouponAvailableCondition = new CouponAvailableCondition();
            $CouponAvailableCondition->setCoupon($Coupon);
        } else {
            // 更新
            $Coupon = $app['eccube.plugin.coupon.repository.coupon']->find($id);
            if (!$Coupon) {
                $app->addError('admin.plugin.coupon.notfound', 'admin');

                return $app->redirect($app->url('plugin_coupon_list'));
            }
            
            //利用条件
            $CouponAvailableCondition = $Coupon->getCouponAvailableCondition();
        }

        $form = $app['form.factory']->createBuilder('admin_plugin_coupon', $Coupon)->getForm();
        // クーポンコードの発行
        if (!$id) {
            $form->get('coupon_cd')->setData($app['eccube.plugin.coupon.service.coupon']->generateCouponCd());
        }
        $details = array();
        $CouponDetails = $Coupon->getCouponDetails();
        foreach ($CouponDetails as $CouponDetail) {
            $details[] = clone $CouponDetail;
            $CouponDetail->getCategoryFullName();
        }
        $form->get('CouponDetails')->setData($details);
        $form->handleRequest($request);
        
        // 利用条件フォーム
        $searchForm = $app['form.factory']->createBuilder('coupon_available_condition', $CouponAvailableCondition)->getForm();
        $searchForm->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid() && $searchForm->isSubmitted() && $searchForm->isValid()) {
            /** @var \Plugin\Coupon\Entity\Coupon $Coupon */
            $Coupon = $form->getData();
            $oldReleaseNumber = $request->get('coupon_release_old');
            if (!$oldReleaseNumber) {
                $Coupon->setCouponUseTime($Coupon->getCouponRelease());
            } else {
                if ($Coupon->getCouponRelease() != $oldReleaseNumber) {
                    $Coupon->setCouponUseTime($Coupon->getCouponRelease());
                }
            }

            $CouponDetails = $app['eccube.plugin.coupon.repository.coupon_detail']->findBy(array(
                'Coupon' => $Coupon,
            ));
            foreach ($CouponDetails as $CouponDetail) {
                $Coupon->removeCouponDetail($CouponDetail);
                $app['orm.em']->remove($CouponDetail);
                $app['orm.em']->flush($CouponDetail);
            }
            $CouponDetails = $form->get('CouponDetails')->getData();
            foreach ($CouponDetails as $CouponDetail) {
                $CouponDetail->setCoupon($Coupon);
                $CouponDetail->setCouponType($Coupon->getCouponType());
                $CouponDetail->setDelFlg(Constant::DISABLED);
                $Coupon->addCouponDetail($CouponDetail);
                $app['orm.em']->persist($CouponDetail);
            }
            $app['orm.em']->persist($Coupon);
            
            // 利用条件
            $CouponAvailableCondition = $searchForm->getData();
            $CouponAvailableCondition->setCoupon($Coupon);
            $app['orm.em']->persist($CouponAvailableCondition);
            
            // 保存
            $app['orm.em']->flush($Coupon);
            $app['orm.em']->flush($CouponAvailableCondition);
            
            // 成功時のメッセージを登録する
            $app->addSuccess('admin.plugin.coupon.regist.success', 'admin');

            return $app->redirect($app->url('plugin_coupon_list'));
        }

        return $this->renderRegistView($app, array(
            'searchForm' => $searchForm->createView(),
            'form' => $form->createView(),
            'id' => $id,
        ));
    }

    /**
     * クーポンの有効/無効化.
     *
     * @param Application $app
     * @param Request     $request
     * @param int         $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function enable(Application $app, Request $request, $id)
    {
        $coupon = $app['eccube.plugin.coupon.repository.coupon']->find($id);
        if (!$coupon) {
            $app->addError('admin.plugin.coupon.notfound', 'admin');

            return $app->redirect($app->url('plugin_coupon_list'));
        }
        // =============
        // 更新処理
        // =============
        $status = $app['eccube.plugin.coupon.service.coupon']->enableCoupon($id);
        if ($status) {
            $app->addSuccess('admin.plugin.coupon.enable.success', 'admin');
        } else {
            $app->addError('admin.plugin.coupon.notfound', 'admin');
        }

        return $app->redirect($app->url('plugin_coupon_list'));
    }

    /**
     * クーポンの削除.
     *
     * @param Application $app
     * @param Request     $request
     * @param int         $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Application $app, Request $request, $id)
    {
        $coupon = $app['eccube.plugin.coupon.repository.coupon']->find($id);
        if (!$coupon) {
            $app->addError('admin.plugin.coupon.notfound', 'admin');

            return $app->redirect($app->url('plugin_coupon_list'));
        }
        // クーポン削除時のtokenで使用
        $form = $app['form.factory']->createBuilder('admin_plugin_coupon_search')->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $service = $app['eccube.plugin.coupon.service.coupon'];
            // クーポン情報を削除する
            if ($service->deleteCoupon($id)) {
                $app->addSuccess('admin.plugin.coupon.delete.success', 'admin');
            } else {
                $app->addError('admin.plugin.coupon.notfound', 'admin');
            }
        } else {
            $app->addError('admin.plugin.coupon.delete.error', 'admin');
        }

        return $app->redirect($app->url('plugin_coupon_list'));
    }

    /**
     * 編集画面用のrender.
     *
     * @param Application $app
     * @param array       $parameters
     *
     * @return Response
     */
    protected function renderRegistView(Application $app, $parameters = array())
    {
        // 商品検索フォーム
        $searchProductModalForm = $app['form.factory']->createBuilder('admin_search_product')->getForm();
        // カテゴリ検索フォーム
        $searchCategoryModalForm = $app['form.factory']->createBuilder('admin_plugin_coupon_search_category')->getForm();
        $viewParameters = array(
            'searchProductModalForm' => $searchProductModalForm->createView(),
            'searchCategoryModalForm' => $searchCategoryModalForm->createView(),
        );
        $viewParameters += $parameters;

        return $app->render('Coupon/Resource/template/admin/regist.twig', $viewParameters);
    }

    /**
     * クーポン入力、登録画面.
     *
     * @param Application $app
     * @param Request     $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function shoppingCoupon(Application $app, Request $request)
    {
        // カートチェック
        if (!$app['eccube.service.cart']->isLocked()) {
            // カートが存在しない、カートがロックされていない時はエラー
            return $app->redirect($app->url('cart'));
        }
        $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);
        if (!$Order) {
            $app->addError('front.shopping.order.error');

            return $app->redirect($app->url('shopping_error'));
        }
        
        // 利用可能なクーポン一覧
        $Coupons = null;
        if ($app->isGranted('ROLE_USER')) {
            $Coupons = $app['eccube.plugin.coupon.repository.coupon']->findSelectableCouponAllByCustomer($app, $app->user());
        } else {
            $Coupons = $app['eccube.plugin.coupon.repository.coupon']->findSelectableCouponAllByGuest();
        }
        
        $form = $app['form.factory']->createBuilder('front_plugin_coupon_shopping', null, array('coupons' => $Coupons))->getForm();
        
        // 利用中のクーポンコードがある場合はフォームに設定する
        $CouponOrder = $app['eccube.plugin.coupon.service.coupon']->getCouponOrder($Order->getPreOrderId());
        $couponCd = null;
        if ($CouponOrder) {
            $couponCd = $CouponOrder->getCouponCd();
            $selectedCoupon = $app['eccube.plugin.coupon.repository.coupon']->findOneBy(array('coupon_cd' => $couponCd));
            
            // クーポンコードがクーポン一覧にあるか？
            $fromChoices = $form->get('coupon_select')->getConfig()->getOption('choices');
            $hasChoice = false;
            foreach ($fromChoices as $Choice) {
                if ($Choice->getCouponCd() == $couponCd) {
                    $hasChoice = true;
                    break;
                }
            }
            
            // フォームに設定する
            if ($hasChoice) {
                $form->get('coupon_select')->setData($selectedCoupon);
            } else {
                $form->get('coupon_cd')->setData($couponCd);
            }
        }
        
        
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // サービスの取得
            /* @var $service CouponService */
            $service = $app['eccube.plugin.coupon.service.coupon'];
            if (!is_null($form->get('coupon_select')->getData())) {
                $formCouponCd = $form->get('coupon_select')->getData()->getCouponCd();
            } else {
                $formCouponCd = $form->get('coupon_cd')->getData();
            }
            $formCouponCancel = $form->get('coupon_use')->getData();
            // ---------------------------------
            // クーポンコード入力項目追加
            // ----------------------------------
            if ($formCouponCd == $couponCd) {
                // 画面上のクーポンコードと既に登録済みのクーポンコードが同一の場合、何もしない
                //return $app->redirect($app->url('shopping'));
            }

            if ($formCouponCancel == 0 || empty($formCouponCd)) {
                // クーポンを利用しない OR クーポンの入力がない場合は、Orderからクーポンを削除
                $this->removeCouponOrder($Order, $app);

                return $app->redirect($app->url('shopping'));
            } else {
                // クーポンコードが入力されている
                $discount = 0;
                $error = false;
                // クーポン情報を取得
                /* @var $Coupon Coupon */
                $Coupon = $app['eccube.plugin.coupon.repository.coupon']->findActiveCoupon($formCouponCd);
                if ($app->isGranted('ROLE_USER')) {
                    $Customer = $app->user();
                } else {
                    $Customer = $app['eccube.service.shopping']->getNonMember($this->sessionKey);
                    if ($Coupon) {
                        if ($Coupon->getCouponMember()) {
                            $form->get('coupon_cd')->addError(new FormError('front.plugin.coupon.shopping.member'));
                            $error = true;
                        }
                    }
                }
                if ($Coupon && !$error) {
                    $lowerLimit = $Coupon->getCouponLowerLimit();
                    // 既に登録済みのクーポンコードを一旦削除
                    $this->removeCouponOrder($Order, $app);
                    // 対象クーポンが存在しているかチェック
                    $couponProducts = $service->existsCouponProduct($Coupon, $Order);
                    $checkLowerLimit = $service->isLowerLimitCoupon($couponProducts, $lowerLimit);
                    // 値引き額を取得
                    $discount = $service->recalcOrder($Order, $Coupon, $couponProducts);
                    if (sizeof($couponProducts) == 0) {
                        $existCoupon = false;
                    } else {
                        $existCoupon = true;
                    }

                    if (!$existCoupon) {
                        $form->get('coupon_cd')->addError(new FormError('front.plugin.coupon.shopping.notexists'));
                        $error = true;
                    }

                    if (!$checkLowerLimit) {
                        $form->get('coupon_cd')->addError(new FormError('front.plugin.coupon.shopping.lowerlimit'));
                        $error = true;
                    }

                    // クーポンの利用条件に当てはまるかのチェック
                    if ($app->isGranted('ROLE_USER')) {
                        $isAvailable = $service->checkCouponAvailableCondition($formCouponCd, $Customer);
                        if (!$isAvailable && $existCoupon) {
                            $form->get('coupon_cd')->addError(new FormError('front.plugin.coupon.shopping.notavailable'));
                            $error = true;
                        }
                    }

                    // クーポンが既に利用されているかチェック
                    $couponUsedOrNot = $service->checkCouponUsedOrNot($formCouponCd, $Customer);
                    if ($couponUsedOrNot && $existCoupon) {
                        // 既に存在している
                        $form->get('coupon_cd')->addError(new FormError('front.plugin.coupon.shopping.sameuser'));
                        $error = true;
                    }

                    // クーポンの発行枚数チェック
                    $checkCouponUseTime = $this->checkCouponUseTime($formCouponCd, $app);
                    if (!$checkCouponUseTime && $existCoupon) {
                        $form->get('coupon_cd')->addError(new FormError('front.plugin.coupon.shopping.couponusetime'));
                        $error = true;
                    }

                    // 合計金額より値引き額の方が高いかチェック
                    if ($Order->getTotal() < $discount && $existCoupon) {
                        $form->get('coupon_cd')->addError(new FormError('front.plugin.coupon.shopping.minus'));
                        $error = true;
                    }
                } elseif (!$Coupon) {
                    $form->get('coupon_cd')->addError(new FormError('front.plugin.coupon.shopping.notexists'));
                }
                // ----------------------------------
                // 値引き項目追加 / 合計金額上書き
                // ----------------------------------
                if (!$error && $Coupon) {
                    // クーポン情報を登録
                    $this->setCouponOrder($Order, $Coupon, $formCouponCd, $Customer, $discount, $app);

                    return $app->redirect($app->url('shopping'));
                } else {
                    // エラーが発生した場合、前回設定されているクーポンがあればその金額を再設定する
                    if ($couponCd && $Coupon) {
                        // クーポン情報を取得
                        $Coupon = $app['eccube.plugin.coupon.repository.coupon']->findActiveCoupon($couponCd);
                        if ($Coupon) {
                            $couponProducts = $service->existsCouponProduct($Coupon, $Order);
                            // 値引き額を取得
                            $discount = $service->recalcOrder($Order, $Coupon, $couponProducts);
                            // クーポン情報を登録
                            $this->setCouponOrder($Order, $Coupon, $couponCd, $Customer, $discount, $app);
                        }
                    }
                }
            }
        }

        return $app->render('Coupon/Resource/template/default/shopping_coupon.twig', array(
            'form' => $form->createView(),
            'Order' => $Order,
        ));
    }

    /**
     *  save delivery.
     *
     * @param Application $app
     * @param Request     $request
     *
     * @return Response
     */
    public function saveDelivery(Application $app, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $date = explode(',', $request->get('coupon_delivery_date'));
            $time = explode(',', $request->get('coupon_delivery_time'));
            /* @var Order $Order */
            $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);
            /* @var Shipping $Shipping */
            $Shippings = $Order->getShippings();
            $index = 0;
            foreach ($Shippings as $Shipping) {
                if ($time[$index]) {
                    $DeliveryTime = $app['eccube.repository.delivery_time']->find($time[$index]);
                    $Shipping->setDeliveryTime($DeliveryTime);
                } else {
                    $Shipping->setDeliveryTime(null);
                }

                if ($date[$index]) {
                    $Shipping->setShippingDeliveryDate(new \DateTime($date[$index]));
                } else {
                    $Shipping->setShippingDeliveryDate(null);
                }

                ++$index;
                $app['orm.em']->persist($Shipping);
                $app['orm.em']->flush($Shipping);
            }
        }

        return new Response();
    }

    /**
     *  クーポンの発行枚数のチェック.
     *
     * @param int         $couponCd
     * @param Application $app
     *
     * @return bool クーポンの枚数が一枚以上の時にtrueを返す
     */
    private function checkCouponUseTime($couponCd, Application $app)
    {
        $Coupon = $app['eccube.plugin.coupon.repository.coupon']->findOneBy(array('coupon_cd' => $couponCd));
        // クーポンの発行枚数は購入完了時に減算される、一枚以上残っていれば利用できる
        return $Coupon->getCouponUseTime() >= 1;
    }

    /**
     * クーポン情報に登録.
     *
     * @param Order  $Order
     * @param Coupon $Coupon
     * @param $couponCd
     * @param Customer $Customer
     * @param $discount
     * @param Application $app
     */
    private function setCouponOrder(Order $Order, Coupon $Coupon, $couponCd, Customer $Customer, $discount, Application $app)
    {
        $total = $Order->getTotal() - $discount;
        $Order->setDiscount($Order->getDiscount() + $discount);
        $Order->setTotal($total);
        $Order->setPaymentTotal($total);
        // クーポン受注情報を保存する
        $app['eccube.plugin.coupon.service.coupon']->saveCouponOrder($Order, $Coupon, $couponCd, $Customer, $discount);
        // 合計、値引きを再計算し、dtb_orderを更新する
        $app['orm.em']->flush($Order);
    }

    /**
     * クーポンコードが未入力または、クーポンコードを登録後に再度別のクーポンコードが設定された場合、
     * 既存のクーポンを情報削除.
     *
     * @param Order       $Order
     * @param Application $app
     */
    private function removeCouponOrder(Order $Order, Application $app)
    {
        // クーポンが未入力でクーポン情報が存在すればクーポン情報を削除
        $CouponOrder = $app['eccube.plugin.coupon.service.coupon']->getCouponOrder($Order->getPreOrderId());
        if ($CouponOrder) {
            $app['orm.em']->remove($CouponOrder);
            $app['orm.em']->flush($CouponOrder);
            $Order->setDiscount($Order->getDiscount() - $CouponOrder->getDiscount());
            $Order->setTotal($Order->getTotal() + $CouponOrder->getDiscount());
            $Order->setPaymentTotal($Order->getPaymentTotal() + $CouponOrder->getDiscount());
            $app['orm.em']->flush($Order);
        }
    }
    
}
