<?php
/**
 * Created by PhpStorm.
 * User: achen
 * Date: 8/4/2018
 * Time: 6:31 PM
 */

require_once ('../../config.php');

if (empty($_GET['email'])){
    exit('did not get para');
}

$email=$_GET['email'];
$conn=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
if (!$conn){
    die('lose connection');
}
else{
    $result=mysqli_query($conn, sprintf("select * from users where email= '%s' limit 1", $email));
//    $result = mysqli_query($connection, sprintf("select * from users where email = '%s' limit 1", $email));
    if ($result){
        if ($user=mysqli_fetch_assoc($result)){
            $row=$user['img'];
            echo $row;
        }
    }
    else{
        exit('cannot find user');
    }
    mysqli_close($conn);
}
