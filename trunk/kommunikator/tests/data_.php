<?

/**
 * @param key->value in json format {"ke1":"value1","key2":"value2"} 
 */
$pwd = getcwd();
chdir(dirname(__FILE__) . '/../src');
if (!isset($argv[1]))
    exit;
$params = json_decode($argv[1]);
if ($params) {
    foreach ($params as $key => $value)
        $_GET[$key] = $value;
    include 'data.php';
};
chdir($pwd);
?>