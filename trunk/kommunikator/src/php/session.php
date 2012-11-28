<?//session_start();

$ext_path = 'ext/';

if (isset($_GET['lang'])) 
    $_SESSION['lang'] = $_GET['lang'];

if (!isset($_SESSION['lang']))
    $_SESSION['lang'] = 'ru';

if($_ENV["HTTP_X_FORWARDED_FOR"])
    $_SESSION["ip"] = $_ENV["HTTP_X_FORWARDED_FOR"].":".$_SERVER["REMOTE_ADDR"];
else
    $_SESSION["ip"] = $_SERVER["REMOTE_ADDR"];
$_SESSION["lasttime"] = date("d.m.Y H:i:s");

if (file_exists($ext_path.'ext-all.js')) {
//    echo '<script type="text/javascript" src="'.$ext_path.'bootstrap.js"></script>'."\n";
    echo '<script type="text/javascript" src="'.$ext_path.'ext-all.js"></script>'."\n";
}
else die('File "ext-all.js" is not found');

$app_path = 'js/app/';

if (file_exists($app_path.'locale/'.$_SESSION['lang'].'.js'))
    echo '<script type="text/javascript" src="'.$app_path.'locale/'.$_SESSION['lang'].'.js"></script>'."\n";
else
if (file_exists($app_path.'locale/ru.js'))
    echo '<script type="text/javascript" src="'.$app_path.'locale/ru.js"></script>'."\n";

if ($_SESSION['lang']!='en' && file_exists($ext_path.'locale/ext-lang-'.$_SESSION['lang'].'.js'))
    echo '<script type="text/javascript" src="'.$ext_path.'locale/ext-lang-'.$_SESSION['lang'].'.js"></script>'."\n";


if (isset($_GET['style']))
    $_SESSION['style'] = $_GET['style'];

if (!isset($_SESSION['style']))
//    $_SESSION['style'] = 'clifton';
    $_SESSION['style'] = 'blue';

if (isset($_GET['refresh']))
    $refresh = $_GET['refresh'];

if (isset($_GET['refreshtime']))
    $refreshtime = $_GET['refreshtime'];

if (file_exists($ext_path.'resources/css/ext-all-'.$_SESSION['style'].'.css'))
    echo '<link rel="stylesheet" type="text/css" href="'.$ext_path.'resources/css/ext-all-'.$_SESSION['style'].'.css" />';
    else
    echo '<link rel="stylesheet" type="text/css" href="'.$ext_path.'resources/css/ext-all.css" />';





?>