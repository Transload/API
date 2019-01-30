<?php
/*****************************************************************/
/*	    	Transload.me API Example		         */
/*	    Documentation: http://transload.me/?p=api	         */
/******************************************************************
	$TransloadAPI -> authorize($_request['username'], $_request['password']);
	$TransloadAPI -> accountdetails();
	$TransloadAPI -> supporthost();
	$TransloadAPI -> pricelist();
	$TransloadAPI -> checkfile($_request['link']);
	$TransloadAPI -> downloadfile($_request['link']);
	$TransloadAPI -> createcupone($_request['balance'], $_request['count'])
	$TransloadAPI -> checkcupone($_request['voucher']);
	$TransloadAPI -> refill($_request['account'], $_request['balance'], $_request['return']$_request['success_url'], $_request['error_url']);

******************************************************************/

	include('transload-api.v1.3.php');
	$_request = array_merge($_GET, $_POST);
	$TransloadAPI = new TransloadAPI;
	$TransloadAPI -> authorize();

	if ($TransloadAPI -> authorized == false)
	die($TransloadAPI -> msg(5));

	print '<pre>Email: '.$TransloadAPI -> account['email'];
	print '<br>Balance: '.number_format($TransloadAPI -> account['balance'],2).' USD';
	print '<br>Downloaded traffic: '.$TransloadAPI -> size($TransloadAPI -> account['download']);
	print '<br>Registration date: '.$TransloadAPI -> account['reg_date'];
	print '</pre>';
?>
