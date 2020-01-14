<!DOCTYPE html>
<html>
<head>
	<title>百度AI录音文件转文本</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <META HTTP-EQUIV="pragma" CONTENT="no-cache">
    <META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
    <META HTTP-EQUIV="expires" CONTENT="0">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url()?>skin/css/bootstrap.min.css">
    <!--	<link href="--><?php //echo base_url()?><!--skin/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">-->
    <!--	<script src="--><?php //echo base_url()?><!--skin/js/jquery.js"></script>-->
    <!--	<script src="--><?php //echo base_url()?><!--skin/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>-->
    <!--	<script type="text/javascript" src="--><?php //echo base_url()?><!--skin/js/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>-->
	<!-- 引入 echarts.js -->
<!--	<script src="--><?php //echo base_url()?><!--skin/js/echarts.min.js"></script>-->
	<script src="<?php echo base_url()?>skin/js/jquery-1.10.2.js"></script>
    <script src="<?php echo base_url()?>skin/js/bootstrap.min.js"></script>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/css/bootstrap-select.min.css">

	<!-- Latest compiled and minified JavaScript -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/js/bootstrap-select.min.js"></script>
	<!-- (Optional) Latest compiled and minified JavaScript translation files -->
    <!--    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/js/i18n/defaults-*.min.js"></script>-->
</head>
<body>
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
			  <a class="navbar-brand" href="#">任务列表</a>
			</div>
	</nav>

	<div class="container col-md-12">
        <div class="row clearfix">
            <div class="col-md-12column">
                <div class="row clearfix col-md-12" style="">
                    <div id="mod-tables">
                    </div>
                    <div class="col-md-12">
                        <form  action="<?php echo base_url()?>Main/process" method="post" enctype="multipart/form-data">
                        <div class="col-md-4">
                            <input class="form-control span8" type="file" name="uploadFile" id="uploadFile" />
                        </div>
                        <div class="col-md-4">
                            <input style="margin-right:20px" type="submit" class="btn btn-default span4" name="mod" value="上传" onclick=""/>
                        </div>
                        </form>
                    </div>
                        <table class="table table-bordered co">
                            <tr>
                                <th style="display:none">任务id</th>
                                <th>任务名</th>
                                <th>创建时间</th>
                                <th>更新时间</th>
                                <th>全文下载地址</th>
                                <th>全文细节下载地址</th>
                                <th>任务状态</th>
                                <th>操作</th>
                            </tr>
                            <?php foreach($data as $v): ?>
                                <tr>
                                    <td style="display:none"><input class="taskId" type="text" name="taskId" value="<?php echo  $v['taskId'];?>"/></td>
                                    <td><?php echo  $v['taskName'];?></td>
                                    <td><?php echo  $v['createTime'];?></td>
                                    <td><?php echo  $v['updateTime'];?></td>
                                    <td><?php echo  $v['contentFile'];?></td>
                                    <td><?php echo  $v['detailFile'];?></td>
                                    <td><?php echo  $v['status'];?></td>
                                    <td>
                                        <input style="margin-right:20px" type="button" class="sub btn btn-default" name="query" value="查询" data-taskid="<?php echo  $v['taskId'];?>"/>
<!--                                        <input style="margin-right:20px" type="button" class="sub btn btn-default" name="mod" value="修改" data-taskid="--><?php //echo  $v['taskId'];?><!--">-->
                                        <input style="margin-right:20px" type="button" class="sub btn btn-default" name="del" value="删除" data-taskid="<?php echo  $v['taskId'];?>"/>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>

                </div>
            </div>
        </div>
	</div>
<div style="display:none;">
    <script src="<?php echo base_url()?>skin/js/ajaxForm.js"></script>
    <!--	<script src="--><?php //echo base_url()?><!--skin/js/ajaxMain.js"></script>-->
</div>



</body>
</html>
