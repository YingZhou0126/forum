<?php
/**
 * Created by PhpStorm.
 * User: achen
 * Date: 8/4/2018
 * Time: 8:14 PM
 */
require_once 'config.php';


session_start();
function xiu_get_current_user(){
    if (empty($_SESSION['current_login_user'])){
        header('Location:login.php');
        exit();
    }
    return $_SESSION['current_login_user'];
}


function xiu_connect (){
    $conn=mysqli_connect(DB_HOST,DB_USER, DB_PASS, DB_NAME);
    if (!$conn){
        die('connect fail');
    }
    return $conn;
}

function xiu_query($sql){
    $conn=xiu_connect();
    $query=mysqli_query($conn,$sql);
    if (!$query){
        exit('wrong') ;
    }
    while($row=mysqli_fetch_assoc($query)){
        $all_result[]=$row;
    }

    mysqli_free_result($query);
    mysqli_close($conn);
    return $all_result;

}


function xiu_fetch_one($sql){
    $res= xiu_query($sql);
    return isset($res[0])? $res[0]: null;

}

function xiu_execute($sql){
    $conn=xiu_connect();
    $result=mysqli_query($conn, $sql);
    if ($result){
        $affected_row=mysqli_affected_rows($conn);
    }
    mysqli_close($conn);
    return isset($affected_row)? $affected_row: 0;

}

//  pagination
/*
 *
 * $format <?php xiu_pagination(2, 10, '/list.php?page=%d', 5); ?>
 * */

function xiu_pagination($page, $total_page, $format, $size=5){
    $region = floor($size)/2;
    $begin = $page - $region;
    $begin = $begin < 1 ? 1 : $begin;
    $end = $begin + $size - 1;
    $end = $end > $total_page ? $total_page: $end;
    $begin = $end - $size + 1;
    $begin = $begin <1 ? 1: $begin;

    //last page
    if ($page-1>0){
        printf('<li><a href="%s">&laquo;</a></li>', sprintf($format, $page-1));
    }

    // last misc
    if ($begin > 1) {
        print('<li class="disabled"><span>···</span></li>');
    }


    for ($i = $begin; $i <= $end; $i++ ){
        $className = $i == $page ? ' class="active" ': '';
        printf('<li %s><a href="%s"> %d </a></li>', $className, sprintf($format, $i), $i);
    }

    //next misc
    if ($end < $total_page) {
        print('<li class="disabled"><span>···</span></li>');
    }

    //next page
    if ($page+1 <= $total_page){
        printf('<li><a href="%s">&raquo;</a></li>', sprintf($format, $page+1));
    }

}