<?
/**
 * @param string user
 * @param string password
 * @assert ('admin','admin')==true
 */
$newpasswd = getparam("newpasswd");

$password = getparam("pass");
$extension= $_SESSION['extension'] ;
if ($extension)
    $extension = $conn->escapeSimple($extension);
if ($password)
    $password = $conn->escapeSimple($password);

    if ($extension) {
        $sql = "SELECT * from extensions where extension = '$extension' and password = '$password'";
        if (query_to_array($sql)) {
             $sql=sprintf("UPDATE  extensions SET  password = '$newpasswd' WHERE extension = '$extension' and password = '$password'" );
        query($sql); 
        
          
   echo (out(array("success" => true, "message" => 'pwd_change')));
   
        } else {
        echo (out(array("success" => false, "message" => 'pwd_incorrect'))); }
    } 
?>