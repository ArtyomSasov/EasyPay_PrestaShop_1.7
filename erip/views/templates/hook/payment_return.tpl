{if $status == 'ok'}
	<strong>{l s='Счет добавлен в систему ЕРИП для оплаты. Номер заказа для оплаты:'  mod='erip'}</strong>
	<strong>{$order_erip}</strong>
{else}
	<p class="warning">
		{l s='Произошла ошибка. Пожалуйста, повторите попытку позже.'}
	</p>
{/if}