<?php
	// See all errors and warnings
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);

	$server = "localhost";
	$username = "root";
	$password = "";
	$database = "dbUser";
	$mysqli = mysqli_connect($server, $username, $password, $database);

	$email = isset($_POST["loginEmail"]) ? $_POST["loginEmail"] : false;
	$pass = isset($_POST["loginPass"]) ? $_POST["loginPass"] : false;
	session_start();

	// if email and/or pass POST values are set, set the variables to those values, otherwise make them false
?>

<!DOCTYPE html>
<html>
<head>
	<title>IMY 220 - Assignment 2</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta charset="utf-8" />
	<meta name="author" content="Tshegofatsho Motlatle">
	<!-- Replace Name Surname with your name and surname -->
</head>
<body>
	<div class="container">
		<?php
			if($email && $pass){
				$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
				$res = $mysqli->query($query);
				if($row = mysqli_fetch_array($res))
				{

					$_SESSION["row"] = $row;
					$_SESSION["loggedIn"] = true;
					echo 	"<table class='table table-bordered mt-3'>
								<tr>
									<td>Name</td>
									<td>" . $row['name'] . "</td>
								<tr>
								<tr>
									<td>Surname</td>
									<td>" . $row['surname'] . "</td>
								<tr>
								<tr>
									<td>Email Address</td>
									<td>" . $row['email'] . "</td>
								<tr>
								<tr>
									<td>Birthday</td>
									<td>" . $row['birthday'] . "</td>
								<tr>
							</table>";

					echo 	"<form method='POST' enctype='multipart/form-data' action='login.php'>
								<div class='form-group'>
									<input type='file' class='form-control' name='picToUpload[]' id='picToUpload' multiple/><br/>
									<input type='submit' class='btn btn-standard' value='Upload Image' name='submit' />
									<input type='hidden' name='loginEmail' value='".$email."'/>
									<input type='hidden' name='loginPass' value='".$pass."'/>
								</div>
						  	</form>";
				}
				else{
					$_SESSION["loggedIn"] = false;
					echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							You are not registered on this site!
	  						</div>';
				}
			}
			else {
				$_SESSION["loggedIn"] = false;
				echo 	'<div class="alert alert-danger mt-3" role="alert">
	  						Could not log you in
	  					</div>';
			}
		?>
	</div>
	<div class="container">
		<?php
			if($_SESSION["loggedIn"] == true)
			{
				$target_dir = "gallery/";
				$upload_file = isset($_FILES["picToUpload"]) ? $_FILES["picToUpload"] : false;;
				if ($upload_file)
				{
					$countfiles = count($upload_file['name']);
					echo $countfiles;
					for ($i=0; $i < $countfiles ; $i++)
					{
						$target_file = $target_dir . basename($upload_file["name"][$i]);
						$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
						if ($upload_file["size"][$i] > 1000000)
						{
							echo 	'<div class="alert alert-danger mt-3" role="alert">
							File Too big
							</div>';
						}
						else if (($imageFileType != "jpg") && ($imageFileType != "jpeg"))
						{
							echo 	'<div class="alert alert-danger mt-3" role="alert">
							Sorry only JPEG and JPG pictures allowed
							</div>';
						}
						else
						{
							if (move_uploaded_file($upload_file["tmp_name"][$i], $target_file))
							{
								$row = $_SESSION['row'];
								$query = "INSERT INTO tbgallery VALUES (0, {$row['user_id']}, '{$upload_file['name'][$i]}')";
								$res = $mysqli->query($query);
							}
							else
							{
								echo 	'<div class="alert alert-danger mt-3" role="alert">
								Sorry there was an error uploading your file
								</div>';
							}

						}
					}
				}
			}
		 ?>

		 <h3>Image Gallery</h3><br/>
		 <div class="row imageGallery">
			 <?php
			 $row_O = $_SESSION['row'];
			 $query = "SELECT filename FROM tbgallery WHERE user_id = {$row_O['user_id']}";
			 $result = $mysqli->query($query);
			 if (!$result)
			 {
				 die('Invalid query: ' . mysql_error());
			 }
			 else if ($result->num_rows > 0)
			 {
				 while ($row = mysqli_fetch_array($result))
				 {
					 echo 	'<div class="col-3" style="background-image: url(gallery/'.$row["filename"].');"></div>';
				 }
			 }
			 ?>
		 </div>
	</div>

</body>
</html>
