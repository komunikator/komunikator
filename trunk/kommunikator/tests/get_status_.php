<?

/**
 * @param string session
 */
chdir(dirname(__FILE__) . '\..\src');
$_GET['action'] = "get_status";
if (isset($argv[1]))
    $_POST['session'] = $argv[1];

include 'data.php';
?>