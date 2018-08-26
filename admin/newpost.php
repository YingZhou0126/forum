<?php
/**
 * 文章管理
 */

// 载入脚本
// ========================================

require '../common.php';

// 访问控制
// ========================================

// 获取登录用户信息
$current_user=xiu_get_current_user();



//   分页
//==========================================


//get status id and category id
$where = '1 = 1';
$search='';

if (isset($_GET['category']) && $_GET['category']!='all'){
    $where .= sprintf(" and posts.category_id = %d", $_GET['category']);
    $search .= ' & category='. $_GET['category'] ;
}

if (isset($_GET['status']) && $_GET['status']!='all'){
    $where .= sprintf(" and posts.status = '%s'", $_GET['status']);
    $search .= ' & status='. $_GET['status'] ;
}

//每页显示数据
$size=6;

//获取第几页
$page=isset($_GET['page'])? (int)$_GET['page']: 1;

//page smaller than 1
if ($page<1){
    header('Location: newpost.php?page=1'. $search);
}


//计算最大页码
$total_count=(int)xiu_fetch_one(sprintf('select count(1) as num from posts 
INNER JOIN users ON posts.user_id = users.id
INNER JOIN categories ON posts.category_id = categories.id
WHERE %s', $where))['num'];
$max_page=(int)ceil($total_count/$size);

//if page bigger than max_page
if ($page>$max_page){
    header('Location: newpost.php?page='.$max_page. $search);
}

//获取数据
//====================================================

//获取每页所有posts数据

$posts=xiu_query(sprintf('SELECT 
  posts.id,
  posts.title,
  posts.created,
  posts.status,
  categories.name AS category_name,
  users.nickname AS author_name
FROM posts
INNER JOIN users ON posts.user_id = users.id
INNER JOIN categories ON posts.category_id = categories.id
WHERE %s
ORDER BY posts.created DESC
LIMIT %d,%d', $where,($page-1)*$size, $size));

//all categories
$categories=xiu_query('select * from categories');

//计算页码

////显示页码个数
//$visiable =5;
////区间
//$region=($visiable-1)/2;
////开始页面
//$begin=$page-$region;
////结束页面
//$end=$page+$region;
//
//if ($begin<1){
//    $begin=1;
//    $end=$visiable;
//}
//
//if ($end>$max_page){
//    $end=$max_page;
//    $begin=$max_page-$visiable+1;
//    if ($begin<1){
//        $begin=1;
//    }
//}


//转化状态
function convert_status($status){
    $posts=array(
            'published'  => 'publish',
            'trashed'  => 'trash',
            'drafted'  => 'draft'
    );
        return isset($posts[$status])? $posts[$status] :'unknown';
}

//show created time
function convert_date($date){
    $timestamp=strtotime($date);
    return date('m/d/Y <b\r> H:i:s', $timestamp);
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>Posts &laquo; Admin</title>
    <link rel="stylesheet" href="../static/assets/vendors/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../static/assets/vendors/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" href="../static/assets/vendors/nprogress/nprogress.css">
    <link rel="stylesheet" href="../static/assets/css/admin.css">
    <script src="../static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
<script>NProgress.start()</script>

<div class="main">
    <nav class="navbar">
        <button class="btn btn-default navbar-btn fa fa-bars"></button>
        <ul class="nav navbar-nav navbar-right">
            <li><a href="profile.php"><i class="fa fa-user"></i>个人中心</a></li>
            <li><a href="logout.php"><i class="fa fa-sign-out"></i>退出</a></li>
        </ul>
    </nav>
    <div class="container-fluid">
        <div class="page-title">
            <h1>所有文章</h1>
            <a href="post-add.php" class="btn btn-primary btn-xs">写文章</a>
        </div>
        <!-- 有错误信息时展示 -->
        <!-- <div class="alert alert-danger">
          <strong>错误！</strong> 发生XXX错误
        </div> -->
        <div class="page-action">
            <!-- show when multiple checked -->
            <a class="btn btn-danger btn-sm btn-delete" href="post-delete.php" style="display: none">批量删除</a>

            <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']?>">
                <select name="status" class="form-control input-sm">
                    <option value="all">所有状态</option>
                    <option value="published" <?php echo isset($_GET['status']) && $_GET['status']=='published' ? 'selected': ''?>>published</option>
                    <option value="drafted" <?php echo isset($_GET['status']) && $_GET['status']=='drafted' ? 'selected': ''?>>drafted</option>
                    <option value="trashed" <?php echo isset($_GET['status']) && $_GET['status']=='trashed' ? 'selected': ''?>>trash</option>
                </select>

                <select name="category" class="form-control input-sm">
                    <option value="all">所有分类</option>
                    <?php foreach ($categories as $item ): ?>
                        <option value="<?php echo $item['id'] ?>" <?php echo isset($_GET['category']) && $item['id']==$_GET['category'] ? 'selected': ''?>><?php echo $item['name']?></option>
                  <?php endforeach ?>
                </select>
                <button class="btn btn-default btn-sm">筛选</button>
            </form>

            <ul class="pagination pagination-sm pull-right">
                <?php xiu_pagination($page, $max_page, '?page=%d' . $search); ?>
            </ul>
        </div>
        <table class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>标题</th>
                <th>作者</th>
                <th>分类</th>
                <th class="text-center">发表时间</th>
                <th class="text-center">状态</th>
                <th class="text-center" width="100">操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($posts as $item): ?>
            <tr data-id="<?php echo $item['id']; ?>">
                <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id'];?>" ></td>
                <td><?php echo $item['title']; ?></td>
                <td><?php echo $item['author_name']; ?></td>
                <td><?php echo $item['category_name']; ?></td>
                <td class="text-center"><?php echo convert_date($item['created']); ?></td>
                <td class="text-center"><?php echo convert_status($item['status']); ?></td>
                <td class="text-center">
                    <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
                    <a href="post-delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
            </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>

<?php $current_page = 'posts'; ?>
<?php include 'inc/sidebar.php'; ?>

<script src="../static/assets/vendors/jquery/jquery.js"></script>
<script src="../static/assets/vendors/bootstrap/js/bootstrap.js"></script>
<script>

<script>NProgress.done()</script>
<script>
    $(function () {
        var $btnDelete = $('.btn-delete');
        var thinput=$('.table thead input');
        var boinput=$('.table tbody input');
        var check=[];

        //all checked or all not checked
        thinput.on('change', function () {
            boinput.prop('checked',thinput.prop('checked')).trigger('change');
        });

        boinput.on('change', function () {
            var cur=$(this).data('id');
            if ($(this).prop('checked')){
                check.push(cur);
            }
            else{
                check.splice(check.indexOf(cur),1);
            }
           check.length > 0 ? $btnDelete.fadeIn() : $btnDelete.fadeOut();
            $btnDelete.prop('search','?id='+ check);

        });
    });
</script>
</body>
</html>
