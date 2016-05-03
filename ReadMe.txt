1. Put my library (securityLayer.php) at the same directory with the test file.
2. add following two lines in your test file:

include_once 'securityLayer.php';
$dbh = new PDO("mysql:host=localhost:8889;dbname=cse545", $mysql_username, $mysql_password);

The parameters may vary depending on your configuration.

3. change all the “mysql_query($query)” to “queryCheck($dbh, $query)”, for example:

queryCheck($dbh, $query) or die("unable to submit the query".mysql_error());
$res = queryCheck($dbh, "select content from files where name = '${_POST['name']}' and password = '${pass}'") or die(mysql_error());
