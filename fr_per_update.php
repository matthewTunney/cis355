<?php 
/* ---------------------------------------------------------------------------
 * filename    : fr_per_update.php
 * author      : George Corser, gcorser@gmail.com
 * description : This program updates one volunteer's details (table: fr_persons)
 * ---------------------------------------------------------------------------
 */
session_start();
if(!isset($_SESSION["fr_person_id"])){ // if "user" not set,
	session_destroy();
	header('Location: login.php');     // go to login page
	exit;
}
	error_reporting(0);	
require '../database/database.php';

$id = $_GET['id'];

if ( !empty($_POST)) { // if $_POST filled then process the form

	# initialize/validate (same as file: fr_per_create.php)

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
	$fname = htmlspecialchars($_POST['fname']);
	$lname = htmlspecialchars($_POST['lname']);
	$email = htmlspecialchars($_POST['email']);
	$mobile = htmlspecialchars($_POST['mobile']);
//	$password = $_POST['password'];
	$title =  htmlspecialchars($_POST['title']);
	$picture = $_POST['picture']; // not used
	$address =  htmlspecialchars($_POST['address']);
	$city =  htmlspecialchars($_POST['city']);
	$state =  htmlspecialchars($_POST['state']);
	$zip =  htmlspecialchars($_POST['zip']);
	
	// initialize $_FILES variables
	$fileName = $_FILES['userfile']['name'];
	$tmpName  = $_FILES['userfile']['tmp_name'];
	$fileSize = $_FILES['userfile']['size'];
	$fileType = $_FILES['userfile']['type'];
	$content = file_get_contents($tmpName);

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

	if (empty($email)) {
		$emailError = 'Please enter valid Email Address (REQUIRED)';
		$valid = false;
	} else if ( !filter_var($email,FILTER_VALIDATE_EMAIL) ) {
		$emailError = 'Please enter a valid Email Address';
		$valid = false;
	}

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
	
	if ($valid) { // if valid user input update the database
	
		if($fileSize > 0) { // if file was updated, update all fields
			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "UPDATE fr_persons  set fname = ?, lname = ?, email = ?, mobile = ?, password = ?, title = ?, filename = ?,filesize = ?,filetype = ?,filecontent = ? WHERE id = ?";
			$q = $pdo->prepare($sql);
			$q->execute(array($fname, $lname, $email, $mobile, $password, $title, $fileName,$fileSize,$fileType,$content, $id));
			Database::disconnect();
			header("Location: fr_persons.php");
		}
		else { // otherwise, update all fields EXCEPT file fields
			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "UPDATE fr_persons  set fname = ?, lname = ?, email = ?, mobile = ?, password = ?, title = ? WHERE id = ?";
			$q = $pdo->prepare($sql);
			$q->execute(array($fname, $lname, $email, $mobile, $password, $title,  $id));
			Database::disconnect();
			header("Location: fr_persons.php");
		}
	}
} else { // if $_POST NOT filled then pre-populate the form
	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT * FROM fr_persons where id = ?";
	$q = $pdo->prepare($sql);
	$q->execute(array($id));
	$data = $q->fetch(PDO::FETCH_ASSOC);
	$fname = $data['fname'];
	$lname = $data['lname'];
	$email = $data['email'];
	$mobile = $data['mobile'];
	$password = $data['password'];
	$address =  $data['address'];
	$city =  $data['city'];
	$state =  $data['state'];
	$zip =  $data['zip'];

	Database::disconnect();
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
				<h3>Update Volunteer Details</h3>
			</div>
	
			<form class="form-horizontal" action="fr_per_update.php?id=<?php echo $id?>" method="post" enctype="multipart/form-data">
			
				<!-- Form elements (same as file: fr_per_create.php) -->

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
					<label class="control-label">address Number</label>
					<div class="controls">
						<input name="address" type="text"  placeholder="address" value="<?php echo !empty($address)?$address:'';?>">
						<?php if (!empty($addressError)): ?>
							<span class="help-inline"><?php echo $addressError;?></span>
						<?php endif;?>
					</div>
				</div>
				
				<div class="control-group <?php echo !empty($cityError)?'error':'';?>">
					<label class="control-label">city Number</label>
					<div class="controls">
						<input name="city" type="text"  placeholder="city" value="<?php echo !empty($city)?$city:'';?>">
						<?php if (!empty($cityError)): ?>
							<span class="help-inline"><?php echo $cityError;?></span>
						<?php endif;?>
					</div>
				</div>
				
				<div class="control-group <?php echo !empty($stateError)?'error':'';?>">
					<label class="control-label">state Number</label>
					<div class="controls">
						<input name="state" type="text"  placeholder="state" value="<?php echo !empty($state)?$state:'';?>">
						<?php if (!empty($stateError)): ?>
							<span class="help-inline"><?php echo $stateError;?></span>
						<?php endif;?>
					</div>
				</div>
				
				<div class="control-group <?php echo !empty($zipError)?'error':'';?>">
					<label class="control-label">zip Number</label>
					<div class="controls">
						<input name="zip" type="text"  placeholder="zip" value="<?php echo !empty($zip)?$zip:'';?>">
						<?php if (!empty($zipError)): ?>
							<span class="help-inline"><?php echo $zipError;?></span>
						<?php endif;?>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">Title</label>
					<div class="controls">
						<select class="form-control" name="title">
							<?php 
							# editor is a volunteer only allow volunteer option
							if (0==strcmp($_SESSION['fr_person_title'],'Volunteer')) echo '<option selected value="Volunteer" >Volunteer</option>';
							else if($title==Volunteer) echo 
							'<option selected value="Volunteer" >Volunteer</option><option value="Administrator" >Administrator</option>';
							else echo
							'<option value="Volunteer">Volunteer</option>
							<option selected value="Administrator" >Administrator</option>';
							?>
						</select>
					</div>
				</div>
				
					<div class="control-group <?php echo !empty($addressError)?'error':'';?>">
					<label class="control-label">Address</label>
					<div class="controls">
						<input name="address" type="text"  placeholder="Address" value="<?php echo !empty($address)?$address:'';?>">
						<?php if (!empty($addressError)): ?>
							<span class="help-inline"><?php echo $addressError;?></span>
						<?php endif;?>
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
					<button type="submit" class="btn btn-success">Update</button>
					<a class="btn" href="fr_persons.php">Back</a>
				</div>
				
			</form>
			
				<!-- Display photo, if any --> 

				<div class='control-group col-md-6'>
					<div class="controls ">
					<?php 
					if ($data['filesize'] > 0) 
						echo '<img  height=5%; width=15%; src="data:image/jpeg;base64,' . 
							base64_encode( $data['filecontent'] ) . '" />'; 
					else 
						echo 'No photo on file.';
					?><!-- converts to base 64 due to the need to read the binary files code and display img -->
					</div>
				</div>
				
		</div><!-- end div: class="span10 offset1" -->
		
    </div> <!-- end div: class="container" -->
	
</body>
</html>
