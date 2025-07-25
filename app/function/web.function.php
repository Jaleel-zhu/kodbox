<?php
/*
* @link http://kodcloud.com/
* @author warlee | e-mail:kodcloud@qq.com
* @copyright warlee 2014.(Shanghai)Co.,Ltd
* @license http://kodcloud.com/tools/license/license.txt
*/


/**
 * client ip address
 * 
 * @param boolean $s_type ip类型[ip|long]
 * @return string $ip
 */
function get_client_ip($b_ip = true){
	$arr_ip_header = array( 
		"HTTP_CLIENT_IP",
		"HTTP_X_FORWARDED_FOR",
		"REMOTE_ADDR",
		"HTTP_CDN_SRC_IP",
		"HTTP_PROXY_CLIENT_IP",
		"HTTP_WL_PROXY_CLIENT_IP"
	);
	$client_ip = 'unknown';
	foreach ($arr_ip_header as $key) {
		if (!empty($_SERVER[$key]) && strtolower($_SERVER[$key]) != "unknown") {
			$client_ip = $_SERVER[$key];
			break;
		}
	}
	if ($pos = strpos($client_ip,',')){
		// $client_ip = substr($client_ip,$pos+1);
		$client_ip = substr($client_ip,0,$pos);
	}
	$client_ip = trim($client_ip);
	if (!$client_ip || filter_var($client_ip, FILTER_VALIDATE_IP)) return $client_ip;
	// 过滤可能伪造的ip
	preg_match('/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/', $client_ip, $matches);
	$client_ip = filter_var($matches[1], FILTER_VALIDATE_IP);
	return $client_ip ? $client_ip : 'unknown';
}

if(!function_exists('filter_var')){
	if(!defined('FILTER_VALIDATE_IP')){
		define('FILTER_VALIDATE_INT','int');
		define('FILTER_VALIDATE_FLOAT','float');
		define('FILTER_VALIDATE_EMAIL','email');
		define('FILTER_VALIDATE_REGEXP','reg');
		define('FILTER_VALIDATE_URL','url');
		define('FILTER_VALIDATE_IP','ip');
		
		define('FILTER_FLAG_IPV4','ipv4');
		define('FILTER_FLAG_IPV6','ipv6');
		define('FILTER_FLAG_EMAIL_UNICODE','email');
		
		define('FILTER_SANITIZE_STRING','string');
		define('FILTER_SANITIZE_NUMBER_INT','int');
		define('FILTER_SANITIZE_NUMBER_FLOAT','float');
		define('FILTER_SANITIZE_SPECIAL_CHARS','special');
		define('FILTER_SANITIZE_EMAIL','email');
	}
	function filter_var($str,$filter,$option=false){
		$mapReg = array(
			'ip' 		=> "/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/",
			'ipv4' 		=> "/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/",
			'ipv6' 		=> "/\s*(([:.]{0,7}[0-9a-fA-F]{0,4}){1,8})\s*/",
			'url'		=> "/(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&:\/~\+#]*[\w\-\@?^=%&\/~\+#])?/",
			'email'		=> '/\w+([\.\-]\w+)*\@\w+([\.\-]\w+)*\.\w+/',
			'int'		=> "/[-\+]?\d+/",
			'float'		=> "/[-\+]?\d+(\.\d+)?/",
			// 'reg'	=> _get($options,'regexp'),
		);
		if($filter == 'string'){return addslashes($str);}
		if($filter == 'special'){return htmlspecialchars($str,ENT_QUOTES,'UTF-8',false);}
		
		if($filter== 'ip' && $option == 'ipv4'){$filter = 'ipv4';}
		if($filter== 'ip' && $option == 'ipv6'){$filter = 'ipv6';}
		$reg = $mapReg[$filter];
		if(preg_match($reg,$str,$matches)){return $matches[1];}
		return $str;
	}
}

function get_server_ip(){
	static $ip = NULL;
	if ($ip !== NULL) return $ip;
	if (isset($_SERVER['SERVER_ADDR'])){
		$ip = $_SERVER['SERVER_ADDR'];
	}else{
		$ip = getenv('SERVER_ADDR');
	}
	
	if(!$ip){
		$ipArray = gethostbynamel(gethostname());
		$ipArray = is_array($ipArray) ? $ipArray : array();
		for($i=0; $i < count($ipArray); $i++) { 
			if($ipArray[$i] == '127.0.0.1'){continue;}
			$ip = $ipArray[$i];break;
		}
	}
	return $ip;
}

function get_url_link($url){
	if(!$url) return "";
	$res = parse_url($url);
	$port = (empty($res["port"]) || $res["port"] == '80')?'':':'.$res["port"];
	return $res['scheme']."://".$res["host"].$port.$res['path'];
}
function get_url_root($url){
	if(!$url) return "";
	$res = parse_url($url);
	$port = (empty($res["port"]) || $res["port"] == '80')?'':':'.$res["port"];
	return $res['scheme']."://".$res["host"].$port.'/';
}
function get_url_domain($url){
	if(!$url) return "";
	$res = parse_url($url);
	return $res["host"] ? $res["host"]:'';
}
function get_url_scheme($url){
	if(!$url) return "";
	$res = parse_url($url);
	return $res['scheme'] ? $res["scheme"]:'';
}
function is_domain($host){
	if(!$host) return false;
	$tmp = parse_url($host);
	if (isset($tmp['host'])) $host = $tmp['host'];
	if($host == 'localhost') return false;
	return !filter_var($host, FILTER_VALIDATE_IP);
}
function http_build_url($urlArr) {
	if (empty($urlArr)) return '';
	$url = $urlArr['scheme'] . "://".$urlArr['host'];
	if(!empty($urlArr['port'])) {
		$url .= ":" . $urlArr['port'];
	}
	$url .= $urlArr['path'];
	if(!empty($urlArr['query'])) {
		$url .= "?" . $urlArr['query'];
	}
	if(!empty($urlArr['fragment'])) {
		$url .= "#" . $urlArr['fragment'];
	}
	return $url;
}

function http_type(){
	if( 
		(!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') ||
		(!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ||
		(!empty($_SERVER['HTTP_SSL']) && $_SERVER['HTTP_SSL'] == 1) ||
		
		(!empty($_SERVER['HTTP_X_HTTPS']) && strtolower($_SERVER['HTTP_X_HTTPS']) !== 'off') ||
		(!empty($_SERVER['HTTP_X_SCHEME']) && $_SERVER['HTTP_X_SCHEME'] == 'https') ||
		(!empty($_SERVER['HTTP_X_SSL']) && ($_SERVER['HTTP_X_SSL'] == 1 || strtolower($_SERVER['HTTP_X_SSL']) == 'yes')) ||
		(!empty($_SERVER['HTTP_CF_VISITOR']) && strpos($_SERVER['HTTP_CF_VISITOR'], 'https') !== false) ||
		(!empty($_SERVER['HTTP_X_FORWARDED_PROTOCOL']) && $_SERVER['HTTP_X_FORWARDED_PROTOCOL'] == 'https') || 
		(!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
	){
		return 'https';
	}
	return 'http';
}

function get_host() {
	$httpType = http_type();
	$port = (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] !='80') ? ':'.$_SERVER['SERVER_PORT']:'';
	if($httpType == 'https' && $port == ':443'){$port = '';} // 忽略https 443端口;
	$host = $_SERVER['SERVER_NAME'].$port;
	if(isset($_SERVER['HTTP_HOST'])){$host = $_SERVER['HTTP_HOST'];}
	if(isset($_SERVER['HTTP_X_FORWARDED_HOST'])){//proxy
		$hosts = explode(',', $_SERVER['HTTP_X_FORWARDED_HOST']);
		$host  = trim($hosts[0]);
	}else if(isset($_SERVER['HTTP_X_FORWARDED_SERVER'])){
		$host  = $_SERVER['HTTP_X_FORWARDED_SERVER'];
	}

	// 前端cookie保留host; //define() >  cookie > x-forwarded-host > host
	$cookieKey = 'KOD_HOST_'.KOD_SITE_ID;
	if($_COOKIE && !empty($_COOKIE[$cookieKey])){
		$kodHost = rtrim(rawurldecode($_COOKIE[$cookieKey]),'/').'/';
		return str_replace(array('<','>'),'_',$kodHost);
	}
	
	$host = str_replace(array('<','>'),'_',$host);// 安全检测兼容(xxs)
	return $httpType.'://'.trim($host,'/').'/';
}
// current request url
function this_url(){
	return get_host().ltrim($_SERVER['REQUEST_URI'],'/');
}

//解决部分主机不兼容问题
function webroot_path($basicPath){
	$index = path_clear($basicPath.'index.php');
	$uri   = path_clear($_SERVER["DOCUMENT_URI"]);
	
	// 兼容 index.php/explorer/list/path; 路径模式;
	if($uri){//DOCUMENT_URI存在的情况;
		$uri = dirname($uri).'/index.php';
	}
	if( substr($index,- strlen($uri) ) == $uri){
		$path = substr($index,0,strlen($index)-strlen($uri));
		return rtrim($path,'/').'/';
	}
	$uri = path_clear($_SERVER["SCRIPT_NAME"]);
	if( substr($index,- strlen($uri) ) == $uri){
		$path = substr($index,0,strlen($index)-strlen($uri));
		return rtrim($path,'/').'/';
	}
	
	// 子目录sso调用情况兼容;
	if($_SERVER['SCRIPT_FILENAME'] && $_SERVER["DOCUMENT_URI"]){
		$index = path_clear($_SERVER['SCRIPT_FILENAME']);
		$uri   = path_clear($_SERVER["DOCUMENT_URI"]);		
		// 兼容 index.php/test/todo 情况;
		if( strstr($uri,'.php/')){
			$uri = substr($uri,0,strpos($uri,'.php/')).'.php';
		}		
		if( substr($index,- strlen($uri) ) == $uri){
			$path = substr($index,0,strlen($index)-strlen($uri));
			return rtrim($path,'/').'/';
		}
	}
	return str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']);
}

function ua_has($str){
	if(!isset($_SERVER['HTTP_USER_AGENT'])){
		return false;
	}
	if(strpos($_SERVER['HTTP_USER_AGENT'],$str) ){
		return true;
	}
	return false;
}
function is_wap(){   
	if(!isset($_SERVER['HTTP_USER_AGENT'])){
		return false;
	} 
	if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom|miui)/i', 
		strtolower($_SERVER['HTTP_USER_AGENT']))){
		return true;
	}
	if((isset($_SERVER['HTTP_ACCEPT'])) && 
		(strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false)){
		return true;
	}
	return false;
}

/**
 * 终止并完成http请求；客户端终止等待完成请求
 * 后续代码可以继续运行；例如日志、统计等代码；后续输出将不再生效；
 */
function http_close(){
	ignore_timeout();
	static $firstRun = false;
	if($firstRun) return; //避免重复调用;
	
	if(function_exists('fastcgi_finish_request')) {
		fastcgi_finish_request();
	} else {
		header("Connection: close");
		header("Content-Length: ".ob_get_length());
		ob_start();echo str_pad('',1024*5);
        ob_end_flush();flush();
        ob_end_flush();echo str_pad('',1024*5);flush();
	}
	$firstRun = true;
}

function parse_headers($raw_headers){
	$headers = array();
	$key = '';
	foreach (explode("\n", $raw_headers) as $h) {
		$h = explode(':', $h, 2);
		if (isset($h[1])) {
			if ( ! isset($headers[$h[0]])) {
				$headers[$h[0]] = trim($h[1]);
			} elseif (is_array($headers[$h[0]])) {
				$headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1])) );
			} else {
				$headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1])) );
			}
			$key = $h[0];
		} else {
			if (substr($h[0], 0, 1) === "\t") {
				$headers[$key] .= "\r\n\t" . trim($h[0]);
			} elseif ( ! $key) {
				$headers[0] = trim($h[0]);
			}
			trim($h[0]);
		}
	}
	return $headers;
}


$GLOBALS['curlKodLastTime'] = 0; // 间隔100ms;
$GLOBALS['curlKodLast'] = false;

$GLOBALS['curlCache'] = array();
$GLOBALS['curlCacheResult'] = array();
function curl_progress_start($curl){
	$GLOBALS['curlKodLastTime'] = 0;
	$GLOBALS['curlKodLast'] = $curl;
	Hook::trigger('curl.progressStart',$curl);
	think_status('curlTimeStart');

	// 内存缓存;
	// $curlInfo = curl_getinfo($curl);
	// if(isset($GLOBALS['curlCache'][$curlInfo['url']])){
	// 	write_log($curlInfo);
	// 	$curl = curl_copy_handle($GLOBALS['curlCache'][$curlInfo['url']]);
	// 	return $GLOBALS['curlCacheResult'][$curlInfo['url']];
	// }
}
function curl_progress_end($curl,&$curlResult=false){
	$GLOBALS['curlKodLastTime'] = 0;
	$GLOBALS['curlKodLast'] = false;
	Hook::trigger('curl.progressEnd',$curl);
	
	// 网络请求记录;
	$curlInfo = curl_getinfo($curl);
	think_status('curlTimeEnd');
	$runTime = '[ RunTime:'.think_status('curlTimeStart','curlTimeEnd',6).'s ]';
	$runInfo = "sizeUp={$curlInfo['size_upload']};sizeDown={$curlInfo['download_content_length']};";//json_encode($curlInfo)
	think_trace(" ".$curlInfo['url'].";".$runInfo.$runTime,'','CURL');
	
	$httpCode = $curlInfo['http_code'];
	$errorMessage = '';
	if($curlResult && $httpCode < 200 || $httpCode >= 300){
		$errorMessage = "curl error code=".$httpCode.'; '.curl_error($curl);		
		$GLOBALS['curl_request_error'] = array('message'=>$errorMessage,'url'=> $curlInfo['url'],'code'=>$httpCode);
		write_log("[CURL] ".$curlInfo['url'].";$errorMessage;");
	}
	// write_log("[CURL] ".$curlInfo['url']."; code=$httpCode;".curl_error($curl).";".get_caller_msg(),'test');
	if(GLOBAL_DEBUG){
		$response = strlen($curlResult) > 1000 ? substr($curlResult,0,1000).'...':$curlResult;
		write_log("[CURL] code=".$httpCode.';'.$curlInfo['url'].";$errorMessage \n".$response,'curl');
	}
}
function curl_progress(){
	$args = func_get_args();
	if (is_resource($args[0]) || (is_object($args[0]) && get_class($args[0])=== 'CurlHandle')  ) { 
		// php5.5+ 第一个为$curl; <5.5则只有4个参数; php8兼容
		array_shift($args);
	}
	$downloadSize 	= $args[0];
	$download 		= $args[1];
	$uploadSize 	= $args[2];
	$upload 		= $args[3];
	if(!$download && !$upload ) return;
	if(timeFloat() - $GLOBALS['curlKodLastTime'] < 0.1) return;
	
	$GLOBALS['curlKodLastTime'] = timeFloat();
	Hook::trigger('curl.progress',$GLOBALS['curlKodLast'],$downloadSize,$download,$uploadSize,$upload);
}


// https://segmentfault.com/a/1190000000725185
// http://blog.csdn.net/havedream_one/article/details/52585331 
// php7.1 curl上传中文路径文件失败问题？【暂时通过重命名方式解决】
function url_request($url,$method='GET',$data=false,$headers=false,$options=false,$json=false,$timeout=3600){
	if($url && substr($url,0,2) == '//'){$url = 'http:'.$url;}
	$header = url_header($url);// 跳转同时检测;
	if(!$url || !$header['url'] ){
		$theUrl = isset($header['urlBefore']) ? $header['urlBefore']:$url;
		return array('data'=> 'URL not allow! '.htmlentities($theUrl),'code'=> 0);
	}
	ignore_timeout();
	$ch = curl_init();
	$upload = false;
	if(is_array($data)){//上传检测并兼容
		foreach($data as $key => $value){
			if(!is_string($value) || substr($value,0,1) !== "@"){
				continue;
			}
			$upload = true;
			$path = ltrim($value,'@');
			$filename = iconv_app(get_path_this($path));
			$mime = get_file_mime(get_path_ext($filename));
			if(isset($data['curlUploadName'])){//自定义上传文件名;临时参数
				$filename = $data['curlUploadName'];
				unset($data['curlUploadName']);
			}
			if (class_exists('\CURLFile')){
				$data[$key] = new CURLFile(realpath($path),$mime,$filename);
			}else{
				$data[$key] = "@".realpath($path).";type=".$mime.";filename=".$filename;
			}
			//有update且method为PUT
			if($method == 'PUT'){
				curl_setopt($ch, CURLOPT_PUT,1);
				curl_setopt($ch, CURLOPT_INFILE,@fopen($path,'r'));
				curl_setopt($ch, CURLOPT_INFILESIZE,@filesize($path));
				unset($data[$key]); // put通常通过body上传文件;不需要post参数,参数放在url中
			}
		}
	}
	if($upload){
		if(class_exists('\CURLFile')){
			curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
		}else if (defined('CURLOPT_SAFE_UPLOAD')) {
			curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
		}
	}

	// post数组或拼接的参数；不同方式服务器兼容性有所差异
	// http://blog.csdn.net/havedream_one/article/details/52585331 
	// post默认用array发送;content-type为x-www-form-urlencoded时用key=1&key=2的形式
	if (is_array($data) && is_array($headers) && $method != 'DOWNLOAD'){
		foreach ($headers as $key) {
			if(strstr($key,'x-www-form-urlencoded')){
				$data = http_build_query($data);
				break;
			}
		}
	}
	if($method == 'GET' && $data){
		$data = is_array($data) ? http_build_query($data) : '';
		if($data &&  strstr($url,'?')){$url = $url.'&'.$data;}
		if($data && !strstr($url,'?')){$url = $url.'?'.$data;}
	}
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_HEADER,1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
	curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	// curl_setopt($ch, CURLOPT_SSLVERSION,1);//1|5|6; http://t.cn/RZy5nXF
	// curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
	curl_setopt($ch, CURLOPT_TIMEOUT,$timeout);
	curl_setopt($ch, CURLOPT_REFERER,get_url_root($url));
	curl_setopt($ch, CURLOPT_NOPROGRESS, false);
	curl_setopt($ch, CURLOPT_PROGRESSFUNCTION,'curl_progress');curl_progress_start($ch);
	curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36');
	
	if($options && isset($options['cookie'])){
		curl_setopt($ch, CURLOPT_COOKIE, $options['cookie']);
		unset($options['cookie']);
	}
	if($headers){
		if(is_string($headers)){
			$headers = array($headers);
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	}

	switch ($method) {
		case 'GET':
			curl_setopt($ch,CURLOPT_HTTPGET,1);
			break;
		case 'DOWNLOAD':
			//远程下载到指定文件；进度条
			$fp = fopen ($data,'w+');
			curl_setopt($ch, CURLOPT_HTTPGET,1);
			curl_setopt($ch, CURLOPT_HEADER,0);//不输出头
			curl_setopt($ch, CURLOPT_FILE, $fp);
			//CURLOPT_RETURNTRANSFER 必须放在CURLOPT_FILE前面;否则出问题
			break;
		case 'HEAD':
			curl_setopt($ch, CURLOPT_NOBODY, true);
			break;
		case 'POST':
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
			break;
		case 'OPTIONS':
		case 'PATCH':
		case 'DELETE':
		case 'PUT':
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST,$method);
			if($data){curl_setopt($ch, CURLOPT_POSTFIELDS,$data);}
			break;
		default:break;
	}

	if(is_array($options)){
		curl_setopt_array($ch, $options);
	}
	$response = curl_exec($ch);curl_progress_end($ch,$response);
	$header_size = curl_getinfo($ch,CURLINFO_HEADER_SIZE);
	$response_info = curl_getinfo($ch);
	$http_body   = substr($response, $header_size);
	$http_header = substr($response, 0, $header_size);
	$http_header = parse_headers($http_header);
	if(is_array($http_header)){
		$http_header['kod_add_request_url'] = $url;
	}
	//error
	if($response_info['http_code'] == 0){
		$error_message = curl_error($ch);
		$error_message = $error_message ? "\n".$error_message : 'Network error!';
		return array(
			'data'		=> "API call to $url failed;".$error_message,
			'code'		=> 0,
			'header'	=> $response_info,
		);
	}

	curl_close($ch);
	if(isset($GLOBALS['curlCurrentFile']) && is_array($GLOBALS['curlCurrentFile'])){
		Cache::remove($GLOBALS['curlCurrentFile']['cacheKey']);
	}
	$success = $response_info['http_code'] >= 200 && $response_info['http_code'] <= 299;
	if( $json && $success){
		$data = @json_decode($http_body,true);
		if (json_last_error() == 0) { // string
			$http_body = $data;
		}
	}
	$return = array(
		'data'		=> $http_body,
		'status'	=> $success,
		'code'		=> $response_info['http_code'],
		'header'	=> $http_header,
	);
	return $return;
}
function curl_get_contents($url,$timeout=60){
	$data = url_request($url,'GET',0,0,0,0,$timeout);
	if($data['code'] == 0) return false;
	return $data['data'];
}
function file_get_contents_nossl($url){
	$options = array('ssl'	=> array(
		'verify_peer'		=> false, 
		'verify_peer_name'	=> false
	));
	return file_get_contents($url, false, stream_context_create($options));
}

function url_request_proxy($url,$method='GET',$data=false,$headers=false,$options=false,$json=false,$timeout=3600){
	if(!is_array($headers)){$headers = array();}
	$config = $GLOBALS['config'];
	if($config['CURLOPT_PROXY']){
		if(!is_array($options)){$options = array();}
		foreach($config['CURLOPT_PROXY'] as $k=>$v){$options[$k] = $v;}
	}else if($config['HTTP_PROXY']){
		$add = strstr($config['HTTP_PROXY'],'?') ? '&':'?';
		$url = $config['HTTP_PROXY'] .$add.'_url='.base64_encode($url);
	};
	return url_request($url,$method,$data,$headers,$options,$json,$timeout);
}


// 多个url批量请求; ['url1','url2',...],  or [{url,method,data,header,options},...],  
function url_request_mutil($requests,$timeout=20){
	$mh = curl_multi_init();
	$handles = array();

	// 初始化每个请求
	$hasStart = false;$hasEnd = false;
	foreach($requests as $index => $request){
		if(is_string($request)){$request = array('url' => $request,'method'=>'GET');}
		$ch = curl_init();$url = $request['url'];
		if(isset($request['method']) && strtoupper($request['method']) === 'POST') {
			curl_setopt($ch, CURLOPT_POST,1);
			if(isset($request['data'])){curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($request['data']));}
		}else{
			curl_setopt($ch,CURLOPT_HTTPGET,1);
			$data = is_array($request['data']) ? http_build_query($request['data']) : '';
			if($data &&  strstr($url,'?')){$url = $url.'&'.$data;}
			if($data && !strstr($url,'?')){$url = $url.'?'.$data;}
		}
		if(isset($request['headers'])){
			$headers = is_string($request['headers']) ? array($request['headers']) : $headers;
			curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
		}
		if(is_array($request['options'])){
			if(isset($request['options']['cookie'])){
				curl_setopt($ch, CURLOPT_COOKIE, $request['options']['cookie']);
				unset($request['options']['cookie']);
			}
			curl_setopt_array($ch, $request['options']);
		}
		
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
		curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT,$timeout);
		curl_setopt($ch, CURLOPT_REFERER,get_url_link($url));
		curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36');
		curl_multi_add_handle($mh, $ch);
		if(!$hasStart){$hasStart = true;curl_progress_start($ch);}
		$handles[$index] = $ch;
	}

	// 执行所有请求;循环调用-每次调用返回当前状态码;
	$active = null;
	do{
		$mrc = curl_multi_exec($mh, $active);
	}while($mrc == CURLM_CALL_MULTI_PERFORM);
	while($active && $mrc == CURLM_OK){
		if(curl_multi_select($mh) == -1){continue;}
		do{
			$mrc = curl_multi_exec($mh, $active);
		}while($mrc == CURLM_CALL_MULTI_PERFORM);
	}

	// 获取结果
	$results = array();
	foreach($handles as $index => $ch){
		$info = curl_getinfo($ch);
		$results[$index] = array(
			'data' 	=> curl_multi_getcontent($ch),
			'info' 	=> $info,
			'code'	=> $info['http_code'] >= 200 && $info['http_code'] <= 299,
		);
		if(!$hasEnd){$hasEnd = true;curl_progress_end($ch,$results[$index]['data']);}
		curl_multi_remove_handle($mh, $ch);
		curl_close($ch);
	}	
	curl_multi_close($mh);
	return $results;
}

function get_headers_curl($url,$timeout=10,$depth=0,&$headers=array()){
	if($url && substr($url,0,2) == '//'){$url = 'http:'.$url;}
	if(!function_exists('curl_init')) return false;
	if(!$url || !request_url_safe($url)) return false;
	if($depth >= 10) return false;
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_HEADER,true); 
	curl_setopt($ch, CURLOPT_NOBODY,true); 
    curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($ch, CURLOPT_TIMEOUT,$timeout);
	curl_setopt($ch, CURLOPT_REFERER,get_url_link($url));
	curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36');
	
	// 通过GET获取header, 兼容oss等服务器不允许method=HEAD的情况;
	if($GLOBALS['curl_header_use_get']){
		curl_setopt($ch, CURLOPT_NOBODY,false);
		curl_setopt($ch, CURLOPT_HTTPGET,1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Range: bytes=0-0"));
	}

	$res = curl_exec($ch);
	$res = explode("\r\n", $res);
	$response_info = curl_getinfo($ch);
	$headers['http_code'] = $response_info['http_code'];

	$location = false;
	foreach ($res as $line) {
		list($key, $val) = explode(": ", $line, 2);
		$the_key = trim($key);
		if($the_key == 'Location' || $the_key == 'location'){
			$the_key = 'Location';
			$location = trim($val);
		}
		if( strlen($the_key) == 0 &&
			strlen(trim($val)) == 0  ){
			continue;
		}
		if( substr($the_key,0,4) == 'HTTP' &&
			strlen(trim($val)) == 0  ){
			$headers[] = $the_key;
			continue;
		}

		if(!isset($headers[$the_key])){
			$headers[$the_key] = trim($val);
		}else{
			if(is_string($headers[$the_key])){
				$temp = $headers[$the_key];
				$headers[$the_key] = array($temp);
			}
			$headers[$the_key][] = trim($val);
		}
	}
	if($location !== false){
		$depth++;
		get_headers_curl($location,$timeout,$depth,$headers);
	}
	return count($headers)==0?false:$headers;
} 

/**
 * url安全监测; 防止SSRF 攻击;
 * 监测处: curl_exec,file_get_contents,fsockopen
 * 
 * https://websec.readthedocs.io/zh/latest/vuln/ssrf.html 
 * 仅保留http,https协议; 禁用内网及本机访问; 端口白名单; 
 * 301跳转注意处理; 
 */
function request_url_safe($url){
	$url   = str_replace('\\','/',$url);
	$allow = array('http','https','ftp');
	$info  = parse_url($url);$hasAllow = false;
	foreach($allow as $scheme){
		$schemeNow = substr($url,0,strlen($scheme) + 3);
		if($schemeNow === $scheme."://"){$hasAllow = true;}
	}
	if(strstr($url,'../')) return false;
	if(!$hasAllow) return false;
	if(!$info['scheme'] || !$info['host'] || !in_array($info['scheme'],$allow)) return false;
	if(@file_exists($url) ) return false;
	//if($info['host'] == 'localhost' || $info['host'] == '127.0.0.1' || strstr($info['host'],'192.168.')) return false;
	
	return true;
}

// url header data
function url_header($url){
	if(!request_url_safe($url)) return false;
	$header = get_headers_curl($url);//curl优先
	if(is_array($header) && $header['http_code'] == 302){$header = false;}
	if(is_array($header)){
		$header['ACTION_BY'] = 'get_headers_curl';
	}else{
		stream_context_set_default(array(
            'ssl' => array(
                'verify_host'       => false,
                'verify_peer'       => false,
                'verify_peer_name'  => false,
            ),
        ));
		$header = @get_headers($url,true);
		if($header){
			$header['http_code'] = intval(match_text($header[0],'HTTP\/1\.1\s+(\d+)\s+'));
		}
	}
	if (!$header) return false; 
	foreach ($header as $key => $value) {
		$header[strtolower($key)] = $value;
	}

	//加入小写header值;兼容各种不统一的情况
	$header['———'] = '————————————';//分隔
	foreach ($header as $key => $value) {
		$header[strtolower($key)] = $value;
	}
	$checkArr = array(
		'content-length'		=> 0, 
		'location'				=> $url,//301调整
		'content-disposition'	=> '',
		'content-range'			=> '',
	);
	//处理多次跳转的情况
	foreach ($checkArr as $key=>$val) {
		if(isset($header[$key])){
			$checkArr[$key] = $header[$key];
			if(is_array($header[$key])  && count($header[$key])>0){
				$checkArr[$key] = $header[$key][count($header[$key])-1];
			}
		}
	}
	
	$name 	= $checkArr['content-disposition'];
	$length = $checkArr['content-length'];
	$fileUrl= $checkArr['location'];
	if($fileUrl && substr($fileUrl,0,2) == '//'){$fileUrl = 'http:'.$fileUrl;}
	
	// 如果是断点请求, 内容长度从content-range取总长度;
	if($checkArr['content-range']){
		$rangeArr = explode('/',$checkArr['content-range']);
		if(count($rangeArr) == 2 && intval($rangeArr[1]) > intval($length)){
			$length = intval($rangeArr[1]);
		}
	}
	
	if($name){
		$disposition = $name;
		$name = '';
		$checkReg = array( //前后优先级依次降低;
			'/filename\s*\*\s*=.*\'\'(.*)/i',
			'/filename\s*=\s*"(.*)"/i',
			'/filename\s*=\s*(.*);/i'
		);
		foreach ($checkReg as $reg){
			preg_match($reg,$disposition,$match);
			if(!$name && is_array($match) && count($match) == 2 && $match[1]){
				$name = $match[1];
			}
		}
	}
	if(isset($header['x-outfilename']) && $header['x-outfilename']){
		$name = rawurldecode($header['x-outfilename']);
	}
	if(!$name){
		$name = get_path_this($fileUrl);
		if (strstr($name,'?')){
			$name = substr($name,0,strrpos($name,'?'));
		}
		if(!$name) $name = date("mdHi");
		if(!strstr($name,'.')){ //没有扩展名,自动追加;
			$contentType = $header['content-type']; // location ;跳转情况;
			if(is_array($contentType)){
				$contentType = $contentType[count($contentType)-1];
			}
			$ext  = get_file_ext_by_mime($contentType);
			$name .= '.'.$ext;
		}
	}

	$name = str_replace(array('/','\\'),'-',rawurldecode($name));//safe;
	$supportRange = isset($header["accept-ranges"])?true:false;
	$result = array(
		'url' 		=> request_url_safe($fileUrl) ? $fileUrl: '',
		'urlBefore' => $fileUrl,
		'length' 	=> $length,
		'name' 		=> trim($name,'"'),
		'supportRange' => $supportRange && ($length!=0),
		'code'		=> $header['http_code'],
		'status'	=> $header['http_code'] >= 200 && $header['http_code'] < 400,
		'all'		=> $header,
	);
	if(!function_exists('curl_init')){
		$result['supportRange'] = false;
	}
	//pr($url,$result);
	return $result;
}


// check url if can use
function check_url($url){
	stream_context_set_default(array(
		'ssl' => array(
			'verify_host'       => false,
			'verify_peer'       => false,
			'verify_peer_name'  => false,
		),
	));
	$array = @get_headers($url,true);
	if(!$array) return false;

	$error = array('/404/','/403/','/500/');
	foreach ($error as $value) {
		if (preg_match($value, $array[0])) {
			return false;
		}
	}
	return true;
} 

// refer URL
function refer_url(){
	return isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '';
} 

function select_var($array){
	if (!is_array($array)) return -1;
	ksort($array);
	$chosen = -1;
	foreach ($array as $k => $v) {
		if (isset($v)) {
			$chosen = $v;
			break;
		} 
	} 
	return $chosen;
}

/**
 * 解析url获得url参数
 * @param $query
 * @return array array
 */
function parse_url_query($url){
	$arr = mb_parse_url($url);
	$queryParts = explode('&',$arr['query']);
	$params = array();
	foreach ($queryParts as $param) {
		$item = explode('=', $param);
		// $params[$item[0]] = $item[1];
		$key = $item[0]; unset($item[0]);
        $params[$key] = implode('=', $item);
	}
	return $params;
}

function mb_parse_url($url, $component = -1) {
	$encodedUrl = preg_replace_callback(
		'%[^:/?#&=\.]+%usD',
		function ($matches) {
			return urlencode($matches[0]);
		},
		$url
	);
	$components = parse_url($encodedUrl, $component);
	if (is_array($components)) {
		foreach ($components as &$part) {
			if (is_string($part)) {
				$part = urldecode($part);
			}
		};unset($part);
	} else if (is_string($components)) {
		$components = urldecode($components);
	}
	return $components;
}

function stripslashes_deep($value,$decode=true){
	return is_array($value) ?array_map('stripslashes_deep',$value) :stripslashes($value);
}

function parse_url_route(){
	$param = str_replace($_SERVER['SCRIPT_NAME'],"",$_SERVER['SCRIPT_NAME']);
	if($param && substr($param,0,1) == '/'){
		$arr = explode('&',$param);
		$arr[0] = ltrim($arr[0],'/');
		foreach ($arr as  $cell) {
			$cell = explode('=',$cell);
			if(is_array($cell)){
				if(!isset($cell[1])){
					$cell[1] = '';
				}
				$_GET[$cell[0]] = $cell[1];
				$_REQUEST[$cell[0]] = $cell[1];
			}
		}
	}
}


/**
 * GET/POST数据统一入口
 * 将GET和POST的数据进行过滤，去掉非法字符以及hacker code，返回一个数组
 * 注意如果GET和POST有相同的Key，POST优先
 * 
 * @return array $_GET和$_POST数据过滤处理后的值
 */
function parse_incoming(){
	parse_url_route();
	global $_GET, $_POST,$_COOKIE;
	if (function_exists('get_magic_quotes_gpc') && @get_magic_quotes_gpc()) {
		$_COOKIE = stripslashes_deep($_COOKIE);
		$_GET	 = stripslashes_deep($_GET);
		$_POST	 = stripslashes_deep($_POST);
	}	
	$return = array();
	$return = array_merge($_GET,$_POST);
	foreach ($return as $itemKey => $itemValue) {
		if(is_array($itemValue)){
			unset($return[$itemKey]);
		}
	}
	$remote = array_get_index($return,0);
	
	//路由支持以参数形式传入;兼容没有value的GET参数key被忽略掉的情况;UC手机浏览器;
	if(isset($return['API_ROUTE'])){
		$remote = array($return['API_ROUTE'],'');
		unset($return['API_ROUTE']);
	}
	
	$router = isset($remote[0]) ? trim($remote[0],'/') : '';
	preg_match_all('/[0-9a-zA-Z\/_-]*/',$router,$arr);
    $router = join('',$arr[0]);
    $router = str_replace('/','.',$router);
	$remote = explode('.',$router);

	// 微信等追加到根地址后面参数情况处理;  domain.com/?a=1&b=2; 
	if( count($remote) == 1 && 
		$remote[0] == $router &&
		isset($return[$router]) &&
		$return[$router] !='' ){
		$router = '';
		$remote = array('');
	}
	
	$return['URLrouter'] = $router;
	$return['URLremote'] = $remote;
	// pr($_GET,$_POST,$_COOKIE,$return);exit;
	return $return;
} 

function db_escape($str) {
	$str = addslashes($str);
	$str = str_replace(array('_', '%'),array('\\_', '\\%'), $str);
	return $str;
}

/**
 * 获取输入参数 支持过滤和默认值
 * 使用方法:
 * <code>
 * in('id',0); 获取id参数 自动判断get或者post
 * in('post.name','','htmlspecialchars'); 获取$_POST['name']
 * in('get.'); 获取$_GET
 * </code> 
 * @param string $name 变量的名称 支持指定类型
 * @param mixed $default 不存在的时候默认值
 * @param mixed $filter 参数过滤方法
 * @return mixed
 */
function in($name,$default='',$filter=null) {
	$default_filter = 'htmlspecialchars,db_escape';
	if(strpos($name,'.')) { // 指定参数来源
		list($method,$name) = explode('.',$name,2);
	}else{ // 默认为自动判断
		$method = 'request';
	}
	switch(strtolower($method)) {
		case 'get'     :   $input =& $_GET;break;
		case 'post'    :   $input =& $_POST;break;
		case 'request' :   $input =& $_REQUEST;   break;

		case 'put'     :   parse_str(file_get_contents('php://input'), $input);break;
		case 'session' :   $input =Session::get();   break;
		case 'cookie'  :   $input =& $_COOKIE;    break;
		case 'server'  :   $input =& $_SERVER;    break;
		case 'globals' :   $input =  $GLOBALS; break;
		default:return NULL;
	}
	$filters = isset($filter)?$filter:$default_filter;
	if($filters) {
		$filters = explode(',',$filters);
	}
	if(empty($name)) { // 获取全部变量
		$data = $input; 
		foreach($filters as $filter){
			$data = array_map($filter,$data); // 参数过滤
		}
	}elseif(isset($input[$name])) { // 取值操作
		$data =	$input[$name];
		foreach($filters as $filter){
			if(function_exists($filter)) {
				$data = is_array($data)?array_map($filter,$data):$filter($data); // 参数过滤
			}else{
				$data = filter_var($data,is_int($filter)?$filter:filter_id($filter));
				if(false === $data) {
					return isset($default)?$default:NULL;
				}
			}
		}
	}else{ // 变量默认值
		$data = isset($default)?$default:NULL;
	}
	return $data;
}


function url2absolute($index_url, $preg_url){
	if (preg_match('/[a-zA-Z]*\:\/\//', $preg_url)) return $preg_url;
	preg_match('/([a-zA-Z]*\:\/\/.*)\//', $index_url, $match);
	$index_url_temp = $match[1];

	foreach(explode('/', $preg_url) as $key => $var) {
		if ($key == 0 && $var == '') {
			preg_match('/([a-zA-Z]*\:\/\/[^\/]*)\//', $index_url, $match);
			$index_url_temp = $match[1] . $preg_url;
			break;
		} 
		if ($var == '..') {
			preg_match('/([a-zA-Z]*\:\/\/.*)\//', $index_url_temp, $match);
			$index_url_temp = $match[1];
		} elseif ($var != '.') $index_url_temp .= '/' . $var;
	} 
	return $index_url_temp;
}

// 输出js
function exec_js($js){
	echo "<script language='JavaScript'>\n" . $js . "</script>\n";
} 
// 禁止缓存
function no_cache(){
	header("Pragma:no-cache\r\n");
	header("Cache-Control:no-cache\r\n");
	header("Expires:0\r\n");
} 
// 生成javascript转向
function go_url($url, $msg = ''){
	header("Content-type: text/html; charset=utf-8\r\n");
	echo "<script type='text/javascript'>\n";
	echo "window.location.href='$url';";
	echo "</script>\n";
	exit;
} 

function send_http_status($i_status, $s_message = ''){
	$a_status = array(
		// Informational 1xx
		100 => 'Continue',
		101 => 'Switching Protocols', 
		// Success 2xx
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content', 
		// Redirection 3xx
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found', // 1.1
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy', // 306 is deprecated but reserved
		307 => 'Temporary Redirect', 
		// Client Error 4xx
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed', 
		// Server Error 5xx
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		509 => 'Bandwidth Limit Exceeded'
		);

	if (array_key_exists($i_status, $a_status)) {
		header('HTTP/1.1 ' . $i_status . ' ' . $a_status[$i_status]);
	} 
	if ($s_message) {
		echo $s_message;
		exit();
	} 
} 

//是否是windows
function client_is_windows(){
	static $is_windows;
	if(!is_array($is_windows)){
		$is_windows = array(0);
		$os = get_os();
		if(strstr($os,'Windows')){
			$is_windows = array(1);
		}	
	}	
	return $is_windows[0];
}

function is_ajax(){
	if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])=="xmlhttprequest"){
		return true;
	}
	return false;
}

// 获取操作系统信息 TODO
function get_os (){
	$agent = $_SERVER['HTTP_USER_AGENT'];
	$preg_find = array(
		"Windows 95"	=>array('win','95'),
		"Windows ME"	=>array('win 9x','4.90'),
		"Windows 98"	=>array('win','98'),
		"Windows 2000"	=>array('win','nt 5.0',),
		"Windows XP"	=>array('win','nt 5.1'),
		"Windows Vista"	=>array('win','nt 6.0'),
		"Windows 7"		=>array('win','nt 6.1'),
		"Windows 32"	=>array('win','32'),
		"Windows NT"	=>array('win','nt'),
		"Mac OS"		=>array('Mac OS'),
		"Linux"			=>array('linux'),
		"Unix"			=>array('unix'),
		"SunOS"			=>array('sun','os'),
		"IBM OS/2"		=>array('ibm','os'),
		"Macintosh"		=>array('Mac','PC'),
		"PowerPC"		=>array('PowerPC'),
		"AIX"			=>array('AIX'),
		"HPUX"			=>array('HPUX'),
		"NetBSD"		=>array('NetBSD'),
		"BSD"			=>array('BSD'),
		"OSF1"			=>array('OSF1'),
		"IRIX"			=>array('IRIX'),
		"FreeBSD"		=>array('FreeBSD'),
	);

	$os='';
	foreach ($preg_find as $key => $value) {
		if(count($value)==1 && stripos($agent,$value[0])){
			$os=$key;break;
		}else if(count($value)==2 
				 && stripos($agent,$value[0])
				 && stripos($agent,$value[1])
				 ){
			$os=$key;break;
		}
	}
	if ($os=='') {$os = "Unknown"; }
	return $os;
}

function get_broswer(){
    $agent = $_SERVER['HTTP_USER_AGENT']; //获取用户代理字符串
    $pregFind = array(
        "Firefox" => array("/Firefox\/([^;)]+)+/i"),
        "Maxthon" => array("/Maxthon\/([\d\.]+)/", "傲游"),
        "MSIE" => array("/MSIE\s+([^;)]+)+/i", "IE"),
        "OPR" => array("/OPR\/([\d\.]+)/", "Opera"),
        "Edge" => array("/Edge\/([\d\.]+)/"),
        "Chrome" => array("/Chrome\/([\d\.]+)/"),
        "rv:" => array("/rv:([\d\.]+)/", "IE", 'Gecko'),
    );
    $broswer = '';
    foreach ($pregFind as $key => $value) {
        if (stripos($agent, $key)) {
            if (count($value) == 3) {
                if (!stripos($agent, $value[2])) {
                    break;
                }
            }
            $name = count($value) == 1 ? $key : $value[1];
            preg_match($value[0], $agent, $match);
			$broswer = $name . '(' . $match[1] . ')';
			return $broswer;
        }
    }
    if ($broswer == '') {$broswer = "Unknown";}
    return $broswer;
}

// 浏览器是否直接打开
function mime_support($mime){
	$arr_start = array(
		"text/",
		"image/",
		"audio/",
		"video/",
		"message/",
	);
	$arr_mime = array(
		"application/hta",
		"application/javascript",
		"application/json",
		"application/x-latex",
		"application/pdf",
		"application/x-shockwave-flash",
		"application/x-tex",
		"application/x-texinfo"
	);
	if(in_array($mime,$arr_mime)){
		return true;
	}
	foreach ($arr_start as $val) {
		if(substr($mime,0,strlen($val)) == $val){
			return true;
		}
	}
	return false;
}
function get_file_ext_by_mime($contentType){
	$mimetypes = mime_array();
	$contentType = trim(strtolower($contentType));
	foreach ($mimetypes as $ext => $out){
		if($contentType == $out){
			return $ext;
		}
	}
	return 'txt';
}

//根据扩展名获取mime
function get_file_mime($ext){
	$mimetypes = mime_array();
	if (array_key_exists($ext,$mimetypes)){
		$result = $mimetypes[$ext];
		if($result == 'text/html'){
			// $result = "text/plain"; //禁用html网页输出;
		}
		return $result;
	}else{
		if(is_text_file($ext)){
			return "text/plain";
		}
		return 'application/octet-stream';
	}
}

function mime_array(){
	return array(
		"323" => "text/h323",
		"3gp" => "video/3gpp",
		"acx" => "application/internet-property-stream",
		"ai"  => "application/postscript",
		"aif" => "audio/x-aiff",
		"aifc" => "audio/x-aiff",
		"aiff" => "audio/x-aiff",
		"asf" => "video/x-ms-asf",
		"asr" => "video/x-ms-asf",
		"asx" => "video/x-ms-asf",
		"au" => "audio/basic",
		"avi" => "video/x-msvideo",
		"axs" => "application/olescript",
		"bas" => "text/plain",
		"bcpio" => "application/x-bcpio",
		"bin" => "application/octet-stream",
		"bmp" => "image/bmp",
		"c" => "text/plain",
		"cat" => "application/vnd.ms-pkiseccat",
		"cdf" => "application/x-cdf",
		"cer" => "application/x-x509-ca-cert",
		"class" => "application/octet-stream",
		"clp" => "application/x-msclip",
		"cmx" => "image/x-cmx",
		"cod" => "image/cis-cod",
		"cpio" => "application/x-cpio",
		"crd" => "application/x-mscardfile",
		"crl" => "application/pkix-crl",
		"crt" => "application/x-x509-ca-cert",
		"csh" => "application/x-csh",
		"css" => "text/css",
		"dcr" => "application/x-director",
		"der" => "application/x-x509-ca-cert",
		"dir" => "application/x-director",
		"dll" => "application/x-msdownload",
		"dms" => "application/octet-stream",
		"doc" => "application/msword",
		"docx" => "application/msword",
		"dot" => "application/msword",
		"dvi" => "application/x-dvi",
		"dxr" => "application/x-director",
		"eps" => "application/postscript",
		"etx" => "text/x-setext",
		"evy" => "application/envoy",
		"exe" => "application/octet-stream",
		"fif" => "application/fractals",
		"flr" => "x-world/x-vrml",
		"flv" => "video/x-flv",
		"f4v" => "video/x-flv",
		// "f4v" => "application/octet-stream",
		"gif" => "image/gif",
		"gtar" => "application/x-gtar",
		"gz" => "application/x-gzip",
		"h" => "text/plain",
		"hdf" => "application/x-hdf",
		"hlp" => "application/winhlp",
		"hqx" => "application/mac-binhex40",
		"hta" => "application/hta",
		"htc" => "text/x-component",
		"htm" => "text/html",
		"html" => "text/html",
		"htt" => "text/webviewhtml",
		"ico" => "image/x-icon",
		"ief" => "image/ief",
		"iii" => "application/x-iphone",
		"ins" => "application/x-internet-signup",
		"isp" => "application/x-internet-signup",
		"jfif" => "image/pipeg",
		"jpg" => "image/jpeg",
		"jpeg" => "image/jpeg",
		"jpe"  => "image/jpeg",
		"heic" => "image/heic",
		"webp" => "image/webp",
		"cur"  => "image/x-icon",
		"apng" => "image/apng",
		"avif" => "image/avif",
		"js" => "application/javascript",
		"json" => "application/json",
		"latex" => "application/x-latex",
		"lha" => "application/octet-stream",
		"lsf" => "video/x-la-asf",
		"lsx" => "video/x-la-asf",
		"lzh" => "application/octet-stream",
		"m13" => "application/x-msmediaview",
		"m14" => "application/x-msmediaview",
		"m3u" => "audio/x-mpegurl",
		'm4a' => "audio/mp4",
		'm4v' => "audio/mp4",
		"man" => "application/x-troff-man",
		"mdb" => "application/x-msaccess",
		"me" => "application/x-troff-me",
		"mht" => "message/rfc822",
		"mhtml" => "message/rfc822",
		"mid" => "audio/mid",
		"mka" => "audio/x-matroska",
		"mkv" => "video/x-matroska",
		"mny" => "application/x-msmoney",
		"mov" => "video/quicktime",
		"movie" => "video/x-sgi-movie",
		"mp2" => "video/mpeg",
		"mp3" => "audio/mpeg",
		"mp4" => "video/mp4",
		"mp4v" => "video/x-m4v",
		"mpa" => "video/mpeg",
		"mpe" => "video/mpeg",
		"mpeg" => "video/mpeg",
		"mpg" => "video/mpeg",
		"mpp" => "application/vnd.ms-project",
		"mpv2" => "video/mpeg",
		"ms" => "application/x-troff-ms",
		"mvb" => "application/x-msmediaview",
		"nws" => "message/rfc822",
		"oda" => "application/oda",
		"ogg" => "audio/ogg",
		"oga" => "audio/ogg",
		"ogv" => "audio/ogg",
		"p10" => "application/pkcs10",
		"p12" => "application/x-pkcs12",
		"p7b" => "application/x-pkcs7-certificates",
		"p7c" => "application/x-pkcs7-mime",
		"p7m" => "application/x-pkcs7-mime",
		"p7r" => "application/x-pkcs7-certreqresp",
		"p7s" => "application/x-pkcs7-signature",
		"pbm" => "image/x-portable-bitmap",
		"pdf" => "application/pdf",
		"pfx" => "application/x-pkcs12",
		"pgm" => "image/x-portable-graymap",
		"pko" => "application/ynd.ms-pkipko",
		"pma" => "application/x-perfmon",
		"pmc" => "application/x-perfmon",
		"pml" => "application/x-perfmon",
		"pmr" => "application/x-perfmon",
		"pmw" => "application/x-perfmon",
		"png" => "image/png",
		"pnm" => "image/x-portable-anymap",
		"pot," => "application/vnd.ms-powerpoint",
		"ppm" => "image/x-portable-pixmap",
		"pps" => "application/vnd.ms-powerpoint",
		"ppt" => "application/vnd.ms-powerpoint",
		"pptx" => "application/vnd.ms-powerpoint",
		"plist"=> "text/xml",
		"ipa" =>"application/octet-stream",
		"prf" => "application/pics-rules",
		"ps" => "application/postscript",
		"pub" => "application/x-mspublisher",
		"qt" => "video/quicktime",
		"ra" => "audio/x-pn-realaudio",
		"ram" => "audio/x-pn-realaudio",
		"ras" => "image/x-cmu-raster",
		"rgb" => "image/x-rgb",
		"rmi" => "audio/mid",
		"roff" => "application/x-troff",
		"rtf" => "application/rtf",
		"rtx" => "text/richtext",
		"scd" => "application/x-msschedule",
		"sct" => "text/scriptlet",
		"setpay" => "application/set-payment-initiation",
		"setreg" => "application/set-registration-initiation",
		"sh" => "application/x-sh",
		"shar" => "application/x-shar",
		"sit" => "application/x-stuffit",
		"snd" => "audio/basic",
		"spc" => "application/x-pkcs7-certificates",
		"spl" => "application/futuresplash",
		"src" => "application/x-wais-source",
		"sst" => "application/vnd.ms-pkicertstore",
		"stl" => "application/vnd.ms-pkistl",
		"stm" => "text/html",
		"svg" => "image/svg+xml",
		"sv4cpio" => "application/x-sv4cpio",
		"sv4crc" => "application/x-sv4crc",
		"swf" => "application/x-shockwave-flash",
		"t" => "application/x-troff",
		"tar" => "application/x-tar",
		"tcl" => "application/x-tcl",
		"tex" => "application/x-tex",
		"texi" => "application/x-texinfo",
		"texinfo" => "application/x-texinfo",
		"tgz" => "application/x-compressed",
		"tif" => "image/tiff",
		"tiff" => "image/tiff",
		"tr" => "application/x-troff",
		"trm" => "application/x-msterminal",
		"tsv" => "text/tab-separated-values",
		"txt" => "text/plain",
		"uls" => "text/iuls",
		"ustar" => "application/x-ustar",
		"vcf" => "text/x-vcard",
		"vrml" => "x-world/x-vrml",
		"wav" => "audio/wav",
		"wcm" => "application/vnd.ms-works",
		"wdb" => "application/vnd.ms-works",
		"webm" => "video/webm",
		"webmv" => "video/webm",
		"wks" => "application/vnd.ms-works",
		"wmf" => "application/x-msmetafile",
		"wps" => "application/vnd.ms-works",
		"wri" => "application/x-mswrite",
		"wrl" => "x-world/x-vrml",
		"wrz" => "x-world/x-vrml",
		"xaf" => "x-world/x-vrml",
		"xbm" => "image/x-xbitmap",
		"xla" => "application/vnd.ms-excel",
		"xlc" => "application/vnd.ms-excel",
		"xlm" => "application/vnd.ms-excel",
		"xls" => "application/vnd.ms-excel",
		"xlsx" => "application/vnd.ms-excel",
		"xlt" => "application/vnd.ms-excel",
		"xlw" => "application/vnd.ms-excel",
		"xof" => "x-world/x-vrml",
		"xpm" => "image/x-xpixmap",
		"xwd" => "image/x-xwindowdump",
		"z" => "application/x-compress",
		"zip" => "application/zip"
	);
}
