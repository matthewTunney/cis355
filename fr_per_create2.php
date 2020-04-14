<?php 
/* ---------------------------------------------------------------------------
 * filename    : fr_per_create2.php
 * author      : George Corser, gcorser@gmail.com
 * description : This program adds/inserts a new volunteer (table: fr_persons)
 * ---------------------------------------------------------------------------
 */
session_start();
// session_start();
// if(!isset($_SESSION["fr_person_id"])){ // if "user" not set,
	// session_destroy();
	// header('Location: login.php');     // go to login page
	// exit;
// }
error_reporting(0);	
require '../database/database.php';

if ( !empty($_POST)) { // if not first time through

	// initialize user input validation variables
	$fnameError = null;
	$lnameError = null;
	$emailError = null;
	$mobileError = null;
	$passwordError = null;
	$titleError = null;
	$pictureError = null; // not used
	$addressError = null;
	$cityError = null;
	$stateError = null;
	$zipError = null;
	
	// initialize $_POST variables
	$fname =  htmlspecialchars($_POST['fname']);
	$lname = htmlspecialchars($_POST['lname']);
	$email = htmlspecialchars($_POST['email']);
	$mobile = htmlspecialchars($_POST['mobile']);
	$password = htmlspecialchars($_POST['password']);
	$passwordhash = MD5($password);
	$title = htmlspecialchars($_POST['title']);
//	$picture = $_POST['picture']; // not used
	
	// initialize $_FILES variables
	$fileName = $_FILES['userfile']['name'];
	$tmpName  = $_FILES['userfile']['tmp_name'];
	$fileSize = $_FILES['userfile']['size'];
	$fileType = $_FILES['userfile']['type'];
	$content = file_get_contents($tmpName);
	
$address =  htmlspecialchars($_POST['address']);
$city =  htmlspecialchars($_POST['city']);
$state =  htmlspecialchars($_POST['state']);
$zip =  htmlspecialchars($_POST['zip']);
	// validate user input
	$valid = true;
	if (empty($fname)) {
		$fnameError = 'Please enter First Name';
		$valid = false;
	}
	if (empty($lname)) {
		$lnameError = 'Please enter Last Name';
		$valid = false;
	}
	// do not allow 2 records with same email address!
	if (empty($email)) {
		$emailError = 'Please enter valid Email Address (REQUIRED)';
		$valid = false;
	} else if ( !filter_var($email,FILTER_VALIDATE_EMAIL) ) {
		$emailError = 'Please enter a valid Email Address';
		$valid = false;
	}

	$pdo = Database::connect();
	$sql = "SELECT * FROM fr_persons";
	foreach($pdo->query($sql) as $row) {

		if($email == $row['email']) {
			$emailError = 'Email has already been registered!';
			$valid = false;
		}
	}
	Database::disconnect();
	
	// email must contain only lower case letters
	if (strcmp(strtolower($email),$email)!=0) {
		$emailError = 'email address can contain only lower case letters';
		$valid = false;
	}
	
	if (empty($mobile)) {
		$mobileError = 'Please enter Mobile Number (or "none")';
		$valid = false;
	}
	if(!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $mobile)) {
		$mobileError = 'Please write Mobile Number in form 000-000-0000';
		$valid = false;
	}
	if (empty($password)) {
		$passwordError = 'Please enter valid Password';
		$valid = false;
	}
	if (empty($title)) {
		$titleError = 'Please enter valid Title';
		$valid = false;
	}
	// restrict file types for upload
	$types = array('image/jpeg','image/gif','image/png');

	if($filesize > 0) {
		if(in_array($_FILES['userfile']['type'], $types)) {
		}
		else {
			$filename = null;
			$filetype = null;
			$filesize = null;
			$filecontent = null;
			$pictureError = 'improper file type';
			$valid=false;
			
		}
	}
	// insert data
	if ($valid) 
	{
		$pdo = Database::connect();
		
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "INSERT INTO fr_persons (fname,lname,email,mobile,password,title,
		filename,filesize,filetype,filecontent, address, city, state, zip) values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,?,?)";
		$q = $pdo->prepare($sql);
		$q->execute(array($fname,$lname,$email,$mobile,$passwordhash,$title,
		$fileName,$fileSize,$fileType,$content,$address,$city,$state,$zip));
		
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT * FROM fr_persons WHERE email = ? AND password = ? LIMIT 1";
		$q = $pdo->prepare($sql);
		$q->execute(array($email,$passwordhash));
		$data = $q->fetch(PDO::FETCH_ASSOC);
		
		$_SESSION['fr_person_id'] = $data['id'];
		$_SESSION['fr_person_title'] = $data['title'];
		
		Database::disconnect();
		header("Location: fr_events.php");
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="js/bootstrap.min.js"></script>
	<link rel="icon" href="cardinal_logo.png" type="image/png" />
</head>

<body>
    <div class="container">

		<div class="span10 offset1">
			<?php
				require 'functions.php';
				Functions::logoDisplay2();
			?>
			<div class="row">
				<h3>Add New Volunteer</h3>
			</div>
	
			<form class="form-horizontal" action="fr_per_create2.php" method="post" enctype="multipart/form-data">

				<div class="control-group <?php echo !empty($fnameError)?'error':'';?>">
					<label class="control-label">First Name</label>
					<div class="controls">
						<input name="fname" type="text"  placeholder="First Name" value="<?php echo !empty($fname)?$fname:'';?>">
						<?php if (!empty($fnameError)): ?>
							<span class="help-inline"><?php echo $fnameError;?></span>
						<?php endif; ?>
					</div>
				</div>
				
				<div class="control-group <?php echo !empty($lnameError)?'error':'';?>">
					<label class="control-label">Last Name</label>
					<div class="controls">
						<input name="lname" type="text"  placeholder="Last Name" value="<?php echo !empty($lname)?$lname:'';?>">
						<?php if (!empty($lnameError)): ?>
							<span class="help-inline"><?php echo $lnameError;?></span>
						<?php endif; ?>
					</div>
				</div>
				
				<div class="control-group <?php echo !empty($emailError)?'error':'';?>">
					<label class="control-label">Email</label>
					<div class="controls">
						<input name="email" type="text" placeholder="Email Address" value="<?php echo !empty($email)?$email:'';?>">
						<?php if (!empty($emailError)): ?>
							<span class="help-inline"><?php echo $emailError;?></span>
						<?php endif;?>
					</div>
				</div>
				
				<div class="control-group <?php echo !empty($mobileError)?'error':'';?>">
					<label class="control-label">Mobile Number</label>
					<div class="controls">
						<input name="mobile" type="text"  placeholder="Mobile Phone Number" value="<?php echo !empty($mobile)?$mobile:'';?>">
						<?php if (!empty($mobileError)): ?>
							<span class="help-inline"><?php echo $mobileError;?></span>
						<?php endif;?>
					</div>
				</div>
				<div class="control-group <?php echo !empty($addressError)?'error':'';?>">
					<label class="control-label">Address</label>
					<div class="controls">
						<input name="address" type="text" placeholder="123 Maple St" value="<?php echo !empty($address)?$address:'';?>">
						<?php if (!empty($addressError)): ?>
							<span class="help-inline"><?php echo $addressError;?></span>
						<?php endif;?>
					</div>
				</div
				
				<div class="control-group <?php echo !empty($cityError)?'error':'';?>">
					<label class="control-label">City</label>
					<div class="controls">
						<input name="city" type="text" placeholder="Hill Valley" value="<?php echo !empty($city)?$city:'';?>">
						<?php if (!empty($cityError)): ?>
							<span class="help-inline"><?php echo $cityError;?></span>
						<?php endif;?>
					</div>
				</div>
				
				<div class="control-group <?php echo !empty($stateError)?'error':'';?>">
					<label class="control-label">State</label>
					<div class="controls">
						<input name="state" type="text" placeholder="CA" value="<?php echo !empty($state)?$state:'';?>">
						<?php if (!empty($stateError)): ?>
							<span class="help-inline"><?php echo $stateError;?></span>
						<?php endif;?>
					</div>
				</div>
				<div class="control-group <?php echo !empty($zipError)?'error':'';?>">
					<label class="control-label">Zip Code</label>
					<div class="controls">
						<input name="zip" type="text" placeholder="12345" value="<?php echo !empty($zip)?$zip:'';?>">
						<?php if (!empty($zipError)): ?>
							<span class="help-inline"><?php echo $zipError;?></span>
						<?php endif;?>
					</div>
				</div>
				
				
				
				
				
				
				<div class="control-group <?php echo !empty($passwordError)?'error':'';?>">
					<label class="control-label">Password</label>
					<div class="controls">
						<input id="password" name="password" type="password"  placeholder="password" value="<?php echo !empty($password)?$password:'';?>">
						<?php if (!empty($passwordError)): ?>
							<span class="help-inline"><?php echo $passwordError;?></span>
						<?php endif;?>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">Title</label>
					<div class="controls">
						<select class="form-control" name="title">
							<option value="Volunteer" selected>Volunteer</option>
							<!-- <option value="Administrator" >Administrator</option> -->
						</select>
					</div>
				</div>
			  
				<div class="control-group <?php echo !empty($pictureError)?'error':'';?>">
					<label class="control-label">Picture</label>
					<div class="controls">
						<input type="hidden" name="MAX_FILE_SIZE" value="16000000">
						<input name="userfile" type="file" id="userfile">
						
					</div>
				</div>
			  
				<div class="form-actions">
					<button type="submit" class="btn btn-success">Confirm</button>
					<a class="btn" href="fr_events.php">Back</a>
				</div>
				
			</form>
			
		</div> <!-- end div: class="span10 offset1" -->
				
    </div> <!-- end div: class="container" -->
  </body>
</html>