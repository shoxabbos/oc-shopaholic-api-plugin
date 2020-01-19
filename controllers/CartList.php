<?php namespace Shohabbos\Shopaholicapi\Controllers;

use Input;
use Lang;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

// custom classess
use Kharanenka\Helper\Result;
use Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem;
use Lovata\OrdersShopaholic\Classes\Processor\CartProcessor;
use Lovata\OrdersShopaholic\Classes\Processor\OfferCartPositionProcessor;

class CartList extends Controller
{

	/**
     * Get offers list from cart
     * @param \Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem $obShippingTypeItem
     * @return \Lovata\OrdersShopaholic\Classes\Collection\CartPositionCollection
     */
    public function get($obShippingTypeItem = null)
    {
        CartProcessor::instance()->setActiveShippingType($obShippingTypeItem);
        
        $list = CartProcessor::instance()->get();

        $data = [];
        foreach ($list as $key => $value) {
            $data[] = $value->toArray();
        }

        return $data;        
    }

	/**
     * Add product to cart
     * @return array
     */
    public function add()
    {
        $arRequestData = Input::get('cart');
        $obActiveShippingType = $this->getActiveShippingTypeFromRequest();
        if (!empty($obActiveShippingType) && $obActiveShippingType->isNotEmpty()) {
            CartProcessor::instance()->setActiveShippingType($obActiveShippingType);
        }

        CartProcessor::instance()->add($arRequestData, OfferCartPositionProcessor::class);
        Result::setData(CartProcessor::instance()->getCartData());

        return Result::get();
    }

    /**
     * Update cart
     * @return array
     */
    public function update()
    {
        $arRequestData = Input::get('cart');
        $obActiveShippingType = $this->getActiveShippingTypeFromRequest();
        if (!empty($obActiveShippingType) && $obActiveShippingType->isNotEmpty()) {
            CartProcessor::instance()->setActiveShippingType($obActiveShippingType);
        }

        CartProcessor::instance()->update($arRequestData, OfferCartPositionProcessor::class);
        Result::setData(CartProcessor::instance()->getCartData());

        return Result::get();
    }

    /**
     * Remove offers from cart
     * @return array
     */
    public function remove()
    {
        $arRequestData = Input::get('cart');
        $obActiveShippingType = $this->getActiveShippingTypeFromRequest();
        if (!empty($obActiveShippingType) && $obActiveShippingType->isNotEmpty()) {
            CartProcessor::instance()->setActiveShippingType($obActiveShippingType);
        }

        $sType = Input::get('type', 'offer');

        CartProcessor::instance()->remove($arRequestData, OfferCartPositionProcessor::class, $sType);
        Result::setData(CartProcessor::instance()->getCartData());

        return Result::get();
    }

    /**
     * Restore cart position
     * @return array
     */
    public function restore()
    {
        $arRequestData = Input::get('cart');
        $obActiveShippingType = $this->getActiveShippingTypeFromRequest();
        if (!empty($obActiveShippingType) && $obActiveShippingType->isNotEmpty()) {
            CartProcessor::instance()->setActiveShippingType($obActiveShippingType);
        }

        CartProcessor::instance()->restore($arRequestData, OfferCartPositionProcessor::class);
        Result::setData(CartProcessor::instance()->getCartData());

        return Result::get();
    }

    /**
     * Clear cart
     */
    public function clear()
    {
        CartProcessor::instance()->clear();

        return 'ok';
    }

    /**
     * Set shipping type
     */
    public function setShippingType()
    {
        $iShippingTypeID = Input::get('shipping_type_id');
        if (empty($iShippingTypeID)) {
            $sMessage = Lang::get('lovata.toolbox::lang.message.e_not_correct_request');
            Result::setFalse()->setMessage($sMessage);
            return Result::get();
        }

        //Get shipping type object
        $obShippingTypeItem = ShippingTypeItem::make($iShippingTypeID);
        if ($obShippingTypeItem->isEmpty()) {
            $sMessage = Lang::get('lovata.toolbox::lang.message.e_not_correct_request');
            Result::setFalse()->setMessage($sMessage);
            return Result::get();
        }

        CartProcessor::instance()->setActiveShippingType($obShippingTypeItem);
        Result::setData(CartProcessor::instance()->getCartData());

        return Result::get();
    }

    /**
     * Update cart
     * @return array
     */
    public function saveData()
    {
        $arUserData = (array) Input::get('user');
        $arCartProperty = (array) Input::get('property');
        $arBillingAddress = (array) Input::get('billing_address');
        $arShippingAddress = (array) Input::get('shipping_address');

        $obCart = CartProcessor::instance()->getCartObject();

        try {

            $obCart->user_data = array_merge((array) $obCart->user_data, $arUserData);
            $obCart->email = array_get($obCart->user_data, 'email');

            $obCart->property = array_merge((array) $obCart->property, $arCartProperty);

            $obCart->billing_address = array_merge((array) $obCart->billing_address, $arBillingAddress);
            $obCart->shipping_address = array_merge((array) $obCart->shipping_address, $arShippingAddress);

            $obCart->shipping_type_id = Input::get('shipping_type_id', $obCart->shipping_type_id);
            $obCart->payment_method_id = Input::get('payment_method_id', $obCart->payment_method_id);

            $obCart->save();
        } catch (\October\Rain\Database\ModelException $obException) {
            $this->processValidationError($obException);
        }

        return Result::get();
    }

    /**
     * Get active shipping type from request
     * @return ShippingTypeItem
     */
    public function getActiveShippingTypeFromRequest()
    {
        $iShippingTypeID = Input::get('shipping_type_id');
        if (empty($iShippingTypeID)) {
            return null;
        }

        //Get shipping type item
        $obShippingTypeItem = ShippingTypeItem::make($iShippingTypeID);

        return $obShippingTypeItem;
    }

}
