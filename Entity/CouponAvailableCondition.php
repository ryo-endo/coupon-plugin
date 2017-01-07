<?php

namespace Plugin\Coupon\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CouponAvailableCondition
 */
class CouponAvailableCondition extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $multi;

    /**
     * @var string
     */
    private $company_name;

    /**
     * @var integer
     */
    private $birth_month;

    /**
     * @var \DateTime
     */
    private $birth_start;

    /**
     * @var \DateTime
     */
    private $birth_end;

    /**
     * @var string
     */
    private $tel01;

    /**
     * @var string
     */
    private $tel02;

    /**
     * @var string
     */
    private $tel03;

    /**
     * @var integer
     */
    private $buy_total_start;

    /**
     * @var integer
     */
    private $buy_total_end;

    /**
     * @var integer
     */
    private $buy_times_start;

    /**
     * @var integer
     */
    private $buy_times_end;

    /**
     * @var \DateTime
     */
    private $create_date_start;

    /**
     * @var \DateTime
     */
    private $create_date_end;

    /**
     * @var \DateTime
     */
    private $update_date_start;

    /**
     * @var \DateTime
     */
    private $update_date_end;

    /**
     * @var \DateTime
     */
    private $last_buy_start;

    /**
     * @var \DateTime
     */
    private $last_buy_end;

    /**
     * @var string
     */
    private $buy_product_name;

    /**
     * @var string
     */
    private $buy_product_code;

    /**
     * @var \Plugin\Coupon\Entity\Coupon
     */
    private $Coupon;

    /**
     * @var \Eccube\Entity\Master\CustomerStatus
     */
    private $Status;

    /**
     * @var \Eccube\Entity\Master\Pref
     */
    private $Pref;

    /**
     * @var \Eccube\Entity\Master\Sex
     */
    private $Sex;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set multi
     *
     * @param string $multi
     * @return CouponAvailableCondition
     */
    public function setMulti($multi)
    {
        $this->multi = $multi;

        return $this;
    }

    /**
     * Get multi
     *
     * @return string 
     */
    public function getMulti()
    {
        return $this->multi;
    }

    /**
     * Set company_name
     *
     * @param string $companyName
     * @return CouponAvailableCondition
     */
    public function setCompanyName($companyName)
    {
        $this->company_name = $companyName;

        return $this;
    }

    /**
     * Get company_name
     *
     * @return string 
     */
    public function getCompanyName()
    {
        return $this->company_name;
    }

    /**
     * Set birth_month
     *
     * @param integer $birthMonth
     * @return CouponAvailableCondition
     */
    public function setBirthMonth($birthMonth)
    {
        $this->birth_month = $birthMonth;

        return $this;
    }

    /**
     * Get birth_month
     *
     * @return integer 
     */
    public function getBirthMonth()
    {
        return $this->birth_month;
    }

    /**
     * Set birth_start
     *
     * @param \DateTime $birthStart
     * @return CouponAvailableCondition
     */
    public function setBirthStart($birthStart)
    {
        $this->birth_start = $birthStart;

        return $this;
    }

    /**
     * Get birth_start
     *
     * @return \DateTime 
     */
    public function getBirthStart()
    {
        return $this->birth_start;
    }

    /**
     * Set birth_end
     *
     * @param \DateTime $birthEnd
     * @return CouponAvailableCondition
     */
    public function setBirthEnd($birthEnd)
    {
        $this->birth_end = $birthEnd;

        return $this;
    }

    /**
     * Get birth_end
     *
     * @return \DateTime 
     */
    public function getBirthEnd()
    {
        return $this->birth_end;
    }

    /**
     * Set tel01
     *
     * @param string $tel01
     * @return CouponAvailableCondition
     */
    public function setTel01($tel01)
    {
        $this->tel01 = $tel01;

        return $this;
    }

    /**
     * Get tel01
     *
     * @return string 
     */
    public function getTel01()
    {
        return $this->tel01;
    }

    /**
     * Set tel02
     *
     * @param string $tel02
     * @return CouponAvailableCondition
     */
    public function setTel02($tel02)
    {
        $this->tel02 = $tel02;

        return $this;
    }

    /**
     * Get tel02
     *
     * @return string 
     */
    public function getTel02()
    {
        return $this->tel02;
    }

    /**
     * Set tel03
     *
     * @param string $tel03
     * @return CouponAvailableCondition
     */
    public function setTel03($tel03)
    {
        $this->tel03 = $tel03;

        return $this;
    }

    /**
     * Get tel03
     *
     * @return string 
     */
    public function getTel03()
    {
        return $this->tel03;
    }

    /**
     * Set buy_total_start
     *
     * @param integer $buyTotalStart
     * @return CouponAvailableCondition
     */
    public function setBuyTotalStart($buyTotalStart)
    {
        $this->buy_total_start = $buyTotalStart;

        return $this;
    }

    /**
     * Get buy_total_start
     *
     * @return integer 
     */
    public function getBuyTotalStart()
    {
        return $this->buy_total_start;
    }

    /**
     * Set buy_total_end
     *
     * @param integer $buyTotalEnd
     * @return CouponAvailableCondition
     */
    public function setBuyTotalEnd($buyTotalEnd)
    {
        $this->buy_total_end = $buyTotalEnd;

        return $this;
    }

    /**
     * Get buy_total_end
     *
     * @return integer 
     */
    public function getBuyTotalEnd()
    {
        return $this->buy_total_end;
    }

    /**
     * Set buy_times_start
     *
     * @param integer $buyTimesStart
     * @return CouponAvailableCondition
     */
    public function setBuyTimesStart($buyTimesStart)
    {
        $this->buy_times_start = $buyTimesStart;

        return $this;
    }

    /**
     * Get buy_times_start
     *
     * @return integer 
     */
    public function getBuyTimesStart()
    {
        return $this->buy_times_start;
    }

    /**
     * Set buy_times_end
     *
     * @param integer $buyTimesEnd
     * @return CouponAvailableCondition
     */
    public function setBuyTimesEnd($buyTimesEnd)
    {
        $this->buy_times_end = $buyTimesEnd;

        return $this;
    }

    /**
     * Get buy_times_end
     *
     * @return integer 
     */
    public function getBuyTimesEnd()
    {
        return $this->buy_times_end;
    }

    /**
     * Set create_date_start
     *
     * @param \DateTime $createDateStart
     * @return CouponAvailableCondition
     */
    public function setCreateDateStart($createDateStart)
    {
        $this->create_date_start = $createDateStart;

        return $this;
    }

    /**
     * Get create_date_start
     *
     * @return \DateTime 
     */
    public function getCreateDateStart()
    {
        return $this->create_date_start;
    }

    /**
     * Set create_date_end
     *
     * @param \DateTime $createDateEnd
     * @return CouponAvailableCondition
     */
    public function setCreateDateEnd($createDateEnd)
    {
        $this->create_date_end = $createDateEnd;

        return $this;
    }

    /**
     * Get create_date_end
     *
     * @return \DateTime 
     */
    public function getCreateDateEnd()
    {
        return $this->create_date_end;
    }

    /**
     * Set update_date_start
     *
     * @param \DateTime $updateDateStart
     * @return CouponAvailableCondition
     */
    public function setUpdateDateStart($updateDateStart)
    {
        $this->update_date_start = $updateDateStart;

        return $this;
    }

    /**
     * Get update_date_start
     *
     * @return \DateTime 
     */
    public function getUpdateDateStart()
    {
        return $this->update_date_start;
    }

    /**
     * Set update_date_end
     *
     * @param \DateTime $updateDateEnd
     * @return CouponAvailableCondition
     */
    public function setUpdateDateEnd($updateDateEnd)
    {
        $this->update_date_end = $updateDateEnd;

        return $this;
    }

    /**
     * Get update_date_end
     *
     * @return \DateTime 
     */
    public function getUpdateDateEnd()
    {
        return $this->update_date_end;
    }

    /**
     * Set last_buy_start
     *
     * @param \DateTime $lastBuyStart
     * @return CouponAvailableCondition
     */
    public function setLastBuyStart($lastBuyStart)
    {
        $this->last_buy_start = $lastBuyStart;

        return $this;
    }

    /**
     * Get last_buy_start
     *
     * @return \DateTime 
     */
    public function getLastBuyStart()
    {
        return $this->last_buy_start;
    }

    /**
     * Set last_buy_end
     *
     * @param \DateTime $lastBuyEnd
     * @return CouponAvailableCondition
     */
    public function setLastBuyEnd($lastBuyEnd)
    {
        $this->last_buy_end = $lastBuyEnd;

        return $this;
    }

    /**
     * Get last_buy_end
     *
     * @return \DateTime 
     */
    public function getLastBuyEnd()
    {
        return $this->last_buy_end;
    }

    /**
     * Set buy_product_name
     *
     * @param string $buyProductName
     * @return CouponAvailableCondition
     */
    public function setBuyProductName($buyProductName)
    {
        $this->buy_product_name = $buyProductName;

        return $this;
    }

    /**
     * Get buy_product_name
     *
     * @return string 
     */
    public function getBuyProductName()
    {
        return $this->buy_product_name;
    }

    /**
     * Set buy_product_code
     *
     * @param string $buyProductCode
     * @return CouponAvailableCondition
     */
    public function setBuyProductCode($buyProductCode)
    {
        $this->buy_product_code = $buyProductCode;

        return $this;
    }

    /**
     * Get buy_product_code
     *
     * @return string 
     */
    public function getBuyProductCode()
    {
        return $this->buy_product_code;
    }

    /**
     * Set Coupon
     *
     * @param \Plugin\Coupon\Entity\Coupon $coupon
     * @return CouponAvailableCondition
     */
    public function setCoupon(\Plugin\Coupon\Entity\Coupon $coupon = null)
    {
        $this->Coupon = $coupon;

        return $this;
    }

    /**
     * Get Coupon
     *
     * @return \Plugin\Coupon\Entity\Coupon 
     */
    public function getCoupon()
    {
        return $this->Coupon;
    }

    /**
     * Set Status
     *
     * @param \Eccube\Entity\Master\CustomerStatus $status
     * @return CouponAvailableCondition
     */
    public function setStatus(\Eccube\Entity\Master\CustomerStatus $status = null)
    {
        $this->Status = $status;

        return $this;
    }

    /**
     * Get Status
     *
     * @return \Eccube\Entity\Master\CustomerStatus 
     */
    public function getStatus()
    {
        return $this->Status;
    }

    /**
     * Set Pref
     *
     * @param \Eccube\Entity\Master\Pref $pref
     * @return CouponAvailableCondition
     */
    public function setPref(\Eccube\Entity\Master\Pref $pref = null)
    {
        $this->Pref = $pref;

        return $this;
    }

    /**
     * Get Pref
     *
     * @return \Eccube\Entity\Master\Pref 
     */
    public function getPref()
    {
        return $this->Pref;
    }

    /**
     * Set Sex
     *
     * @param \Eccube\Entity\Master\Sex $sex
     * @return CouponAvailableCondition
     */
    public function setSex(\Eccube\Entity\Master\Sex $sex = null)
    {
        $this->Sex = $sex;

        return $this;
    }

    /**
     * Get Sex
     *
     * @return \Eccube\Entity\Master\Sex 
     */
    public function getSex()
    {
        return $this->Sex;
    }
}
