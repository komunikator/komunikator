<?
$username = getparam("user");
$extension = getparam("extension");
$password = getparam("password");

if ($username)  $username = $conn->escapeSimple($username);
if ($extension) $extension = $conn->escapeSimple($extension);
if (password)   $password = $conn->escapeSimple($password);

if ($password && ($username || $extension)) { 
    $_SESSION = array();
    if ($username) {
        $sql = "SELECT * from users where username = '$username' and password = '$password'";
        if (query_to_array($sql)) {
            $_SESSION['user'] = $username;
        }
    } else 
        if($extension) {
            $sql = "SELECT * from extensions where extension = '$extension' and password = '$password'";
            if (query_to_array($sql)) {
                $_SESSION['extension'] = $extension;
            }
        }
        
    if (isset($_SESSION['user']) || isset($_SESSION['extension'])) {
        $out = array("success"=>true,"session_name"=>session_name(),"session_id"=>session_id(),"message"=>"Auth successful");
        if (isset($_SESSION['user'])) $out['user'] = $_SESSION['user'];
        if (isset($_SESSION['extension'])) $out['extension'] = $_SESSION['extension'];
        echo (json_encode($out));  
    } 
	else echo (json_encode(array("success"=>false,"message"=>"Auth failed")));
}
?>