<?php
// Проверка уведомления
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/erip.php');

$values = array();
$values['order_mer_code'] = Tools::GetValue('order_mer_code');
$values['sum'] = Tools::GetValue('sum');
$values['mer_no'] = Tools::GetValue('mer_no');
$values['card'] = Tools::GetValue('card');
$values['purch_date'] = Tools::GetValue('purch_date');
$values['notify_signature'] = Tools::GetValue('notify_signature');

// Проверка электронной подписи
$signature_checked = validateRequest($values);
if ($signature_checked == 1){

	$cart = new Cart($values['order_mer_code']);
	$order_id = Order::getOrderByCartId($cart->id);
	$order = new Order($order_id);

	$history = new OrderHistory();// Объект История заказов
	$history->id_order = $order_id;//Получение данных о заказе через id заказа
	$history->changeIdOrderState(_PS_OS_PAYMENT_, $history->id_order);//Изменим статус заказа на "Оплачен"
	header("HTTP/1.0 200 OK");
	print $status = 'OK | the notice is processed'; //Все успешно
}elseif ($signature_checked == 0){
	header("HTTP/1.0 400 Bad Request");
	print $status = 'FAILED | incorrect digital signature'; //Ошибка вычисления электронной подписи
}else{
	header("HTTP/1.0 400 Bad Request");
	print $status = 'FAILED | the notice is not processed'; //Ошибка в параметрах
} 
// Функция проверки электронной подписи
function validateRequest($request){	
	$signature_checked = -1;
	if (isset($request['order_mer_code']) && 
		isset($request['sum']) && 
		isset($request['mer_no']) && 
		isset($request['card']) && 
		isset($request['purch_date'])
		){
		$signature_checked = 0;
		$hash = md5($request['order_mer_code'] . 
				$request['sum'] . 
				$request['mer_no'] . 
				$request['card'] . 
				$request['purch_date'] . 
				Tools::safeOutput(Configuration::get('ERIP_WEB_KEY')));
				var_dump($hash);
		if ($hash == $request['notify_signature']){
			$signature_checked = 1;
		}
	}

return $signature_checked;
}

?>