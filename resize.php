<?php

	/*ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);*/

	use Resizer\lib\Constant;
	use Resizer\lib\Resizer;

	require_once __DIR__. "/lib/Constant.php";
	require_once __DIR__. "/lib/Resizer.php";

	if(!empty($_POST)) {
		$data = NULL;
		$error = NULL;
		$imageData = array();
		$default = new Constant();
		$imageData['generateThumbnail'] = $default->getGenerateThumbnail();
		$imageData['thumbnailSize'] = $default->getThumbnailSize();
		$imageData['thumbnailName'] = $default->getthumbnailName();
		$imageData['destinationFolder'] = $default->getDestinationFolder();
		$imageData['maxSize'] = $default->getMaxSize();
		$imageData['allowedSize'] = $default->getAllowedSize();
		$imageData['quality'] = $default->getQuality();
		$imageData['imageData'] = !empty($_POST['url']) ? $_POST['url'] : $_FILES['file'];
		$imageData['type'] = !empty($_POST['url']) ? 'url' : 'local';
		try {
			$resize = new Resizer($imageData);
			$data = $resize->resize();
			echo json_encode(array('message'=>'uploaded sucessfully')); 
			exit();
		} catch (Exception $e) {
			header('HTTP/1.1 400 Bad Request'); 
			echo json_encode(array('message'=>$e->getMessage()));
			exit();
		}
		//echo'<pre>'.print_r($data,TRUE).'</pre>';
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Resizer</title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="./assets/css/style.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<script src="./assets/js/script.js"></script>
	</head>
	<body>
		<div id="loader"></div>
		<h2>Resizer</h2>
		<div id="msg">Hello</div>
		<p>This script will resize jpeg, png and gif files and it will also create thumbnails for the same.The destination directory will be created if it doesn’t exist.</p>
		<div style="margin-top: 10px;">
			<form method="post" enctype="multipart/form-data" id="submit">
				<div>
					<label for="file">File Source</label>
					<input type="text" name="url" id="url">
					<input type="file" name="file[]" id="local" multiple="multiple" style="width: 28%;">
					<span id="source-type">
						<a href="#" class="source" data-source-type="url">Paste Image URL</a>
						<span>||</span>
						<a href="#" class="source" data-source-type="local">Upload Image</a>
					</span>
				</div>
				<div style="margin-top: 10px;" id="resize">
					<input type="submit" name="submit" value="Resize">
				</div>
			</form>
		</div>
		<p>Once you have pressed Resize please be patient as it may take some to resize your images depending on the number of images passed.</p>
	</body>
</html>
