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

$DIGT_path = 'DIGT/';

if (file_exists($DIGT_path.'locale/'.$_SESSION['lang'].'.js'))
    echo '<script type="text/javascript" src="'.$DIGT_path.'locale/'.$_SESSION['lang'].'.js"></script>'."\n";
else
if (file_exists($DIGT_path.'locale/ru.js'))
    echo '<script type="text/javascript" src="'.$DIGT_path.'locale/ru.js"></script>'."\n";

if ($_SESSION['lang']!='en' && file_exists($ext_path.'locale/ext-lang-'.$_SESSION['lang'].'.js'))
    echo '<script type="text/javascript" src="'.$ext_path.'locale/ext-lang-'.$_SESSION['lang'].'.js"></script>'."\n";


if (isset($_GET['style']))
    $_SESSION['style'] = $_GET['style'];

if (!isset($_SESSION['style']))
    $_SESSION['style'] = 'blue';

if (isset($_GET['refresh']))
    $refresh = $_GET['refresh'];

if (isset($_GET['refreshtime']))
    $refreshtime = $_GET['refreshtime'];

switch ($_SESSION['style']) {
    case 'blue':
        echo '<link rel="stylesheet" type="text/css" href="'.$ext_path.'resources/css/ext-all.css" />';
        break;
    case 'gray':
        echo '<link rel="stylesheet" type="text/css" href="'.$ext_path.'resources/css/ext-all-gray.css" />';
        break;
    case 'access':
        echo '<link rel="stylesheet" type="text/css" href="'.$ext_path.'resources/css/ext-all-access.css" />';
        break;
    case 'scoped':
        echo '<link rel="stylesheet" type="text/css" href="'.$ext_path.'resources/css/ext-all-scoped.css" />';
        break;
    default:
        echo '<link rel="stylesheet" type="text/css" href="'.$ext_path.'resources/css/ext-all.css" />';
} ;


?>