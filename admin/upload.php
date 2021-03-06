<?php
/**
 * 接收文件上传请求
 */

// 如果选择了文件 $_FILES['file']['error'] => 0
//if (empty($_FILES['file']['error'])) {
//  // PHP 在会自动接收客户端上传的文件到一个临时的目录
//  $temp_file = $_FILES['file']['tmp_name'];
//  // 我们只需要把文件保存到我们指定上传目录
//  $target_file = '..../static/uploads/' . $_FILES['file']['name'];
//  if (move_uploaded_file($temp_file, $target_file)) {
//    $image_file = '../static/uploads/' . $_FILES['file']['name'];
//  }
//}
//
//// 设置响应类型为 JSON
//header('Content-Type: application/json');
//
//if (empty($image_file)) {
//  echo json_encode(array(
//    'success' => false
//  ));
//} else {
//  echo json_encode(array(
//    'success' => true,
//    'data' => $image_file
//  ));
//}



if (empty($_FILES['avatar'])){
    exit('must upload file');
}

$avatar = $_FILES['avatar'];
if ($avatar['error']!='UPLOAD_ERO_OK'){
    exit('file upload fail');
}

// file move

$ext=pathinfo($avatar['name'], PATHINFO_EXTENSION);

$target='../static/uploads/'.uniqid(). '.'.$ext;


//var_dump($avatar);
//var_dump($ext);

if (!move_uploaded_file($avatar['tmp_name'], $target)){
    exit('upload fail');
}

echo substr($target, 2);

