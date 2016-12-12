<?php

class easypayredirectModuleFrontController extends ModuleFrontController
{
	public function initContent()
	{
		parent::initContent();
		$EP_OrderNo = $this->context->cart->id;
		$cart = New Cart($EP_OrderNo);// Объект корзины
		$easypay = new easypay();//Объект easypay
		$EP_Sum = $cart->getOrderTotal(true, Cart::BOTH);//Сумма заказа
		$easypay->validateOrder($cart->id, Configuration::get('PS_OS_CHEQUE'),$EP_Sum, $easypay->displayName);//Создание заказа с статусом ожидаем оплату
		$EP_MerNo = Tools::safeOutput(Configuration::get('EASYPAY_MER_NO')); //номер поставщика
		$web_key = Tools::safeOutput(Configuration::get('EASYPAY_WEB_KEY')); //ключ
		$hash = md5($EP_MerNo . $web_key . $EP_OrderNo . $EP_Sum);
        $customer = new Customer((int)$this->context->cart->id_customer);
		$products = $cart->getProducts();//Описание продукта
		$this->context->smarty->assign(array(
			'EP_MerNo' => $EP_MerNo,
			'EP_Expires' => Tools::safeOutput(Configuration::get('EASYPAY_EXPIRES')),
			'EP_Sum' => $EP_Sum,
			'EP_Hash' => $hash,
			'EP_OrderNo' =>$EP_OrderNo,
			'EP_OrderInfo' =>$products[0]['name'],
            'EP_Comment' => Configuration::get('PS_SHOP_NAME'),
			'EP_Cancel_URL' => Configuration::get('PS_SSL_ENABLED') ? 'https' : 'http'.'://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'index.php?controller=order&step=1',
			'EP_Success_URL' => Configuration::get('PS_SSL_ENABLED') ? 'https' : 'http'.'://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'index.php?controller=order-confirmation&id_cart='.(int)$cart->id.'&id_module='.(int)$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key,
		));

        $this->setTemplate('module:easypay/views/templates/front/redirect.tpl');
	}
}

?>