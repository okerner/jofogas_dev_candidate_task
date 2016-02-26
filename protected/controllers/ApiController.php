<?php

class ApiController extends Controller
{
	private $flickr_api_key = '57f694132e4714c29a64c9af890b124e';
 
    /**
     * Default response format
     * either 'json' or 'xml'
     */
    private $format = 'json';

    // Actions
    public function actionCategories()
    {
    	$all_cats = Tbl_categories::model()->findAll();
    	$this->_sendResponse(200, CJSON::encode($all_cats), 'application/json');
    }

    public function actionSearch()
    {
		// Check if query_string was submitted via GET
		
	    if(!isset($_GET['query_string']))
    	    $this->_sendResponse(500, 'Error: Parameter <b>query_string</b> is missing' );
    	
    	//query url for flickr
    	$url = "https://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=".$this->flickr_api_key."&format=".$this->format."&nojsoncallback=1"."&text=".$_GET['query_string'].((isset($_GET['page']))?'&page='.$_GET['page']:'').((isset($_GET['per_page']))?'&per_page='.$_GET['per_page']:'');
    	
		$curl_handle = curl_init ($url);

		curl_setopt ($curl_handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($curl_handle, CURLOPT_HEADER, FALSE);

		$response = curl_exec ($curl_handle);
		curl_close($curl_handle);
		
    	//json decode flickr's response
		$photos = CJSON::decode($response,TRUE);

    	//setup my response
		$images_with_short_url = array('photos'=>[], 'page'=>$photos['photos']['page'], 'per_page'=>$photos['photos']['perpage'], 'pages'=>$photos['photos']['pages']);
		
		foreach($photos['photos']['photo'] as $one_photo){
			$images_with_short_url['photos'][]=array('url'=>$this->flickr_image_url($one_photo),'title'=>$one_photo['title']);
		}

    	//return my response
    	$this->_sendResponse(200, CJSON::encode($images_with_short_url), 'application/json');

    }

    public function actionCreate()
    {
		// Check if cat_name was submitted via POST
		
		print_r($_POST);
		
	    if(!isset($_POST['cat_name']))
    	    $this->_sendResponse(500, 'Error: Parameter <b>cat_name</b> is missing' );
    	    
    	$model = new Tbl_categories();
    	
    	$model->cat_name=$_POST['cat_name'];
    	if (isset($_POST['parent_id'])) $model->parent_id = $_POST['parent_id'];
		
		if($model->validate())
		{
			$model->save();
    	    $this->_sendResponse(200, 'OK' );
		}
		else {
    	    $this->_sendResponse(500, 'Error: Parameter <b>cat_name</b> is missing' );
		}
    }
    
    public function actionUpdate()
    {
		// Check if id was submitted via POST
		
	    if(!isset($_POST['id']))
    	    $this->_sendResponse(500, 'Error: Parameter <b>id</b> is missing' );
    	    
    	$model = new Tbl_categories();
		$model = $model->findByPk($_POST['id']);
    	
    	if (isset($_POST['parent_id'])) $model->parent_id = $_POST['parent_id'];
    	if (isset($_POST['cat_name'])) $model->cat_name = $_POST['cat_name'];
    	else $model->cat_name = $model->cat_name;
		
		if($model->validate())
		{
			$model->save();
    	    $this->_sendResponse(200, 'OK' );
		}
		else {
    	    $this->_sendResponse(500, 'Error: Parameter <b>id</b> is missing' );
		}
    }
    public function actionDelete()
    {
		// Check if id was submitted via DELETE
		
	    if(!isset($_POST['id']))
    	    $this->_sendResponse(500, 'Error: Parameter <b>id</b> is missing' );
    	    
    	$model = new Tbl_categories();
		$model = $model->findByPk($_POST['id']);
		$model->delete();
    }
    
    private function base_encode($num, $alphabet='123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ') {
		$base_count = strlen($alphabet);
		$encoded = '';
		while ($num >= $base_count) {
			$div = $num/$base_count;
			$mod = ($num-($base_count*intval($div)));
			$encoded = $alphabet[$mod] . $encoded;
			$num = intval($div);
		}
		
		if ($num) $encoded = $alphabet[$num] . $encoded;

		return $encoded;
	}

	private function short_flickr_url($flickr_img_id)
	{
		return 'http://flic.kr/p/'.$this->base_encode($flickr_img_id);
	}
	
	private function flickr_image_url($photo)
	{
		return 'http://farm' . $photo["farm"] . '.static.flickr.com/' . $photo["server"] . '/' . $photo["id"] . '_' . $photo["secret"] . '.jpg"';
	}
	
	private function _getStatusCodeMessage($status)
	{
    	// these could be stored in a .ini file and loaded
	    // via parse_ini_file()... however, this will suffice
    	// for an example
	    $codes = Array(
        	200 => 'OK',
    	    400 => 'Bad Request',
	        401 => 'Unauthorized',
        	402 => 'Payment Required',
    	    403 => 'Forbidden',
	        404 => 'Not Found',
        	500 => 'Internal Server Error',
    	    501 => 'Not Implemented',
	    );
    	return (isset($codes[$status])) ? $codes[$status] : '';
	}
	private function _sendResponse($status = 200, $body = '', $content_type = 'text/html')
	{
    	// set the status
    	$status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
    	header($status_header);
    	// and the content type
    	header('Content-type: ' . $content_type);
 
    	// pages with body are easy
    	if($body != '')
    	{
        	// send the body
        	echo $body;
    	}
    	// we need to create the body if none is passed
    	else
    	{
        	// create some body messages
	        $message = '';
 	
    	    // this is purely optional, but makes the pages a little nicer to read
        	// for your users.  Since you won't likely send a lot of different status codes,
	        // this also shouldn't be too ponderous to maintain
    	    switch($status)
        	{
	            case 401:
    	            $message = 'You must be authorized to view this page.';
        	        break;
	            case 404:
    	            $message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
        	        break;
	            case 500:
    	            $message = 'The server encountered an error processing your request.';
        	        break;
            	case 501:
	                $message = 'The requested method is not implemented.';
    	            break;
	        }
 
    	    // servers don't always have a signature turned on 
	        // (this is an apache directive "ServerSignature On")
    	    $signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];
 
	        // this should be templated in a real-world solution
    	    $body = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
   	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
   	<title>' . $status . ' ' . $this->_getStatusCodeMessage($status) . '</title>
</head>
<body>
   	<h1>' . $this->_getStatusCodeMessage($status) . '</h1>
    <p>' . $message . '</p>
   	<hr />
    <address>' . $signature . '</address>
</body>
</html>';
 
			echo $body;
	    }
    	Yii::app()->end();
	}
	
}