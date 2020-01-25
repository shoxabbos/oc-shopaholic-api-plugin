<?php namespace Shohabbos\Shopaholicapi\Controllers;

use Input;
use Event;
use JWTAuth;
use Illuminate\Http\Request;
use Kharanenka\Helper\Result;
use Illuminate\Routing\Controller;
use Lovata\Toolbox\Classes\Helper\UserHelper;
use Lovata\OrdersShopaholic\Models\UserAddress;
use Lovata\Shopaholic\Classes\Helper\CurrencyHelper;
use Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem;
use Lovata\OrdersShopaholic\Classes\Processor\CartProcessor;
use Lovata\OrdersShopaholic\Classes\Processor\OrderProcessor;
use Lovata\OrdersShopaholic\Classes\Processor\OfferCartPositionProcessor;

use RainLab\User\Models\User as UserModel;
use Lovata\OrdersShopaholic\Models\Order as OrderModel;
use Lovata\OrdersShopaholic\Classes\Item\OrderItem;

// custom classess
use Lovata\OrdersShopaholic\Components\MakeOrder as MakeOrderComponent;
use Lovata\OrdersShopaholic\Components\OrderPage as OrderPageComponent;

class Order extends Controller
{

	const MODE_SUBMIT = 'submit';
    const MODE_AJAX = 'ajax';

    const PROPERTY_MODE = 'mode';
    const PROPERTY_FLASH_ON = 'flash_on';
    const PROPERTY_REDIRECT_ON = 'redirect_on';
    const PROPERTY_REDIRECT_PAGE = 'redirect_page';


	protected $bCreateNewUser = true;

    protected $arOrderData = [];
    protected $arUserData = [];
    protected $arBillingAddressOrder = [];
    protected $arShippingAddressOrder = [];
    /** @var \Lovata\Buddies\Models\User */
    protected $obUser;

    /** @var \Lovata\OrdersShopaholic\Models\Order */
    protected $obOrder;

    /** @var \Lovata\OrdersShopaholic\Interfaces\PaymentGatewayInterface|null */
    protected $obPaymentGateway;




	public $makeOrder;
	public $orderPage;

	public function __construct() {
		$this->makeOrder = new MakeOrderComponent();
		$this->orderPage = new OrderPageComponent();

		$this->obUser = $this->auth();
		if ($this->obUser) {
			$this->arUserData = $this->obUser->toArray();
		}
	}


	public function get() {
		return OrderModel::getBySecretKey(Input::get('key'))->first();
	}


	public function makeOrder() {
		if (!$this->obUser) {
			return response()->json(['error' => 'User not found']);
		}

		$arRequestData = Input::get('cart');
        $obActiveShippingType = $this->getActiveShippingTypeFromRequest();
        if (!empty($obActiveShippingType) && $obActiveShippingType->isNotEmpty()) {
            CartProcessor::instance()->setActiveShippingType($obActiveShippingType);
        }

        CartProcessor::instance()->add($arRequestData, OfferCartPositionProcessor::class);
        $data = Result::setData(CartProcessor::instance()->getCartData());

		$arOrderData = (array) Input::get('order');

        $this->create($arOrderData);

        //Fire event and get redirect URL
        $sRedirectURL = Event::fire(OrderProcessor::EVENT_ORDER_GET_REDIRECT_URL, $this->obOrder, true);
        if (!Result::status() && !empty($this->obPaymentGateway) && $this->obPaymentGateway->isRedirect()) {
            $sRedirectURL = $this->obPaymentGateway->getRedirectURL();

            return Redirect::to($sRedirectURL);
        } else if (empty($this->obPaymentGateway) || !Result::status()) {
            $this->prepareResponseData();
        }

        if ($this->obPaymentGateway && $this->obPaymentGateway->isRedirect()) {
            $sRedirectURL = $this->obPaymentGateway->getRedirectURL();

            return Redirect::to($sRedirectURL);
        } else if ($this->obPaymentGateway && $this->obPaymentGateway->isSuccessful()) {
            Result::setTrue($this->obPaymentGateway->getResponse());
        } else if ($this->obPaymentGateway) {
            Result::setFalse($this->obPaymentGateway->getResponse());
        }

        //Result::setMessage($this->obPaymentGateway->getMessage());
        $this->prepareResponseData();

        $data = Result::data();

        if ($this->obOrder) {
            $data['total_price'] = $this->obOrder->total_price;
            $data['total_price_value'] = $this->obOrder->total_price_value;
        }

        return $data;
	}


	/**
     * Create new order
     * @param array $arOrderData
     * @throws \Exception
     */
    public function create($arOrderData)
    {
        $this->arOrderData = (array) $arOrderData;

        $this->processOrderAddress();

        if (!Result::status()) {
            return;
        }

        $arOrderData = $this->arOrderData;

        $obActiveCurrency = CurrencyHelper::instance()->getActive();

        if (empty(array_get($arOrderData, 'currency')) && !empty($obActiveCurrency)) {
            $arOrderData['currency_id'] = $obActiveCurrency->id;
        }
        if (!isset($arOrderData['property']) || !is_array($arOrderData['property'])) {
            $arOrderData['property'] = [];
        }

        $arOrderData['property'] = array_merge($arOrderData['property'], $this->arUserData, $this->arBillingAddressOrder, $this->arShippingAddressOrder);

        $arPaymentData = Input::get('payment');
        if (!empty($arPaymentData) && is_array($arPaymentData)) {
            $arOrderData['payment_data'] = $arPaymentData;
        }

        $this->obOrder = OrderProcessor::instance()->create($arOrderData, $this->obUser);
        $this->obPaymentGateway = OrderProcessor::instance()->getPaymentGateway();
    }



	/**
     * Prepare address array to save in Order properties
     * @param string $sType
     * @param array  $arAddressData
     * @return array
     */
    protected function prepareAddressData($sType, $arAddressData) : array
    {
        if (empty($arAddressData)) {
            return [];
        }

        $arResult = [];
        foreach ($arAddressData as $sKey => $sValue) {
            $arResult[$sType.'_'.$sKey] = $sValue;
        }

        return $arResult;
    }

	/**
     * Get address data from object
     * @param UserAddress $obAddress
     * @return array
     */
    protected function getAddressData($obAddress) : array
    {
        if (empty($obAddress)) {
            return [];
        }

        $arResult = $obAddress->toArray();
        array_forget($arResult, ['id', 'type']);

        return $arResult;
    }

	/**
     * @param string $sType
     * @param array  $arAddressData
     * @return array
     */
    protected function createUserAddress($sType, $arAddressData) : array
    {
        if (empty($arAddressData)) {
            return [];
        }

        $arAddressData['type'] = $sType;
        $arAddressData['user_id'] = $this->obUser->id;

        try {
            //Create new address for user
            $obAddress = UserAddress::create($arAddressData);
        } catch (\October\Rain\Database\ModelException $obException) {
            $this->processValidationError($obException);
            return [];
        }

        return $this->getAddressData($obAddress);
    }

	/**
     * Find Address object by ID, type and user_id
     * @param string $sType
     * @param array  $arAddressData
     * @return array
     */
    protected function findAddressByID($sType, $arAddressData) : array
    {
        $iAddressID = array_get($arAddressData, 'id');
        if (empty($iAddressID)) {
            return [];
        }

        $obAddress = UserAddress::getByUser($this->obUser->id)->getByType($sType)->find($iAddressID);
        if (empty($obAddress)) {
            return [];
        }

        return $this->getAddressData($obAddress);
    }

	/**
     * Add user address data
     * @param string $sType
     * @param array  $arAddressData
     * @return array
     */
    protected function addOrderAddress($sType, $arAddressData) : array
    {
        if (empty($arAddressData) || empty($sType) || empty($this->obUser)) {
            return $this->prepareAddressData($sType, $arAddressData);
        }

        $arResult = $this->findAddressByID($sType, $arAddressData);

        if (empty($arResult)) {
            $obAddress = UserAddress::findAddressByData($arAddressData, $this->obUser->id);
            if (!empty($obAddress)) {
                $arResult = $this->getAddressData($obAddress);
            }
        }

        if (empty($arResult)) {
            $arResult = $this->createUserAddress($sType, $arAddressData);
        }

        return $this->prepareAddressData($sType, $arResult);
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

    /**
     * Process shipping/billing addresses. Create new user address or get data from exist address
     */
    protected function processOrderAddress()
    {
        $arShippingAddressData = (array) Input::get('shipping_address');
        $arBillingAddressData = (array) Input::get('billing_address');

        $obCart = CartProcessor::instance()->getCartObject();
        $arShippingAddressData = array_merge((array) $obCart->shipping_address, $arShippingAddressData);
        $arBillingAddressData = array_merge((array) $obCart->billing_address, $arBillingAddressData);

        $this->arShippingAddressOrder = $this->addOrderAddress(UserAddress::ADDRESS_TYPE_SIPPING, $arShippingAddressData);
        $this->arBillingAddressOrder = $this->addOrderAddress(UserAddress::ADDRESS_TYPE_BILLING, $arBillingAddressData);
    }

    /**
     * Fire event and prepare response data
     */
    protected function prepareResponseData()
    {
        if (!Result::status()) {
            return;
        }

        $arResponseData = Result::data();
        $arEventData = Event::fire(OrderProcessor::EVENT_UPDATE_ORDER_RESPONSE_AFTER_CREATE, [$arResponseData, $this->obOrder, $this->obUser, $this->obPaymentGateway]);
        if (empty($arEventData)) {
            return;
        }

        foreach ($arEventData as $arData) {
            if (empty($arData)) {
                continue;
            }

            $arResponseData = array_merge($arResponseData, $arData);
        }

        Result::setData($arResponseData);
    }


    private function auth() {
        try {
            $obUser = JWTAuth::parseToken()->authenticate();

            $this->obUser = UserModel::find($obUser->id);
        } catch (\Exception $e) {

        }

        return $this->obUser;
    }

}
