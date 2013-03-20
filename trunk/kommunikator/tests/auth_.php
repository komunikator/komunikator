<?

/**
 * @param string user
 * @param string password
 */
chdir(dirname(__FILE__) . '\..\src');
$_GET['action'] = "auth";
if (isset($argv[1]))
    $_POST['user'] = $argv[1];
if (isset($argv[2]))
    $_POST['password'] = $argv[2];

include 'data.php';
?>