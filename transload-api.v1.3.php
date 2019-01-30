<?php
/*************************************************************************/
/*	    Class Transload.me API * Version: 1.3/01.02.2019	         */
/*	    Documentation: http://transload.me/?p=api		         */
/*************************************************************************/

class TransloadAPI{

	public $version = 'Transload.API.v1.3';
	public $authorized = false; 
	public $account = array(); 
	private $username; 
	private $password; 
	private $session; 
	private $show_log = false; 

	public function showlog() {
		$this -> show_log = true;
		}

	public function authorize($username = '', $password = '') {
		$this -> username = $username;
		$this -> password = $password; 
		$result = $this ->accountdetails();
		if ($result['result'] == 0)
		{ 
		$this -> authorized = true;
		$this -> account = $result; 
		return true; 
		} 
		return false;
	}

	public function session() {
		if ($this -> authorized == true) 
		{ return $this ->session; } else {return '';}
	}

	public function accountdetails() {
		$api_request = api_host.'?username='.$this->username.'&password='.$this->password.'&require=accountdetalis';
		return $this->decode($api_request);
	}

	public function supporthost () {
		$api_request = api_host.'?username='.$this->username.'&password='.$this->password.'&require=supporthost';
		return $this->decode($api_request);
	}
	public function pricelist () {
		$api_request = api_host.'?username='.$this->username.'&password='.$this->password.'&require=pricelist';
		return $this->decode($api_request);
	}
	public function checkfile($link = '') {
		$api_request = api_host.'?username='.$this->username.'&password='.$this->password.'&require=checkfile&link='.$link;
		return $this->decode($api_request);
	}

	public function downloadfile($link = '') {
		$api_request = api_host.'?username='.$this->username.'&password='.$this->password.'&require=downloadfile&link='.$link;
		return $this->decode($api_request);
	}

	public function createcoupon ($balance = '', $count = '') {
		$api_request = api_host.'?username='.$this->username.'&password='.$this->password.'&require=createcoupon&balance='.$balance.'&count='.$count;
		return $this->decode($api_request);
	}

	public function checkcoupon  ($voucher = '') {
		$api_request = api_host.'?username='.$this->username.'&password='.$this->password.'&require=checkcoupon&voucher='.$voucher;
		return $this->decode($api_request);
	}

	public function refill ($account = '', $balance = '', $return = '', $success_url = '', $error_url = '') {
		if ($return != 'json') $return = 'web/json';
		$api_request = api_host.'?username='.$this->username.'&password='.$this->password.'&require=refill&account='.$account.'&balance='.$balance.'&return='.$return.'&success_url='.$success_url.'&error_url='.$error_url;
		return $this->decode($api_request);
	}

	public function size($size){  
		$metrics[0] = ''; $metrics[1] = 'KB'; $metrics[2] = 'MB'; $metrics[3] = 'GB'; $metrics[4] = 'TB'; $metric = 0;  
		while(floor($size/1024) > 1){  ++$metric;  $size /= 1024; }  
		$result =  round($size,1)." ".(isset($metrics[$metric])?$metrics[$metric]:'??');  
		return $result;  
	 }  

	public function post_auth() {
		$_request = $this -> request();
		$result = false;
		if (($_request ['username'] != null) and ($_request ['password'] != null))
		$result = $this->authorize($_request ['username'], $_request['password']);
		return $result;
	}

	public function process_request() {
		$result = false;
		$_request = $this -> request();
		if ($this -> authorized == true)
		{
		if ($_request['require'] == 'accountdetalis') $result =  $this-> accountdetails();
		if ($_request['require'] == 'supporthost') $result = $this-> supporthost();
		if ($_request['require'] == 'pricelist') $result = $this-> pricelist();
		if ($_request['require'] == 'checkfile') $result = $this-> checkfile($_request['link']);
		if ($_request['require'] == 'downloadfile') $result = $this-> downloadfile($_request['link']);
		if ($_request['require'] == 'createcoupon') $result = $this-> createcoupon($_request['balance'], $_request['count']);
		if ($_request['require'] == 'checkcoupon') $result = $this-> checkcoupon($_request['voucher']);
		if ($_request['require'] == 'refill') $result = $this-> refill($_request['account'], $_request['balance'], $_request['return'], $_request['success_url'], $_request['error_url']);
		return $result;
		} else
		return $result;
	}

	public function msg($code) {
		$_msg[0] = 'The request was fulfilled successfully.';
		$_msg[1] = 'The file is not found or has been deleted.';
		$_msg[2] = 'File exchange is not supported.';
		$_msg[3] = 'A system error occurred while processing, please try again later.';
		$_msg[4] = 'On account of insufficient funds, replenish your balance.';
		$_msg[5] = 'Incorrect username / email or password.';
		$_msg[6] = 'Invalid request method.';
		$_msg[7] = 'You have requested too many links, wait a few minutes, or refill your account.';
		$_msg[8] = 'Access is denied.';
		$_msg[10] = 'The request was fulfilled successfully. The resulting link is a folder.';
		$_msg[100] = 'The coupon is successfully created and confirmed.';
		$_msg[101] = 'On balance insufficient funds.';
		$_msg[102] = 'The user is not found.';
		$_msg[103] = 'The minimum voucher amount 0.20 USD';
		$_msg[104] = 'The maximum value of the voucher 1000 USD';
		$_msg[105] = 'The user is not a reseller.';
		$_msg[106] = 'Invalid coupon code or it has already been used.';
		$_msg[107] = 'The coupon has been successfully activated. Funds credited to the balance.';
		$_msg[108] = 'The reseller can only activate your own coupons.';
		$_msg[109] = 'The coupon is deleted successfully.';
		$result = $_msg[$code];
		return $result;
	}

	private function request(){
		$method = $_SERVER['REQUEST_METHOD'];
		$_request = array_merge($_GET, $_POST);
		$_request['hash'] = isset($_request['hash']) ? ($_request['hash']) : '';
		$_request['username'] = isset($_request['username']) ? ($_request['username']) : '';
		$_request['password'] = isset($_request['password']) ? ($_request['password']) : '';
		$_request['require'] = trim(strtolower( isset($_request['require']) ? ($_request['require']) : ''));
		$_request['link'] = trim(strtolower( isset($_request['link']) ? ($_request['link']) : ''));
		$_request['balance'] = trim(isset($_request['balance']) ? ($_request['balance']) : '');
		$_request['count'] = trim(isset($_request['count']) ? ($_request['count']) : '');
		$_request['voucher'] =  trim(isset($_request['voucher']) ? ($_request['voucher']) : '');
		$_request['account'] = trim(strtolower( isset($_request['account']) ? ($_request['account']) : ''));
		$_request['return'] = trim(strtolower( isset($_request['return']) ? ($_request['return']) : ''));
		$_request['success_url'] =  trim(isset($_request['success_url']) ? ($_request['success_url']) : '');
		$_request['error_url'] =  trim(isset($_request['error_url']) ? ($_request['error_url']) : '');
		return $_request;
	}

	private function get($url){
		$c = curl_init();
		$url.="&client_id=".strtolower($this -> version);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_CONNECTTIMEOUT, $this -> version);
		curl_setopt($c, CURLOPT_USERAGENT, $this -> version);
		curl_setopt($c, CURLOPT_TIMEOUT, 60);
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($c, CURLOPT_URL, $url);
		$result = curl_exec($c);
		return $result;
	}
	private function decode($url){
		$file = $this ->get($url);
		$result = json_decode($file, true);
		$this ->log($url, $result);
		return ($result);
	}

	private function log($url, $result) {
		$_url = parse_url($url);
		$_url = $_url['query'];
		parse_str($_url, $param);
		if ($this -> show_log == true )
		{ 
		$url = str_replace($this -> password, '******',$url);
		$url = str_replace('web/json', 'web',$url);
		$code = $result['result'];
		$msg_text = $this -> msg($code);
		$status = '<span class="input-group-addon" style="background-color:red; color:white">FAIL</span>';
		if (($code == 0) or ($code == 107) or ($code == 100) or ($code == 109) or ($code == 10) or ($code == 103))
		{
		if ($code != 103)
		$status = '<span class="input-group-addon" style="background-color:green; color:white">OK</span>';
			if ($param['require'] == 'refill') 
			if ($param['return'] != 'json') 
			{
			$return_url = $result['return_url'];
			$result = '<h5>302 Moved Temporarily</h5>Location: <a href="'.$return_url.'" target="_blank">'.$return_url.'</a>';
			}
		}
		print('<link rel="stylesheet" href="http://transload.me/api/documentation/bootstrap/css/bootstrap.min.css">');
		print('<div class="box"><div class="box-body">');
		print('<div class="input-group"><span class="input-group-addon">Request URL</span><input readonly="readonly" type="text" class="form-control" value="'.$url.'"></div>');
		print('<div class="input-group"><span class="input-group-addon">Result</span>'.$status.'<input readonly="readonly" type="text" class="form-control" value="'.$msg_text.'"></div>');
		print('<pre>');
		print_r($result);
		print('</pre></div></div>');
		}
	}

}
	define('api_host','http://api.transload.me/');
?>
