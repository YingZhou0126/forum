<?php
 require_once '../common.php';

 //当前用户
$current_user=xiu_get_current_user();

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Comments &laquo; Admin</title>
  <link rel="stylesheet" href="../static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="../static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="../static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="../static/assets/css/admin.css">
  <script src="../static/assets/vendors/nprogress/nprogress.js"></script>
    <style>
        .lds-spinner {
            color: official;
            display: flex;
            position: relative;
            width: 64px;
            height: 64px;
        }
        .lds-spinner div {
            transform-origin: 32px 32px;
            animation: lds-spinner 1.2s linear infinite;
        }
        .lds-spinner div:after {
            content: " ";
            display: block;
            position: absolute;
            top: 3px;
            left: 29px;
            width: 5px;
            height: 14px;
            border-radius: 20%;
            background: #fff;
        }
        .lds-spinner div:nth-child(1) {
            transform: rotate(0deg);
            animation-delay: -1.1s;
        }
        .lds-spinner div:nth-child(2) {
            transform: rotate(30deg);
            animation-delay: -1s;
        }
        .lds-spinner div:nth-child(3) {
            transform: rotate(60deg);
            animation-delay: -0.9s;
        }
        .lds-spinner div:nth-child(4) {
            transform: rotate(90deg);
            animation-delay: -0.8s;
        }
        .lds-spinner div:nth-child(5) {
            transform: rotate(120deg);
            animation-delay: -0.7s;
        }
        .lds-spinner div:nth-child(6) {
            transform: rotate(150deg);
            animation-delay: -0.6s;
        }
        .lds-spinner div:nth-child(7) {
            transform: rotate(180deg);
            animation-delay: -0.5s;
        }
        .lds-spinner div:nth-child(8) {
            transform: rotate(210deg);
            animation-delay: -0.4s;
        }
        .lds-spinner div:nth-child(9) {
            transform: rotate(240deg);
            animation-delay: -0.3s;
        }
        .lds-spinner div:nth-child(10) {
            transform: rotate(270deg);
            animation-delay: -0.2s;
        }
        .lds-spinner div:nth-child(11) {
            transform: rotate(300deg);
            animation-delay: -0.1s;
        }
        .lds-spinner div:nth-child(12) {
            transform: rotate(330deg);
            animation-delay: 0s;
        }
        @keyframes lds-spinner {
            0% {
                opacity: 1;
            }
            100% {
                opacity: 0;
            }
        }
        .lds-spinner{
            background-color: rgba(0,0,0,0.7);
            position: fixed;
            top: 50%;
            left: 50%;
            z-index: 999;
            display: none;
        }
    </style>
</head>
<body>
// loading style
<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
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
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong> 发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <div class="btn-batch" style="display: none">
          <button class="btn btn-info btn-sm">批量批准</button>
          <button class="btn btn-warning btn-sm">批量拒绝</button>
          <button class="btn btn-danger btn-sm">批量删除</button>
        </div>
        <ul class="pagination pagination-sm pull-right"></ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>作者</th>
            <th width="500">评论</th>
            <th>评论在</th>
            <th>提交于</th>
            <th>状态</th>
            <th class="text-center" width="140">操作</th>
          </tr>
        </thead>
        <tbody> </tbody>
      </table>
    </div>
  </div>

  <?php $current_page = 'comments'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="../static/assets/vendors/jquery/jquery.js"></script>
  <script src="../static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="../static/assets/vendors/jsrender/jsrender.js"></script>
  <script src="../static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>
  <script>
   $(document)
       .ajaxStart(function () {
            $('.lds-spinner').css('display','flex')
   })
       .ajaxStop(function () {
           $('.lds-spinner').css('display', 'none');
       });

  </script>

  <script id="comment_tmpl" type="text/x-jsrender">
    {{for data}}
    <tr class="{{: status === 'held' ? 'warning' : status === 'rejected' ? 'danger' : '' }}" data-id="{{: id }}">
      <td class="text-center"><input type="checkbox"></td>
      <td>{{: author }}</td>
      <td>{{: content }}</td>
      <td>{{: title }}</td>
      <td>{{: created}}</td>
      <td>{{: status === 'held' ? '待审' : status === 'rejected' ? '拒绝' : '准许' }}</td>
      <td class="text-center">
        {{if status === 'held'}}
        <a class="btn btn-info btn-xs btn-edit" href="javascript:;" data-status="approved">批准</a>
        <a class="btn btn-warning btn-xs btn-edit" href="javascript:;" data-status="rejected">拒绝</a>
        {{/if}}
        <a class="btn btn-danger btn-xs btn-delete" href="javascript:;">删除</a>
      </td>
    </tr>
    {{/for}}

  </script>

  <script>
      var currentPage=1;
      function loadPageData(page){
          $('tbody').fadeOut();
          $.get( 'api/user_comments.php', {page:page}, function (res) {
              console.log(res);
              console.log(res.comment);

              // if we delete data, make sure the page not exceed the last page
              if (page>res.page){
                  loadPageData(res.page);
                  return false;
              }
              // destroy, because it cannot be created twice.
              $('.pagination').twbsPagination('destroy');
              $('.pagination').twbsPagination({

                  // make sure the start is current page, otherwise it will be the 1st page
                  //because we destroy and create a new pagination

                  startPage : page,
                  totalPages : res.page,
                  visiblePages : 5,
                  initiateStartPageClick: false,
                  onPageClick: function (e, page) {
                      loadPageData(page);
                  }
              });

              var html=$('#comment_tmpl').render({ data : res.comment });     //use
              $('tbody').html(html).fadeIn();
              currentPage=page;
          });
      }


      //delete data
      $('tbody').on('click','.btn-delete', function () {
          var id=$(this).parent().parent().data('id');
          console.log(id);
          $.get('comment-delete.php', {id : id}, function (res) {
              if (!res.success)  return;
              loadPageData(currentPage);
          })
      } );

    loadPageData(currentPage);

  </script>

</body>
</html>
