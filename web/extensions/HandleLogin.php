<html>
	<head>
		<script type="text/javascript">
			function redirectAfterLogin(url) {
				window.location = url;
			}
		</script>
	</head>
	<body>
		<?php
            require("Credentials.php");
            require("DatabaseConnection.php");

			parse_str($_SERVER['QUERY_STRING']);
		
			//Verify that the access token belongs to us
			$c = curl_init("https://api.amazon.com/auth/o2/tokeninfo?access_token=" . urlencode($access_token));
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
			 
			$r = curl_exec($c);
			curl_close($c);
			$d = json_decode($r);
			 
			if ($d->aud != $AMAZON_TOKEN) {
			  //The access token does not belong to us
			  header('HTTP/1.1 404 Not Found');
			  echo 'Page not found';
			  exit;
			}
			 
			//Exchange the access token for user profile
			$c = curl_init("https://api.amazon.com/user/profile");
			curl_setopt($c, CURLOPT_HTTPHEADER, array("Authorization: bearer " . $access_token));
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
			 
			$r = curl_exec($c);
			curl_close($c);
			$d = json_decode($r);

            $sql = sprintf("SELECT COUNT(UserID) FROM Users WHERE UserID = '%s'", $d->user_id);

            $database = new Database();
            $result = $database->select($sql);
			
			if (!count($result) == 1) {
				echo sprintf("Welcome Back %s!", $d->name);
			} else {
				$sql = sprintf("INSERT INTO Users (Name, Email, UserID) VALUES ('%s', '%s', '%s')", $d->name, $d->email, $d->user_id);
				$database->query($sql);
			}
			
			echo "<script type=\"text/javascript\">";
			echo "redirectAfterLogin(\"" . $location . "\");";
			echo "</script>";
		?>
	</body>
</html>