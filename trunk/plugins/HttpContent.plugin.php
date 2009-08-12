<?php
//always include plugin class in the top of all plugins
class_exists('Plugin', false) or include(dirname(dirname(__FILE__)).'/classes/Plugin.class.php');
class_exists('Timer', false) or include(dirname(dirname(__FILE__)).'/classes/Timer.class.php');

class HttpContentPlugin extends Plugin {

	//this is default input, prefilled when monitor instance is setup - input is stored in sqllite for the monitor and are only used here
	public static $rawInput =
"url = http://www.sampleurl.com/totest.php   ; URL for HTTP request
maxConnectTimeoutSeconds = 10               ; socket connection timeout
maxRequestTimeoutSeconds = 10               ; request timeout
goodContent = SERVER-OK                     ; if it doesn't see this always bad
badContent = ERROR                          ; if sees this always bad
attempts = 1                                ; # of attempts to find full success before reporting failure
attemptWait = 0                             ; ms to wait between attempts - default 0 no wait
";

	public function about() {
		return array(
			'name'=>'HttpContent',
			'description'=>'Grabs content a web page and searches content for proper strings indicating success or error.',
			'author'=>'mikerlynn',
			'version'=>'1.0'
		);
	}

	/*
	$input is array taken from single input field of values to be used, returns output
	*/
	public function runPlugin($input=array()) {
		$badContent=1;
		$goodContent=1;
		
		for($i=$input['attempts']; $i <= $input['attempts']; $i++){
			$output=Plugin::$output;///set defaults for all output
			$t = new Timer();
			$t->start();
			$output['returnContent'] = HttpContentPlugin::doHTTPGet($input['url'],$input['maxConnectTimeoutSeconds'],$input['maxRequestTimeoutSeconds']);
			$output['responseTimeMs'] = (int)$t->stop();
			$output['measuredValue']=$output['responseTimeMs'];
			if (trim($input['goodContent'])!='') $goodContent = (strpos($output['returnContent'], $input['goodContent'])===false) ? 0 : 1;
			if(trim($input['badContent'])!='') $badContent  = (strpos($output['returnContent'], $input['badContent'])===false) ? 1 : 0;
			
			//default to down
			$output['currentStatus']= 0;
			
			//if its already bad, then its bad.
			if (($goodContent+$badContent) == 2) $output['currentStatus'] = 1;
			if ($output['currentStatus']==1){
				//we've got what we wanted
				break;
			} else {
				//else keep going till we do or hit max attempts
				if( (isset($input['attemptWait'])) && ($input['attemptWait']!==0) )
					usleep($input['attemptWait']*1000);
			}
			
		}

		//html email
		$output['htmlEmail'] = 1;
		return $output;
	}

	private static function doHTTPGet($requestURL, $connectTimeout = 15, $requestTimeout = 15) {
		$con = curl_init();
		curl_setopt($con, CURLOPT_URL, $requestURL);
		curl_setopt($con, CURLOPT_HEADER, false);
		curl_setopt($con, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($con, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($con, CURLOPT_BUFFERSIZE, 16384);
		curl_setopt($con, CURLOPT_CONNECTTIMEOUT, (int)$connectTimeout);
		curl_setopt($con, CURLOPT_TIMEOUT, (int)$requestTimeout);
		curl_setopt($con, CURLOPT_FAILONERROR, true);
		$headers = array(
				'Accept-Language: en-us,en;q=0.5',
				'Cache-Control: max-age=0',
				'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
				'Accept-Encoding:',
				'User-Agent: Mozilla/5.0',
				'Keep-Alive: 300'
			);
		curl_setopt($con, CURLOPT_HTTPHEADER, $headers);

		try {
			$data = curl_exec($con);
		} catch (Exception $e) {
			return 'http error - '.$e->getMessage();//this makes sure it always returns content if there's an error
		}
		curl_close($con);
		return $data;
	}
}
?>
