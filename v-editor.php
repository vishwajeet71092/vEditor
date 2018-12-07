<?php 
	//set session
	session_start();

	//seting error report off
	
	// ini_set('display_errors', 1);
	// error_reporting(E_ALL);
	
	ini_set('display_errors', 0);
	error_reporting(0);
?>

<?php  
	//current directory where editor file exist
	$currentDir = __DIR__;

	//seperator for creating url
	$seprator = DIRECTORY_SEPARATOR;
?>

<?php 
	//functions for set base section
	
	//function to display folder while selecting base folder
	function displaySetBase($path){
		global $seprator;
		//scaning path
		$dir = scandir($path);
		//removing . and .. folder from array
		$cleanDir = array_diff($dir, array('.','..'));
		$allfileList = "";

		// loop through all the file and folder
		foreach ($cleanDir as $name) {
			//create full path
			$isDir = $path.$seprator.$name;
			//check for directory
			if(is_dir($isDir)){
				echo "<li data-path='$path$seprator$name' class='inDir'><i class='fas fa-folder'></i> $name</li>";
			}else{
				$allfileList .= "<li class='inFile'><i class='far fa-file-code'></i> $name</li>";
			}
		}

		echo $allfileList;
	}

	//function to display folder in file manager
	function displayFileManager($path){
		global $seprator;
		//scaning path
		$dir = scandir($path);
		//removing . and .. folder from array
		$cleanDir = array_diff($dir, array('.','..'));
		$filelist = "";

		// loop through all the file and folder
		foreach ($cleanDir as $name) {
			//create full path
			$isDir = $path.$seprator.$name;
			//check for directory
			if(is_dir($isDir)){
				echo "<div><li data-path='$path$seprator$name' class='inDir'><i class='fas fa-folder'></i> $name</li> <a href='javascript:void(0);' data-path='$path$seprator' data-name='$name' onclick='renamefolderfuc(this)'><i class='fas fa-pencil-alt'></i></a> <a href='javascript:void(0);' data-path='$path$seprator' data-name='$name' onclick='deletefolderfuc(this)'><i class='far fa-trash-alt'></i></a></div>";
			}else{
				$filelist.= "<div><li class='inFile'><i class='far fa-file-code'></i> $name</li>  <a href='javascript:void(0);' data-path='$path$seprator' data-name='$name' onclick='renamefilefuc(this)'><i class='fas fa-pencil-alt'></i></a> <a href='javascript:void(0);' data-path='$path$seprator' data-name='$name' onclick='deletefilefuc(this)'><i class='far fa-trash-alt'></i></a></div>";
			}
		}
		echo $filelist;
	}

	//function to create back url
	function backurl($url, $seprator){
		return substr($url, 0, strrpos($url, $seprator));
	}
?>

<?php  
	//code for ajax request of set base on clicking folder
	if(isset($_POST['ajax']) && $_POST['ajax']=="true"){
		$ajaxPath = $_POST['path'];
		displaySetBase($ajaxPath);
		exit();
	}

	//code for ajax request of filemanager
	if(isset($_POST['ajaxFileManager']) && $_POST['ajaxFileManager']=="true"){
		$ajaxPath = $_POST['path'];
		displayFileManager($ajaxPath);
		exit();
	}
	
	//ajax request for file contaent loading in editor
	if(isset($_POST['loadfileajax']) && $_POST['loadfileajax']=="true"){
		$ajaxPath = $_POST['path'];
		echo file_get_contents($ajaxPath);
		exit();
	}
	//request for save file
	if(isset($_POST['SaveBtn'])){
		$filePath = $_POST['savepath'];
		$fileContent = $_POST['savecontent'];
		$myfile = fopen($filePath, "w") or die("Unable to open file!");
		fwrite($myfile, $fileContent);
		fclose($myfile);
		//echo "success";
	}

	//ajax request for dir content on click any dir in sidebar
	if(isset($_POST['loaddirajax']) && $_POST['loaddirajax']=="true"){
		$ajaxPath = $_POST['path'];
		dirListSidebar($ajaxPath);
		exit();
	}
	
?>

<?php 

	//form processing section

	//check if login form is submitted
	
	$loginError = "";
	if(isset($_POST['submitPassword'])){
		if(md5($_POST['password']) == '5f4dcc3b5aa765d61d8327deb882cf99'){
			$_SESSION["login"] = "true";
		}else{
			$loginError = "Password is not correct !";
		}
	}

	//set base form process
	if(isset($_POST['setBaseForm'])){
		$setbase = $_POST['currentDir'];
		$_SESSION["baseDir"] = $setbase;
	}
	//logout code
	if(isset($_POST['logoutBtn'])){
		unset($_SESSION["baseDir"]);
		unset($_SESSION["login"]);
	}
	//reset base
	if(isset($_POST['changeBaseBtn'])){
		unset($_SESSION["baseDir"]);
	}
	
?>

<?php 
	//directory listing function for side bar
	function dirListSidebar($dir){
		echo "<ul id='sidebarUL'>";
		global $seprator;
		//scaning path
		$dirList = scandir($dir);
		//removing . and .. folder from array
		$cleanDir = array_diff($dirList, array('.','..'));

		// loop through all the folder
		foreach ($cleanDir as $name) {
			//create full path
			$isDir = $dir.$seprator.$name;
			//check for directory
			if(is_dir($isDir)){
				echo "<li class='inDir'><a href='javascript:void(0);' data-path='$isDir' onclick='loadDirAjax(this)'><i class='fas fa-folder'></i> $name</a>";
			}
			// the two line is for scanning directory recersivly
			// $subdir =$dir.$seprator.$name;
			// dirListSidebar($subdir);
			echo "</li>";
		}

		// loop through all the file
		foreach ($cleanDir as $name) {
			//create full path
			$isDir = $dir.$seprator.$name;
			//check for directory
			if(!is_dir($isDir)){
				echo "<li class='inFile'><a href='javascript:void(0);' data-path='$isDir' onclick='loadFileAjax(this)'><i class='far fa-file-code'></i> $name</a></li>";
			}
		}
		echo "</ul>";
	}
?>

<?php
	// check if login session is set

	if(!isset($_SESSION["login"]) || $_SESSION["login"] != "true"){
		?>


<!-- html for login page -->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>V Editor</title>
	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
	<style>
		body{
			margin: 0px;
			padding: 0px;
			font-family: 'Source Sans Pro', sans-serif;
		}
		#login_body{
			background: #34373c;
		    width: 100%;
		    height: 100vh;
		    overflow: hidden;
		    display: flex;
		    align-items: center;
  			justify-content: center;
		}
		#login_body input[type='password']{
			background: #1d1e22;
			padding: 15px 20px;
		    border: 0px;
		    color: #888888;
		}
		#login_body input[type='submit']{
			background: #229bf1;
			padding: 15px 20px;
		    border: 0px;
		    color: white;
		}
	</style>
</head>
<body>
	<div id="login_body">
		<div id="login_form_wrapper">
			<form action="" method="post">
				<input type="password" placeholder="<?php if($loginError!=''){echo $loginError;}else{echo "Enter Password";} ?>" id="password_text" name="password">
				<input type="submit" value=">>" id="password_button" name="submitPassword">
			</form>
		</div>
	</div>
</body>
</html>

<?php 
	}elseif($_SESSION["login"] == "true" && !isset($_SESSION["baseDir"])){
		?>
		<!-- html for select base folder page -->
		<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>V Editor</title>
	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
	<style>
		body{
			margin: 0px;
			padding: 0px;
			font-family: 'Source Sans Pro', sans-serif;
		}
		#login_body{
			background: #34373c;
		    width: 100%;
		    height: 100vh;
		    overflow: hidden;
		    display: flex;
		    align-items: center;
  			justify-content: center;
		}
		.inFile .far {
		    color: #229bf1;
		}
		.inFile{
			cursor: default;
		}
		.inDir{
			cursor: pointer;
			display:table;
		}
		#login_body input[type='password']{
			background: #1d1e22;
			padding: 15px 20px;
		    border: 0px;
		    color: #888888;
		}
		#login_body input[type='submit']{
			background: #229bf1;
			padding: 15px 20px;
		    border: 0px;
		    color: white;
		}
		#select_base_body{
			background: #34373c;
		    width: 100%;
		    height: 100vh;
		    overflow: hidden;
		    display: flex;
		    align-items: center;
  			justify-content: center;
  			flex-flow: column;
		}
		#select_base_body h2{
			color: #bbbbbb;
			font-size: 20px;
		}
		#select_base_wrapper{
			height: 70vh;
    		overflow: auto;
    		width: 80%;
    		max-width: 500px;
    		border: 1px solid #4f5052;
    		background: #1d1e22;
    		color: #888;
		}
		#select_base_wrapper ul li{
			list-style-type: none;
			padding: 4px 5px;
    		font-size: 16px;
		}
		#select_base_wrapper ul li .fa-folder{
			color: #d83e34;
		}
		#select_base_wrapper::-webkit-scrollbar-track{
			-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
			background-color: #4f5052;
			border-radius: 10px;
		}

		#select_base_wrapper::-webkit-scrollbar{
			width: 10px;
			background-color: #4f5052;
		}

		#select_base_wrapper::-webkit-scrollbar-thumb{
			border-radius: 10px;
			background-image: -webkit-gradient(linear,
											   left bottom,
											   left top,
											   color-stop(0.44, #727272),
											   color-stop(0.72, #626262),
											   color-stop(0.86, #626262));
		}
		#base_form_wrapper{
			display: flex;
			justify-content: space-around;
			width: 80%;
    		max-width: 500px;
		}
		#base_form_wrapper input[type='submit']{
			background: #229bf1;
			padding: 15px 20px;
		    border: 0px;
		    color: white;
		    margin-top: 15px;
		}
		#select_base_body h2,#select_base_body h5{
			text-align: center;
			color: #bbbbbb;
		}

	</style>
</head>
<body>
	<div id="select_base_body">
		<div><h2>Select Base Folder</h2><h5>This is the preview of what you will see in sidebar of the editor.</h5></div>
		<div id="select_base_wrapper">
			<ul id="select_base_list">
				<?php displaySetBase($currentDir); ?>
			</ul>
			<form action="" method="post">
				
			</form>
		</div>
		<div id="base_form_wrapper">
			<form action="" onsubmit="return false;">
				<input type="hidden" value="">
				<input type="submit" value="<<" id="back_button" data-path="<?php echo backurl($currentDir, $seprator); ?>">
			</form>

			<form action="" method="post">
				<input type="hidden" name="currentDir" id="baseVal" value="<?php echo $currentDir; ?>">
				<input type="submit" value="Set Base" id="password_button" name="setBaseForm">
			</form>
			
		</div>
	</div>
	<script>
		// get seperator in js
		var seprator = "\<?php echo DIRECTORY_SEPARATOR; ?>";

		//creating back url
		function backurl(url, seprator){
			var n = url.lastIndexOf(seprator);
			return url.substr(0,n);
		}

		//function to load dir
		function loadDir(path){

			//data to send
			var data = "ajax=true&path=" + path;

			// create ajax request
			if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		        xmlhttp=new XMLHttpRequest();
		    } else {// code for IE6, IE5
		        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		    }

		    //check for sucessfull response
		    xmlhttp.onreadystatechange=function() {
		        if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
		        	// change the list
		            document.getElementById("select_base_list").innerHTML=xmlhttp.responseText;
		            //add event listner to updated list
					addEventListrnerToLi();

					var backUrl = backurl(path, seprator);
					//update back btn data value
					document.getElementById('back_button').dataset.path = backUrl;
					//update new path to form 
					document.getElementById('baseVal').value = path;
		        }
		    }


		    xmlhttp.open("POST", "v-editor.php", true); 
			xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");                  
			xmlhttp.send(data);

		}

		// function to add event listener to dir li
		function addEventListrnerToLi(){
			//get all li with folder name
			var getInDir = document.getElementsByClassName("inDir");

			//loop throught all the li
			for (var i = 0; i < getInDir.length; i++) {
		 		//adding click event lintner to all li	
		  		getInDir[i].addEventListener("click", function() {

		    		var dirUrl = this.getAttribute('data-path');
		    		loadDir(dirUrl);
		    	});
		    }
		}

		// calling event lister for first time
		addEventListrnerToLi();

	    // add event listner to back btn
	    var backBtn = document.getElementById('back_button');
	    backBtn.addEventListener("click", function() {

    		var dirUrl = this.getAttribute('data-path');
    		loadDir(dirUrl);
    	});

	</script>
</body>
</html>
		<?php
	//code for file manager	
	}elseif($_SESSION["login"] == "true" && isset($_POST["FileManager"])){
		?>
			<!-- html for file Manager code -->
		<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>V Editor - File Manager</title>
	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
	<style>
		body{
			margin: 0px;
			padding: 0px;
			font-family: 'Source Sans Pro', sans-serif;
		}
		#login_body{
			background: #34373c;
		    width: 100%;
		    height: 100vh;
		    overflow: hidden;
		    display: flex;
		    align-items: center;
  			justify-content: center;
		}
		.inFile .far {
		    color: #229bf1;
		}
		.inFile{
			cursor: default;
			display:inline-block;
		}
		.inDir{
			cursor: pointer;
			display:inline-block;
		}
		#login_body input[type='password']{
			background: #1d1e22;
			padding: 15px 20px;
		    border: 0px;
		    color: #888888;
		}
		#login_body input[type='submit']{
			background: #229bf1;
			padding: 15px 20px;
		    border: 0px;
		    color: white;
		}
		#select_base_body{
			background: #34373c;
		    width: 100%;
		    height: 100vh;
		    overflow: hidden;
		    display: flex;
		    align-items: center;
  			justify-content: center;
  			flex-flow: column;
		}
		#select_base_body h2{
			color: #bbbbbb;
			font-size: 20px;
		}
		#select_base_wrapper{
			height: 60vh;
    		overflow: auto;
    		width: 80%;
    		max-width: 500px;
    		border: 1px solid #4f5052;
    		background: #1d1e22;
    		color: #888;
		}
		#select_base_wrapper ul li{
			list-style-type: none;
			padding: 4px 5px;
    		font-size: 16px;
		}
		#select_base_wrapper ul li .fa-folder{
			color: #d83e34;
		}
		#select_base_wrapper::-webkit-scrollbar-track{
			-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
			background-color: #4f5052;
			border-radius: 10px;
		}

		#select_base_wrapper::-webkit-scrollbar{
			width: 10px;
			background-color: #4f5052;
		}

		#select_base_wrapper::-webkit-scrollbar-thumb{
			border-radius: 10px;
			background-image: -webkit-gradient(linear,
											   left bottom,
											   left top,
											   color-stop(0.44, #727272),
											   color-stop(0.72, #626262),
											   color-stop(0.86, #626262));
		}
		#base_form_wrapper{
			display: flex;
			justify-content: space-around;
			width: 80%;
    		max-width: 500px;
		}
		#base_form_wrapper input[type='submit']{
			background: #229bf1;
			padding: 15px 20px;
		    border: 0px;
		    color: white;
		    margin-top: 15px;
		}
		#optionDiv>form, #optionDiv>a{
			display: inline-block;
			color: #f1f1f1;
		    background-color: #229bf1;
		    font-size: 13px;
		    padding: 5px 10px;
		    text-decoration: none;
		    margin: 0px 5px 10px 5px;
		    font-family: 'Source Sans Pro', sans-serif;
		}
		.fa-pencil-alt{
			    color: limegreen;
			    padding: 0 5px;
			    margin: 8px 5px;
		}
		.fa-trash-alt{
			color: palevioletred;
			padding: 0 5px;
			margin: 8px 5px;
		}
		.actionPopup{
			background-color: grey;
		    position: absolute;
		    top: 0;
		    width: 100%;
		    /* margin: 0 auto; */
		    text-align: center;
		    padding: 50px 0px;
		    display: none;
		}
		.actionPopup input[type='submit'] {
		    background: #229bf1;
		    padding: 8px 20px;
		    border: 0px;
		    color: white;
		    margin-top: 15px;
		}
		.actionPopup input[type='text'] {
			background-color: #34373c;
		    border: 0px;
		    padding: 8px;
		    color: #ececec;
		}
		.actionPopup .far{
			color: white;
			position: absolute;
			top: 15px;
			right: 15px;
			cursor: pointer;
			font-size: 20px;
		}
		.actionPopup h5{
			margin: 0px;
		}
		#msgspan{color: white;
    		background: mediumseagreen;
    		padding: 5px;
    		margin-bottom: 10px;
    	}
	</style>
</head>
<body>
	<?php 					
		$baseFolder = $_SESSION["baseDir"]; 
		$msgFlag=0;
		$msg="";
		if(isset($_POST['actionpath'])){
			$baseFolder = $_POST['actionpath'];
		}
	?>
	<!-- popup for rename file -->
	<?php  
		if(isset($_POST['renamefilesubmit'])){
			$actionpath = $_POST['actionpath'];
			$renamefilename = $_POST['renamefilename'];
			$oldfilename = $_POST['oldfilename'];
			if($renamefilename!=''){
				$msgFlag=1;
				$msg="File Name updated..";
				rename($actionpath.$oldfilename,$actionpath.$renamefilename);
			}else{
				$msgFlag=1;
				$msg="File Name is empty..";
			}
		}
	?>
	<div class="actionPopup" id="renamefilepop">
		<i class="far fa-window-close" onclick="closepop();"></i>
		<form action="" method="post">
			<input type="text" name="renamefilename" value=""  id="renamefilename">
			<input type="hidden" name="oldfilename" value=""  id="oldfilename">
			<input type="hidden" name="actionpath" value="" id="renamefilepath">
			<input type="hidden" name="FileManager">			
			<input type="submit" value="Rename File" name="renamefilesubmit">
		</form>
	</div>
	<!-- popup for delete file -->
	<?php  
		if(isset($_POST['deletefilesubmit'])){
			$actionpath = $_POST['actionpath'];
			$deletefilename = $_POST['deletefilename'];

				$msgFlag=1;
				$msg="File deleted..";
				unlink($actionpath.$deletefilename);
		}
	?>
	<div class="actionPopup" id="deletefilepop">
		<i class="far fa-window-close" onclick="closepop();"></i>
		<h5>Are you sure you want to delete..</h5>
		<form action="" method="post">
			<input type="hidden" name="deletefilename" value=""  id="deletefilename">
			<input type="hidden" name="actionpath" value="" id="deletefilepath">
			<input type="hidden" name="FileManager">			
			<input type="submit" value="Delete File" name="deletefilesubmit">
		</form>
	</div>
	<!-- popup for rename folder -->
	<?php  
		if(isset($_POST['renamefoldersubmit'])){
			$actionpath = $_POST['actionpath'];
			$renamefoldername = $_POST['renamefoldername'];
			$oldfoldername = $_POST['oldfoldername'];
			if($renamefoldername!=''){
				$msgFlag=1;
				$msg="Folder Name updated..";
				rename($actionpath.$oldfoldername,$actionpath.$renamefoldername);
			}else{
				$msgFlag=1;
				$msg="Folder Name is empty..";
			}
		}
	?>
	<div class="actionPopup" id="renamefolderpop">
		<i class="far fa-window-close" onclick="closepop();"></i>
		<form action="" method="post">
			<input type="text" name="renamefoldername" value=""  id="renamefoldername">
			<input type="hidden" name="oldfoldername" value=""  id="oldfoldername">
			<input type="hidden" name="actionpath" value="" id="renamefolderpath">	
			<input type="hidden" name="FileManager">		
			<input type="submit" value="Rename Folder" name="renamefoldersubmit">
		</form>
	</div>
	<!-- popup for delete folder -->
	<?php  
		if(isset($_POST['deletefoldersubmit'])){
			$actionpath = $_POST['actionpath'];
			$deletefoldername = $_POST['deletefoldername'];
				$msgFlag=1;
				
				if(rmdir($actionpath.$deletefoldername)){
					$msg="Folder deleted..";
				}else{
					$msg="To keep it safe folder with files or subfolders will not be deleted..";
				}
		}
	?>
	<div class="actionPopup" id="deletefolderpop">
		<i class="far fa-window-close" onclick="closepop();"></i>
		<h5>Are you sure you want to delete..</h5>
		<form action="" method="post">
			<input type="hidden" name="deletefoldername" value=""  id="deletefoldername">
			<input type="hidden" name="actionpath" value="" id="deletefolderpath">
			<input type="hidden" name="FileManager">			
			<input type="submit" value="Delete Folder" name="deletefoldersubmit">
		</form>
	</div>
	<?php  
		//add file code
		if(isset($_POST['addfilesubmit'])){
			$dirpath = $_POST['actionpath'];
			$name = $_POST['newfile'];
			$cfile = $dirpath.DIRECTORY_SEPARATOR.$name;
			if (file_exists($cfile)) {
				$msgFlag=1;
				$msg="File already exist...";
			}else{
				$myfile = fopen($cfile, "w") or die("Unable to open file!");
				fclose($myfile);
				$msgFlag=1;
				$msg="File Added...";
			}
			
		}
	?>
	<!-- popup for create file -->
	<div class="actionPopup" id="addfilepop">
		<i class="far fa-window-close" onclick="closepop();"></i>
		<form action="" method="post">
			<input type="text" name="newfile">
			<input type="hidden" name="actionpath" value="<?php echo $baseFolder; ?>" id="addfileactionpath">
			<input type="hidden" name="FileManager">
			<input type="submit" value="Add File" name="addfilesubmit">
		</form>
	</div>
	<?php  
		//add folder code
		if(isset($_POST['addfoldersubmit'])){
			$dirpath = $_POST['actionpath'];
			$name = $_POST['newfolder'];
			$cfolder = $dirpath.DIRECTORY_SEPARATOR.$name;
			if (file_exists($cfolder)) {
				$msgFlag=1;
				$msg="Folder already exist...";
			}else{
				mkdir($cfolder, 0777, true);
				$msgFlag=1;
				$msg="Folder Added...";
			}
			
		}
	?>
	<!-- popup for create folder -->
	<div class="actionPopup" id="addfolderpop">
		<i class="far fa-window-close" onclick="closepop();"></i>
		<form action="" method="post">
			<input type="text" name="newfolder">
			<input type="hidden" name="actionpath" value="<?php echo $baseFolder; ?>" id="addfolderactionpath">
			<input type="hidden" name="FileManager">
			<input type="submit" value="Add Folder" name="addfoldersubmit">
		</form>
	</div>
	<?php  
		//upload file code
		if(isset($_POST['uploadfilesubmit'])){
			$dirpath = $_POST['actionpath'];
			$file_tmp =$_FILES['file']['tmp_name'];
			$file_name = $_FILES['file']['name'];
			if($file_name!=''){
				move_uploaded_file($file_tmp, $file_name);
				rename(__DIR__.DIRECTORY_SEPARATOR.$file_name,$dirpath.DIRECTORY_SEPARATOR.$file_name);
				$msgFlag=1;
				$msg="File Uploaded...";
			}else{
				$msgFlag=1;
				$msg="No File Selected...";
			}
		}
	?>
	<!-- popup for upload file -->
	<div class="actionPopup"  id="uploadfilepop">
		<i class="far fa-window-close" onclick="closepop();"></i>
		<form action="" method="post" enctype="multipart/form-data">
			<input type="file" name="file">
			<input type="hidden" name="actionpath" value="<?php echo $baseFolder; ?>" id="uploadactionpath">
			<input type="hidden" name="FileManager">
			<input type="submit" value="Upload" name="uploadfilesubmit">
		</form>
	</div>
	<script>
		//close popup code
		function closepop(){
			var popelement = document.getElementsByClassName("actionPopup");
			for (var i = 0; i < popelement.length; i++) {
		 		//adding click event lintner to all li	
		  		popelement[i].style.display = 'none';
		    }
		}
		//delete folder
		function deletefolderfuc(e){
			var path = e.dataset.path;
			var name = e.dataset.name;
			document.getElementById('deletefolderpop').style.display = 'block';
			document.getElementById('deletefolderpath').value = path;
			document.getElementById('deletefoldername').value = name;
		}
		
		//delete file
		function deletefilefuc(e){
			var path = e.dataset.path;
			var name = e.dataset.name;
			document.getElementById('deletefilepop').style.display = 'block';
			document.getElementById('deletefilepath').value = path;
			document.getElementById('deletefilename').value = name;
		}
		
		//rename folder
		function renamefolderfuc(e){
			var path = e.dataset.path;
			var name = e.dataset.name;
			document.getElementById('renamefolderpop').style.display = 'block';
			document.getElementById('renamefolderpath').value = path;
			document.getElementById('renamefoldername').value = name;
			document.getElementById('oldfoldername').value = name;
		}
		
		//rename file
		function renamefilefuc(e){
			var path = e.dataset.path;
			var name = e.dataset.name;
			document.getElementById('renamefilepop').style.display = 'block';
			document.getElementById('renamefilepath').value = path;
			document.getElementById('renamefilename').value = name;
			document.getElementById('oldfilename').value = name;
		}
		

	</script>
	<div id="select_base_body">
		<div><h2>File Manager</h2></div>
		<?php if($msgFlag==1){
			echo "<span id='msgspan'>$msg</span>";
		} ?>
		<div id="optionDiv">
			<a href="javascript:void(0);" onclick="closepop();document.getElementById('addfolderpop').style.display = 'block';"><i class="fas fa-folder"></i> New Folder</a>
			<a href="javascript:void(0);" onclick="closepop();document.getElementById('addfilepop').style.display = 'block';"><i class="far fa-file"></i> New File</a>
			<a href="javascript:void(0);" onclick="closepop();document.getElementById('uploadfilepop').style.display = 'block';"><i class="fas fa-upload"></i> Upload File</i></a>
		</div>
		<div id="select_base_wrapper">
			<ul id="select_base_list">

				<?php 

					displayFileManager($baseFolder); ?>
			</ul>
			<form action="" method="post">
				
			</form>
		</div>

		<div id="base_form_wrapper">
			<form action="" onsubmit="return false;">
				<input type="hidden" value="">
				<input type="submit" value="<<" id="back_button" data-path="<?php echo backurl($baseFolder, $seprator); ?>">
			</form>

			<form action="" method="post">
				<input type="submit" value="Editor" id="password_button" name="goToEditor">
			</form>
			
		</div>
	</div>
	<script>
		// get seperator in js
		var seprator = "\<?php echo DIRECTORY_SEPARATOR; ?>";

		//creating back url
		function backurl(url, seprator){
			var n = url.lastIndexOf(seprator);
			return url.substr(0,n);
		}

		//function to load dir
		function loadDir(path){

			//data to send
			var data = "ajaxFileManager=true&path=" + path;

			// create ajax request
			if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		        xmlhttp=new XMLHttpRequest();
		    } else {// code for IE6, IE5
		        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		    }

		    //check for sucessfull response
		    xmlhttp.onreadystatechange=function() {
		        if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
		        	// change the list
		            document.getElementById("select_base_list").innerHTML=xmlhttp.responseText;
		            //add event listner to updated list
					addEventListrnerToLi();

					var backUrl = backurl(path, seprator);
					//update back btn data value
					document.getElementById('back_button').dataset.path = backUrl;
					//update new path to form 
					//document.getElementById('baseVal').value = path;

					//update action path of upload forms
					document.getElementById('uploadactionpath').value = path;
					//update action path of add folder forms
					document.getElementById('addfolderactionpath').value = path;
					//update action path of add file forms
					document.getElementById('addfileactionpath').value = path;
		        }
		    }


		    xmlhttp.open("POST", "v-editor.php", true); 
			xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");                  
			xmlhttp.send(data);

		}

		// function to add event listener to dir li
		function addEventListrnerToLi(){
			//get all li with folder name
			var getInDir = document.getElementsByClassName("inDir");

			//loop throught all the li
			for (var i = 0; i < getInDir.length; i++) {
		 		//adding click event lintner to all li	
		  		getInDir[i].addEventListener("click", function() {

		    		var dirUrl = this.getAttribute('data-path');
		    		loadDir(dirUrl);
		    	});
		    }
		}

		// calling event lister for first time
		addEventListrnerToLi();

	    // add event listner to back btn
	    var backBtn = document.getElementById('back_button');
	    backBtn.addEventListener("click", function() {

    		var dirUrl = this.getAttribute('data-path');
    		loadDir(dirUrl);
    	});

	</script>
</body>
</html>
		<?php

	}else{
		// this part will contain all the code of editor (login and base is set)

		?>
		<!-- UI of Editor -->
<!DOCTYPE html>
<html lang="en">
<head>
<title>ACE in Action</title>
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
<style type="text/css" media="screen">
	body{
		margin: 0px;
		padding: 0px;
		font-family: 'Source Sans Pro', sans-serif;
		background-color: #272822;
	}
	#editorTopSection{
		height: 30px;
		position: absolute;
        top: 0;
        left: 0;
        z-index: 998;
        width: 100%;
    	background: #292929;
	}
	#editorLeftSection{
		width: 250px;
		position: absolute;
        top: 0;
        left: 0;
        z-index: 999;
        height: 100vh;
        background-color: #3e4036;
        color: #d2d2d2;
    	font-size: 12px;
    	overflow: auto;
	}
	#editorLeftSection::-webkit-scrollbar-track, #editor::-webkit-scrollbar-track{
			-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
			background-color: #4f5052;
			border-radius: 10px;
		}

		#editorLeftSection::-webkit-scrollbar, #editor::-webkit-scrollbar-track{
			width: 10px;
			background-color: #4f5052;
		}
		#editorLeftSection::-webkit-scrollbar-corner, #editor::-webkit-scrollbar-track{
			width: 10px;
			background-color: #4f5052;
		}

		#editorLeftSection::-webkit-scrollbar-thumb, #editor::-webkit-scrollbar-track{
			border-radius: 10px;
			background-image: -webkit-gradient(linear,
											   left bottom,
											   left top,
											   color-stop(0.44, #727272),
											   color-stop(0.72, #626262),
											   color-stop(0.86, #626262));
		}
    #editor { 
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
    }
    #editorTopSectionInner{
    	margin-left: 270px;
    }
    #editorTopSectionInner span{
    	float: left;
    	color: #f79731;
    	padding-top: 5px;
    	font-size: 12px;
    }
    #editorTopSectionInner form{
    	float: right;
    	margin-top: 5px;
    	margin-right: 5px;
    	font-size: 12px;
    }
    #editorTopSectionInner #SaveBtn{
    	background-color: #47b747;
    	border: 0px;
    	color: white;
    	cursor: pointer;
    }
    #editorTopSectionInner #changeBaseBtn{
    	background-color: #f79731;
    	border: 0px;
    	color: white;
    	cursor: pointer;

    }
    #editorTopSectionInner #logoutBtn{
    	background-color: #f14848;
    	color: white;
    	border: 0px;
    	cursor: pointer;

    }
    #editorTopSectionInner #FileManagerBtn{
    	background-color: #675ebb;
    	color: white;
    	border: 0px;
    	cursor: pointer;

    }
    #editorLeftSectionInner{
    	text-align: center;

    }
	#editorLeftSectionInner .fas{
		padding: 9px 5px;
		color: #dad9d9;
		
	}
	#editorLeftSectionInner a{
		color: white;
		text-decoration: none;
		margin-right: 5px;
	}
	#editorLeftSectionInner div{
		background-color: #525252
	}
	#sidebarUL {
	    list-style-type: none;
	    padding-left: 10px;
	    font-size: 13px;
	    color: #bdbdbd;
	}
	#sidebarUL li{
		padding-top: 3px;
		padding-bottom: 3px;
		cursor: default;
	}
	#sidebarUL li a{
		text-decoration: none;
		color: #bdbdbd;
		cursor: default;
	}
	#sidebarUL li a:hover{
		background-color: #4c4c4c;
	}
	#sidebarUL li .fas, #sidebarUL li .far{
		margin-right: 2px;
		margin-left: 10px;
	}
	#sidebarUL .inDir .fas{
		color: #d83e34;
	}
	#sidebarUL .inFile .far{
		color: #229bf1;
	}
	#selectFont{
		height: 17px;
	    border: 0px;
	    background: #808080;
	    color: white;
	}
	#selectTheme{
		height: 17px;
	    border: 0px;
		background: #5c7784;
	    color: white;
	}
	#selectDoc{
		height: 17px;
	    border: 0px;
		background: #5e5173;
	    color: white;
	}
</style>
</head>
<body style="margin: 0; padding: 0;">
<div>
    <div id="editorTopSection">
    	<div id="editorTopSectionInner">
    		<!-- current file name -->
    		<span id="filenameSpan"></span>
    		<!-- logout button -->
    		<form action="" method="post">
    			<input type="submit" value="Logout" id="logoutBtn" name="logoutBtn">
    		</form>
    		<!-- change base button -->
    		<form action="" method="post">
    			<input type="submit" value="Change Base" id="changeBaseBtn" name="changeBaseBtn">
    		</form>
    		<!-- file manager button -->
    		<form action="" method="post">
    			<input type="submit" value="File Manager" id="FileManagerBtn" name="FileManager">
    		</form>
    		<!-- save button -->
    		<form action="" method="post" onsubmit="return saveFile(this);">
    			<input type="hidden" name="savepath" value="" id="savepath">
    			<textarea name="savecontent" id="savecontent" style="display: none;"></textarea>
    			<input type="submit" value="Save" id="SaveBtn" name="SaveBtn">
    		</form>
    		<!-- font resizer -->
    		<form action="" method="post">
	    		<select id="selectFont" onchange="changeFont(this);">
			        <option value="10" id="10px" style="font-size: 10px;">10</option>
			        <option value="11" id="11px" style="font-size: 11px;">11</option>
			        <option value="12" id="12px" style="font-size: 12px;">12</option>
			        <option value="13" id="13px" style="font-size: 13px;">13</option>
			        <option value="14" id="14px" style="font-size: 14px;">14</option>
			        <option value="15" id="15px" style="font-size: 15px;">15</option>
			        <option value="16" id="16px" style="font-size: 16px;">16</option>
			        <option value="17" id="17px" style="font-size: 17px;">17</option>
			        <option value="18" id="18px" style="font-size: 18px;">18</option>
			        <option value="19" id="19px" style="font-size: 19px;">19</option>
			        <option value="20" id="20px" style="font-size: 20px;">20</option>
			        <option value="21" id="21px" style="font-size: 21px;">21</option>
			        <option value="22" id="22px" style="font-size: 22px;">22</option>
			        <option value="23" id="23px" style="font-size: 23px;">23</option>
			        <option value="24" id="24px" style="font-size: 24px;">24</option>
			        <option value="25" id="25px" style="font-size: 25px;">25</option>
			    </select>
		    </form>
		    <!-- Editor Theme -->
    		<form action="" method="post">
			    <select  id="selectTheme" onchange="changeTheme(this);">
			    	<optgroup label="Bright">
			    		<option id="ace/theme/chrome" value="ace/theme/chrome">Chrome</option>
			    		<option id="ace/theme/clouds" value="ace/theme/clouds">Clouds</option>
			    		<option id="ace/theme/crimson_editor" value="ace/theme/crimson_editor">Crimson Editor</option>
			    		<option id="ace/theme/dawn" value="ace/theme/dawn">Dawn</option>
			    		<option id="ace/theme/dreamweaver" value="ace/theme/dreamweaver">Dreamweaver</option>
			    		<option id="ace/theme/eclipse" value="ace/theme/eclipse">Eclipse</option>
			    		<option id="ace/theme/github" value="ace/theme/github">GitHub</option>
			    		<option id="ace/theme/iplastic" value="ace/theme/iplastic">IPlastic</option>
			    		<option id="ace/theme/solarized_light" value="ace/theme/solarized_light">Solarized Light</option>
			    		<option id="ace/theme/textmate" value="ace/theme/textmate">TextMate</option>
			    		<option id="ace/theme/tomorrow" value="ace/theme/tomorrow">Tomorrow</option>
			    		<option id="ace/theme/xcode" value="ace/theme/xcode">XCode</option>
			    		<option id="ace/theme/kuroir" value="ace/theme/kuroir">Kuroir</option>
			    		<option id="ace/theme/katzenmilch" value="ace/theme/katzenmilch">KatzenMilch</option>
			    		<option id="ace/theme/sqlserver" value="ace/theme/sqlserver">SQL Server</option>
			    	</optgroup>
			    	<optgroup label="Dark">
			    		<option id="ace/theme/ambiance" value="ace/theme/ambiance">Ambiance</option>
			    		<option id="ace/theme/chaos" value="ace/theme/chaos">Chaos</option>
			    		<option id="ace/theme/clouds_midnight" value="ace/theme/clouds_midnight">Clouds Midnight</option>
			    		<option id="ace/theme/dracula" value="ace/theme/dracula">Dracula</option>
			    		<option id="ace/theme/cobalt" value="ace/theme/cobalt">Cobalt</option>
			    		<option id="ace/theme/gruvbox" value="ace/theme/gruvbox">Gruvbox</option>
			    		<option id="ace/theme/gob" value="ace/theme/gob">Green on Black</option>
			    		<option id="ace/theme/idle_fingers" value="ace/theme/idle_fingers">idle Fingers</option>
			    		<option id="ace/theme/kr_theme" value="ace/theme/kr_theme">krTheme</option>
			    		<option id="ace/theme/merbivore" value="ace/theme/merbivore">Merbivore</option>
			    		<option id="ace/theme/merbivore_soft" value="ace/theme/merbivore_soft">Merbivore Soft</option>
			    		<option id="ace/theme/mono_industrial" value="ace/theme/mono_industrial">Mono Industrial</option>
			    		<option id="ace/theme/monokai" value="ace/theme/monokai">Monokai</option>
			    		<option id="ace/theme/pastel_on_dark" value="ace/theme/pastel_on_dark">Pastel on dark</option>
			    		<option id="ace/theme/solarized_dark" value="ace/theme/solarized_dark">Solarized Dark</option>
			    		<option id="ace/theme/terminal" value="ace/theme/terminal">Terminal</option>
			    		<option id="ace/theme/tomorrow_night" value="ace/theme/tomorrow_night">Tomorrow Night</option>
			    		<option id="ace/theme/tomorrow_night_blue" value="ace/theme/tomorrow_night_blue">Tomorrow Night Blue</option>
			    		<option id="ace/theme/tomorrow_night_bright" value="ace/theme/tomorrow_night_bright">Tomorrow Night Bright</option>
			    		<option id="ace/theme/tomorrow_night_eighties" value="ace/theme/tomorrow_night_eighties">Tomorrow Night 80s</option>
			    		<option id="ace/theme/twilight" value="ace/theme/twilight">Twilight</option>
			    		<option id="ace/theme/vibrant_ink" value="ace/theme/vibrant_ink">Vibrant Ink</option>
			    	</optgroup>
			    </select>
		    </form>
		    <form action="">
		    	<select  id="selectDoc" onchange="changeDoc(this);">	
		    		<option value="text" id="langtext">Text</option>	    		
		    		<option value="abap">ABAP</option>
		    		<option value="abc">ABC</option>
		    		<option value="actionscript">ActionScript</option>
		    		<option value="ada">ADA</option>
		    		<option value="apache_conf">Apache Conf</option>
		    		<option value="apex">Apex</option>
		    		<option value="asciidoc">AsciiDoc</option>
		    		<option value="asl">ASL</option>
		    		<option value="assembly_x86">Assembly x86</option>
		    		<option value="autohotkey">AutoHotKey</option>
		    		<option value="batchfile">BatchFile</option>
		    		<option value="bro">Bro</option>
		    		<option value="c_cpp">C and C++</option>
		    		<option value="csharp">C#</option>
		    		<option value="c9search">C9 Search Results</option>
		    		<option value="cirru">Cirru</option>
		    		<option value="clojure">Clojure</option>
		    		<option value="cobol">Cobol</option>
		    		<option value="coffee">CoffeeScript</option>
		    		<option value="coldfusion">ColdFusion</option>
		    		<option value="csound">Csound</option>
		    		<option value="csound_document">Csound Document</option>
		    		<option value="csound_score">Csound Score</option>
		    		<option value="css" id="langcss">CSS</option>
		    		<option value="curly">Curly</option>
		    		<option value="d">D</option>
		    		<option value="dart">Dart</option>
		    		<option value="diff">Diff</option>
		    		<option value="django">Django</option>
		    		<option value="dockerfile">Dockerfile</option>
		    		<option value="dot">Dot</option>
		    		<option value="drools">Drools</option>
		    		<option value="edifact">Edifact</option>
		    		<option value="eiffel">Eiffel</option>
		    		<option value="eJS">EJS</option>
		    		<option value="elixir">Elixir</option>
		    		<option value="elm">Elm</option>
		    		<option value="erlang">Erlang</option>
		    		<option value="forth">Forth</option>
		    		<option value="fortran">Fortran</option>
		    		<option value="freemarker">FreeMarker</option>
		    		<option value="fsharp">FSharp</option>
		    		<option value="fsl">FSL</option>
		    		<option value="gcode">Gcode</option>
		    		<option value="gherkin">Gherkin</option>
		    		<option value="gitignore">Gitignore</option>
		    		<option value="glsl">Glsl</option>
		    		<option value="go">Go</option>
		    		<option value="gobstones">Gobstones</option>
		    		<option value="graphqlschema">GraphQLSchema</option>
		    		<option value="groovy">Groovy</option>
		    		<option value="haml">HAML</option>
		    		<option value="handlebars">Handlebars</option>
		    		<option value="haskell">Haskell</option>
		    		<option value="haskell_cabal">Haskell Cabal</option>
		    		<option value="haxe">haXe</option>
		    		<option value="hjson">Hjson</option>
		    		<option value="html" id="langhtml">HTML</option>
		    		<option value="html_elixir">HTML (Elixir)</option>
		    		<option value="html_ruby">HTML (Ruby)</option>
		    		<option value="ini">INI</option>
		    		<option value="io">Io</option>
		    		<option value="jack">Jack</option>
		    		<option value="jade">Jade</option>
		    		<option value="java">Java</option>
		    		<option value="javascript" id="langjavascript">JavaScript</option>
		    		<option value="json">JSON</option>
		    		<option value="jsoniq">JSONiq</option>
		    		<option value="jsp">JSP</option>
		    		<option value="jssm">JSSM</option>
		    		<option value="jsx">JSX</option>
		    		<option value="julia">Julia</option>
		    		<option value="kotlin">Kotlin</option>
		    		<option value="latex">LaTeX</option>
		    		<option value="less" id="langless">LESS</option>
		    		<option value="liquid">Liquid</option>
		    		<option value="lisp">Lisp</option>
		    		<option value="livescript">LiveScript</option>
		    		<option value="logiql">LogiQL</option>
		    		<option value="lsl">LSL</option>
		    		<option value="lua">Lua</option>
		    		<option value="luapage">LuaPage</option>
		    		<option value="lucene">Lucene</option>
		    		<option value="makefile">Makefile</option>
		    		<option value="markdown">Markdown</option>
		    		<option value="mask">Mask</option>
		    		<option value="matlab">MATLAB</option>
		    		<option value="maze">Maze</option>
		    		<option value="mel">MEL</option>
		    		<option value="mixal">MIXAL</option>
		    		<option value="mushcode">MUSHCode</option>
		    		<option value="mysql">MySQL</option>
		    		<option value="nix">Nix</option>
		    		<option value="nsis">NSIS</option>
		    		<option value="objectivec">Objective-C</option>
		    		<option value="ocaml">OCaml</option>
		    		<option value="pascal">Pascal</option>
		    		<option value="perl">Perl</option>
		    		<option value="perl6">Perl 6</option>
		    		<option value="pgsql">pgSQL</option>
		    		<option value="php" id="langphp">PHP</option>
		    		<option value="php_laravel_blade">PHP (Blade Template)</option>
		    		<option value="pig">Pig</option>
		    		<option value="plain_text">Plain Text</option>
		    		<option value="powershell">Powershell</option>
		    		<option value="praat">Praat</option>
		    		<option value="prolog">Prolog</option>
		    		<option value="properties">Properties</option>
		    		<option value="protobuf">Protobuf</option>
		    		<option value="puppet">Puppet</option>
		    		<option value="python">Python</option>
		    		<option value="r">R</option>
		    		<option value="razor">Razor</option>
		    		<option value="rdoc">RDoc</option>
		    		<option value="red">Red</option>
		    		<option value="rhtml">RHTML</option>
		    		<option value="rst">RST</option>
		    		<option value="ruby">Ruby</option>
		    		<option value="rust">Rust</option>
		    		<option value="sass" id="langsass">SASS</option>
		    		<option value="scad">SCAD</option>
		    		<option value="scala">Scala</option>
		    		<option value="scheme">Scheme</option>
		    		<option value="scss" id="langscss">SCSS</option>
		    		<option value="sh">SH</option>
		    		<option value="sjs">SJS</option>
		    		<option value="slim">Slim</option>
		    		<option value="smarty">Smarty</option>
		    		<option value="snippets">snippets</option>
		    		<option value="soy_template">Soy Template</option>
		    		<option value="space">Space</option>
		    		<option value="sql">SQL</option>
		    		<option value="sqlserver">SQLServer</option>
		    		<option value="stylus">Stylus</option>
		    		<option value="svg">SVG</option>
		    		<option value="swift">Swift</option>
		    		<option value="tcl">Tcl</option>
		    		<option value="terraform">Terraform</option>
		    		<option value="tex">Tex</option>
		    		<option value="text">Text</option>
		    		<option value="textile">Textile</option>
		    		<option value="toml">Toml</option>
		    		<option value="tsx">TSX</option>
		    		<option value="twig">Twig</option>
		    		<option value="typescript">Typescript</option>
		    		<option value="vala">Vala</option>
		    		<option value="vbscript">VBScript</option>
		    		<option value="velocity">Velocity</option>
		    		<option value="verilog">Verilog</option>
		    		<option value="vhdl">VHDL</option>
		    		<option value="visualforce">Visualforce</option>
		    		<option value="wollok">Wollok</option>
		    		<option value="xml">XML</option>
		    		<option value="xquery">XQuery</option>
		    		<option value="yaml">YAML</option>
		    	</select>
		    </form>
    	</div>
    </div>
    <div id="editorLeftSection">
    	
    	<div id="DirListSectionInner">
    		<?php 
    			$dirName = $_SESSION["baseDir"];
    			dirListSidebar($dirName); 
    		?>
	    </div>
    </div>
    <div id="editor" style="float: left; margin-top: 30px; margin-left: 250px;"></div>
</div>
<!-- load emmet code and snippets compiled for browser -->
<script src="https://cloud9ide.github.io/emmet-core/emmet.js"></script>
<!-- loading ace editor      -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.1/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.2/ext-emmet.js"></script>
<script>
	// cookie function
	function setCookie(cname, cvalue, exdays) {
	    var d = new Date();
	    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
	    var expires = "expires="+d.toUTCString();
	    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
	}

	function getCookie(cname) {
	    var name = cname + "=";
	    var ca = document.cookie.split(';');
	    for(var i = 0; i < ca.length; i++) {
	        var c = ca[i];
	        while (c.charAt(0) == ' ') {
	            c = c.substring(1);
	        }
	        if (c.indexOf(name) == 0) {
	            return c.substring(name.length, c.length);
	        }
	    }
	    return "";
	}
</script>
<script>
	// configuring ace editor
    var editor = ace.edit("editor");
    
    // enable emmet on the current editor
    editor.setOption("enableEmmet", true);

    //check cookie for theme and set
    var cookieTheme = getCookie('theme')
    if(cookieTheme!=''){
    	editor.setTheme(cookieTheme);
    	document.getElementById(cookieTheme).selected = "true";
    }else{
    	editor.setTheme("ace/theme/monokai");
    	document.getElementById("ace/theme/monokai").selected = "true";
    }

    //check cookie for font and set
    var cookieFont = getCookie('fontsize')
    if(cookieFont!=''){
    	document.getElementById('editor').style.fontSize = cookieFont;
    	document.getElementById(cookieFont).selected = "true";
    }else{
    	document.getElementById('editor').style.fontSize = '12px';
    	document.getElementById('12px').selected = "true";
    }


    editor.session.setMode("ace/mode/php");
    // to set data in editor
    var data= "";
    editor.setValue(data);
    //word wrap ON
    editor.session.setUseWrapMode(true);
    //on change event for checking the content and activating save btn
    editor.session.on('change', function(delta) {
	    console.log('changed');
	});

    //console.log(editor.getValue()); // or session.getValue
</script>

<script>
	// get seperator in js
		var seprator = "\<?php echo DIRECTORY_SEPARATOR; ?>";
	// load file content in editor on clicking on file name
	function loadFileAjax(e){
			//data to send
			var data = "loadfileajax=true&path=" + e.dataset.path;

			// create ajax request
			if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		        xmlhttp=new XMLHttpRequest();
		    } else {// code for IE6, IE5
		        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		    }

		    //check for sucessfull response
		    xmlhttp.onreadystatechange=function() {
		        if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
		        	// change the editor content
		            editor.setValue(xmlhttp.responseText.trim());
		            //to avoid selectiong all
		            editor.gotoLine(1);
		            //set file name
		            var lastSeprator = e.dataset.path.lastIndexOf(seprator);
		            lastSeprator++;
		            document.getElementById('filenameSpan').innerHTML = "<i class='far fa-file-alt'></i> "+e.dataset.path.substr(lastSeprator);
		            //set file path to save btn
		            document.getElementById('savepath').value = e.dataset.path;

		            //set language mode
		            var fileNm = e.dataset.path.substr(lastSeprator);
		            setLanfuageModeAce(fileNm);
		        }
		    }


		    xmlhttp.open("POST", "v-editor.php", true); 
			xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");                  
			xmlhttp.send(data);
	}

	//load folder conent in sidebar on clicking on folder name
	function loadDirAjax(e){
		//check if dir is alreay loaded and if loaded close it else load dir
		var opendiv = e.parentNode.getElementsByTagName("div");
		var opened = opendiv.length;

		if(opened!=0){
			e.parentNode.removeChild(opendiv[0]);
			e.getElementsByTagName('i')[0].className = "fas fa-folder";
		}else{
			//data to send
			var data = "loaddirajax=true&path=" + e.dataset.path;

			// create ajax request
			if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		        xmlhttp=new XMLHttpRequest();
		    } else {// code for IE6, IE5
		        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		    }

		    //check for sucessfull response
		    xmlhttp.onreadystatechange=function() {
		        if (xmlhttp.readyState == 4 && xmlhttp.status == 200){

		        	// change the list
		           var newEl = document.createElement('div');
				   newEl.innerHTML = xmlhttp.responseText;
		           e.parentNode.insertBefore(newEl, e.nextSibling);
		           e.getElementsByTagName('i')[0].className = "fas fa-folder-open";
		        }
		    }


		    xmlhttp.open("POST", "v-editor.php", true); 
			xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");                  
			xmlhttp.send(data);
		}	
	}

	//to set lawnguage mode dynamically
	function setLanfuageModeAce(fileNm){
		var lastDot = fileNm.lastIndexOf('.');
		lastDot++;
		var ext = fileNm.substr(lastDot);
		var lang = "ace/mode/";
		switch (ext) {
	        case "js":
	        	lang = lang+"javascript";
	        	document.getElementById('langjavascript').selected = "true";
	        	break;
	        case "html":
	        	lang = lang+"html";
	        	document.getElementById('langhtml').selected = "true";
	        	break;
	        case "phtml":
	        	lang = lang+"html";
	        	document.getElementById('langhtml').selected = "true";
	        	break;
	       	case "htm":
	        	lang = lang+"html";
	        	document.getElementById('langhtml').selected = "true";
	        	break;
	        case "php":
	        	lang = lang+"php";
	        	document.getElementById('langphp').selected = "true";
	        	break;
	        case "css":
	        	lang = lang+"css";
	        	document.getElementById('langcss').selected = "true";
	        	break;
	        case "sass":
	        	lang = lang+"sass";
	        	document.getElementById('langsass').selected = "true";
	        	break;
	        case "scss":
	        	lang = lang+"scss";
	        	document.getElementById('langscss').selected = "true";
	        	break;
	        case "less":
	        	lang = lang+"less";
	        	document.getElementById('langless').selected = "true";	        	
	        	break;
	        default:
	        	lang = lang+"text";
	        	document.getElementById('langtext').selected = "true";
	    }
		editor.session.setMode(lang);
	}
</script>
<script>
	// change font function
	function changeFont(e){
		var fontsize = e.options[e.selectedIndex].value+'px';
		document.getElementById('editor').style.fontSize = fontsize;
		setCookie('fontsize', fontsize, 30);
	}

	//change Editor theme
	function changeTheme(e){
		var theme = e.options[e.selectedIndex].value;
		editor.setTheme(theme);
		setCookie('theme', theme, 30);
	}

	//change editor document type
	function changeDoc(e){
		var docType = 'ace/mode/'+e.options[e.selectedIndex].value;
		editor.session.setMode(docType);
	}

	//save file with ajax
	function saveFile(e){
		var filepath = document.getElementById('savepath').value;
		if(filepath!=''){
			var text = editor.getValue();
			document.getElementById('savecontent').value = text;
			return true;
		}else{
			return false;
		}
	}
</script>

	//to open file after saving it
	<?php
		//request for save file
		if(isset($_POST['SaveBtn'])){
			$filePath = $_POST['savepath'];
			?>
			<a style="display: none;" href="javascript:void(0);" data-path="<?php echo $filePath; ?>" id="loadSavedFile" onclick="loadFileAjax(this)"><i class="far fa-file-code"></i> hack2.php</a>
			<script>
				document.getElementById("loadSavedFile").click();
			</script>
			<!-- The actual snackbar -->
			<div id="snackbar">File saved..</div>
			<style>
				/* The snackbar - position it at the bottom and in the middle of the screen */
				#snackbar {
				    visibility: hidden; /* Hidden by default. Visible on click */
				    min-width: 250px; /* Set a default minimum width */
				    margin-left: -125px; /* Divide value of min-width by 2 */
				    background-color: #009688!important; /* Black background color */
				    color: white; /* White text color */
				    text-align: center; /* Centered text */
				    border-radius: 2px; /* Rounded borders */
				    padding: 16px; /* Padding */
				    position: fixed; /* Sit on top of the screen */
				    z-index: 1; /* Add a z-index if needed */
				    left: 50%; /* Center the snackbar */
				    bottom: 30px; /* 30px from the bottom */
				}

				/* Show the snackbar when clicking on a button (class added with JavaScript) */
				#snackbar.show {
				    visibility: visible; /* Show the snackbar */
				    /* Add animation: Take 0.5 seconds to fade in and out the snackbar. 
				   However, delay the fade out process for 2.5 seconds */
				   -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
				   animation: fadein 0.5s, fadeout 0.5s 2.5s;
				}

				/* Animations to fade the snackbar in and out */
				@-webkit-keyframes fadein {
				    from {bottom: 0; opacity: 0;} 
				    to {bottom: 30px; opacity: 1;}
				}

				@keyframes fadein {
				    from {bottom: 0; opacity: 0;}
				    to {bottom: 30px; opacity: 1;}
				}

				@-webkit-keyframes fadeout {
				    from {bottom: 30px; opacity: 1;} 
				    to {bottom: 0; opacity: 0;}
				}

				@keyframes fadeout {
				    from {bottom: 30px; opacity: 1;}
				    to {bottom: 0; opacity: 0;}
				}
			</style>
			<script>
				function snackBar() {
				    // Get the snackbar DIV
				    var x = document.getElementById("snackbar");

				    // Add the "show" class to DIV
				    x.className = "show";

				    // After 3 seconds, remove the show class from DIV
				    setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
				}
				snackBar();
			</script>
			<?php
		}
	?>
<script>
	editor.commands.addCommand({
	    name: 'myCommand',
	    bindKey: {win: 'Ctrl-S',  mac: 'Command-S'},
	    exec: function(editor) {
	        document.getElementById('SaveBtn').click();
	    },
	    readOnly: true // false if this command should not apply in readOnly mode
	});
	// add command to lazy-load keybinding_menu extension
    editor.commands.addCommand({
        name: "showKeyboardShortcuts",
        bindKey: {win: "Ctrl-Alt-h", mac: "Command-Alt-h"},
        exec: function(editor) {
            ace.config.loadModule("ace/ext/keybinding_menu", function(module) {
                module.init(editor);
                editor.showKeyboardShortcuts()
            })
        }
    })
    //editor.execCommand("showKeyboardShortcuts")
</script>
</body>
</html>
		<?php
	} 
?>