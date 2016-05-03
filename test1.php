<?php
include_once 'securityLayer.php';
$mysql_username = "root";
$mysql_password = "root";

mysql_connect('localhost', $mysql_username, $mysql_password) or die("Unable to connect to mysql.");
mysql_select_db('cse545') or die("Unable to select database cse545");
$dbh = new PDO("mysql:host=localhost:8889;dbname=cse545", $mysql_username, $mysql_password);

if (isset($_POST['submitfile']))
{
   $tmp_file = $_FILES['file']['tmp_name'];
   $h = fopen($tmp_file, "r") or die("unable to read tmp file");
   $uploaded = fread($h, filesize($tmp_file));

   $query = sprintf("insert into files (name, password, content) values ('%s', '%s', '%s')", mysql_real_escape_string($_POST['name']), mysql_real_escape_string($_POST['password']), mysql_real_escape_string($uploaded));
   queryCheck($dbh, $query) or die("unable to submit the query".mysql_error());

   header('Location: '.$_SERVER['PHP_SELF']);
   exit;
}
else if (isset($_POST['submitaccess']))
{
   $res = queryCheck($dbh, "select content from files where name = '${_POST['name']}' and password = '${_POST['password']}'") or die(mysql_error());
   if ($row = mysql_fetch_array($res))
   {
	  $contents = $row['content'];
	  header('Content-Type: text/plain');
	  echo $contents;
	  exit;
   }
   else
   { ?>
<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>File Storage</title>
</head>

<body>
	 <h1>Error</h1>
		 <p>Did not find file with username/password <?php echo htmlentities($_POST['name']) . "/" . htmlentities($_POST['password']); ?></p>
</body>
</html>
<?php
     exit;																												  
   }
   
}
else {

?>
<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>File Storage</title>
</head>

<body>
	 <h1>Welcome to our file storage system</h1>
	  <p>Access your uploaded file:</p>
	  <form method="POST">
		Name: <input name="name" type="text"><br>
		Password: <input name="password" type="text"><br>	  
		<input name="submitaccess" type="submit">
	  </form>
	  
	 <p>Upload your file:</p>
	 <form enctype="multipart/form-data" method="POST">
	   Name: <input name="name" type="text"><br>
	   Password: <input name="password" type="text"><br>	  
	   File: <input name="file" type="file"><br>
	   <input name="submitfile" type="submit">
	 </form>
	  
</body>
</html>

<?php } ?>
