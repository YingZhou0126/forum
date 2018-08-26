<?php
/**
 * Created by PhpStorm.
 * User: achen
 * Date: 8/8/2018
 * Time: 5:17 PM
 */
require_once '../../common.php';

//set pagination
$page =  empty($_GET['page'])? 1 : intval($_GET[ 'page' ]);
$size = 3;
$region = ( $page-1 ) * $size;

//grt all comments data
$query=xiu_query(sprintf('select author,comments.content,title,posts.created as created,comments.status as status, comments.id as id from comments inner join posts on posts.id=comments.post_id order by posts.created desc LIMIT %d,%d ', $region, $size));

$count=xiu_fetch_one('select count(1) as num from comments inner join posts on posts.id=comments.post_id')['num'];

$total_page=ceil($count/$size);  // float类型， 但经过ceil转化 会是整数， json中显示的是数字

$json=json_encode(array(
    'page'  => $total_page,
    'comment' => $query
));

header('Content-Type: application/json');

echo $json;
