<?
/**
 * @param string user
 * @param string password
 * @assert ('admin','admin')==true
 */

$username = getparam("user");
$extension = getparam("extension");
$password = getparam("password");

//- - - - - - - - - - - - -  --  - - - - - 
if ($username)
  $extension = $username ;
//- - - - - -  - - - - - -  - - - -  - -  -
if ($username)
    $username = $conn->escapeSimple($username);
if ($extension)
    $extension = $conn->escapeSimple($extension);
if ($password)
    $password = $conn->escapeSimple($password);

if ($password && ($username || $extension)) {
    session_start();
    $_SESSION = array();

    if ($username) {
        $sql = "SELECT * from users where username = '$username' and password = '$password'";
        if (query_to_array($sql)) {
            $_SESSION['user'] = $username;
            $_SESSION['time_offset'] = getparam("time_offset");
            $sql = "insert into actionlogs (date,performer,log,ip) values (" . time() . ",\"{$_SESSION['user']}\",\"username $username logged in\", \"{$_SERVER['REMOTE_ADDR']}\")";
            query($sql);
        }
    } /*else*/
    if ($extension) {
        $sql = "SELECT * from extensions where extension = '$extension' and password = '$password'";
        if (query_to_array($sql)) {
            $_SESSION['extension'] = $extension;
            $_SESSION['time_offset'] = getparam("time_offset");
            $sql = "insert into actionlogs (date,performer,log,ip) values (" . time() . ",\"{$_SESSION['extension']}\",\"extension $extension logged in\", \"{$_SERVER['REMOTE_ADDR']}\")";
            query($sql);
        }
    }
    session_write_close();
    if (isset($_SESSION['user']) || isset($_SESSION['extension'])) {
        $out = array("success" => true, "session_name" => session_name(), "session_id" => session_id()/* ,"message"=>"Auth successful" */);
        if (isset($_SESSION['user']))
            $out['user'] = $_SESSION['user'];
        if (isset($_SESSION['extension']))
            $out['extension'] = $_SESSION['extension'];
        echo (out($out));
    }
    else {
        $sql = "insert into actionlogs (date,performer,log,ip) values (" . time() . ",\"{$_SESSION['user']}\",\"failled attempt to log in as unknown : $extension$username\", \"{$_SERVER['REMOTE_ADDR']}\")";
        query($sql);
        echo (out(array("success" => false, "message" => "auth_failed")));
    }
}
?>