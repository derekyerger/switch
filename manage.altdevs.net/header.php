<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<head>
	<meta charset="utf-8" />
	<title>Alternate Devices</title>
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
	<meta content="" name="description" />
	<meta content="" name="author" />
	
	<!-- ================== BEGIN BASE CSS STYLE ================== -->
	<link href="css/OpenSans.css" rel="stylesheet" />
	<link href="css/bootstrap-editable.css" rel="stylesheet" />
	<link href="css/jquery-ui.min.css" rel="stylesheet" />
	<link href="css/bootstrap.min.css" rel="stylesheet" />
	<link href="css/all.min.css" rel="stylesheet" />
	<link href="css/animate.min.css" rel="stylesheet" />
	<link href="css/style.min.css" rel="stylesheet" />
	<link href="css/style-responsive.min.css" rel="stylesheet" />
	<link href="css/default.css" rel="stylesheet" id="theme" />
	<link href="css/altdevs.css" rel="stylesheet" />
	<!-- ================== END BASE CSS STYLE ================== -->
	<link href="css/jquery.gritter.css" rel="stylesheet" />
	<link href="css/sweetalert.css" rel="stylesheet" />
	<link href="css/powerange.min.css" rel="stylesheet" />
	
	<!-- ================== BEGIN BASE JS ================== -->
	<script src="js/pace.min.js"></script>
	<!-- ================== END BASE JS ================== -->
	<style>
		<?= DEV_CSS ?>
	</style>
</head>
<body>
	<!-- begin page-cover -->
	<div class="page-cover" style="background-image: url(img/page-cover.jpg);"></div>
	<!-- end page-cover -->
	
	<!-- begin #page-loader -->
	<div id="page-loader" class="fade show"><span class="spinner"></span></div>
	<!-- end #page-loader -->
	
	<!-- begin #page-container -->
	<div id="page-container" class="fade page-sidebar-fixed page-header-fixed">
		<!-- begin #header -->
		<div id="header" class="header navbar-default">
			<!-- begin navbar-header -->
			<div class="navbar-header">
				<a href="javascript:;" class="navbar-brand"><span class="navbar-logo"></span> <b>alt</b>devs</a>
				<button type="button" class="navbar-toggle" data-click="sidebar-toggled">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			</div>
			<!-- end navbar-header -->
			
			<!-- begin header-nav -->
			<ul class="navbar-nav navbar-right">
				<!--li>
					<form class="navbar-form">
						<div class="form-group">
							<input type="text" class="form-control" placeholder="Enter keyword" />
							<button type="submit" class="btn btn-search"><i class="fa fa-search"></i></button>
						</div>
					</form>
				</li>
				<li class="dropdown">
					<a href="javascript:;" data-toggle="dropdown" class="dropdown-toggle f-s-14">
						<i class="fa fa-bell"></i>
						<span class="label">0</span>
					</a>
					<ul class="dropdown-menu media-list dropdown-menu-right">
						<li class="dropdown-header">NOTIFICATIONS (0)</li>
						<li class="text-center width-300 p-b-10 text-inverse">
							No notification found
						</li>
					</ul>
				</li-->
				<?php if (isset($user)) { ?>
				<li class="dropdown navbar-user">
					<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
						<div class="image image-icon bg-black text-grey-darker">
							<i class="fa fa-user"></i>
						</div>
						<span class="d-none d-md-inline"><?= $user['displayName'] ?></span> <b class="caret"></b>
					</a>
					<div class="dropdown-menu dropdown-menu-right">
						<a href="javascript:;" class="dropdown-item">Edit Profile</a>
						<a href="javascript:;" class="dropdown-item"><span class="badge badge-danger pull-right">2</span> Inbox</a>
						<a href="javascript:;" class="dropdown-item">Calendar</a>
						<a href="javascript:;" class="dropdown-item">Setting</a>
						<div class="dropdown-divider"></div>
						<a href="javascript:;" class="dropdown-item">Log Out</a>
					</div>
				</li>
				<?php } ?>
			</ul>
			<!-- end header navigation right -->
		</div>
		<!-- end #header -->

		<!-- begin #sidebar -->
		<div id="sidebar" class="sidebar">
			<!-- begin sidebar scrollbar -->
			<div data-scrollbar="true" data-height="100%">
				<!-- begin sidebar user -->
				<?php if (isset($user)) { ?>
				<ul class="nav">
					<li class="nav-profile">
						<a href="javascript:;" data-toggle="nav-profile">
							<div class="image image-icon bg-black text-grey-darker">
								<i class="fa fa-user"></i>
							</div>
							<div class="info">
								<b class="caret pull-right"></b>
								<?= $user['displayName'] ?>
								<small><?= $user['displayNameSubtitle'] ?></small>
							</div>
						</a>
					</li>
					<li>
						<ul class="nav nav-profile">
							<li><a href="javascript:;"><i class="fa fa-cog"></i> Settings</a></li>
							<li><a href="javascript:;"><i class="fa fa-pencil-alt"></i> Send Feedback</a></li>
							<li><a href="javascript:;"><i class="fa fa-question-circle"></i> Help</a></li>
						</ul>
					</li>
				</ul>
				<!-- end sidebar user -->
				<?php } ?>
				<!-- begin sidebar nav -->
				<ul class="nav">
					<li class="nav-header">Navigation</li>
					<li class="active has-sub">
						<a href="javascript:;" onclick="retr('page', 'Home');">
							<i class="fa fa-th-large"></i>
							<span>Home</span>
						</a>
						<ul class="sub-menu">
							<li><a href="javascript:" onclick="retr('page', 'Home.status');">Status only</a></li>
							<li><a href="javascript:" onclick="retr('page', 'Home.assignments');">Assignments</a></li>
						</ul>
					</li>
					<li>
						<a href="javascript:" onclick="retr('page', 'Templates')">
							<i class="fa fa-book"></i>
							<span>Templates</span>
						</a>
					</li>
					<li>
						<a href="javascript:" onclick="retr('page', 'Help')">
							<i class="fa fa-info"></i>
							<span>Help</span>
						</a>
					</li>
					<li>
						<a href="javascript:" onclick="calibrate();">
							<i class="fa fa-puzzle-piece"></i>
							<span>Calibration</span>
						</a>
					</li>
					<li>
						<a href="javascript:" onclick="retr('page', 'Advanced')">
							<i class="fa fa-list-ul"></i>
							<span>Advanced</span>
						</a>
					</li>
					<!-- begin sidebar minify button -->
					<li><a href="javascript:;" class="sidebar-minify-btn" data-click="sidebar-minify"><i class="fa fa-angle-double-left"></i></a></li>
					<!-- end sidebar minify button -->
				</ul>
				<!-- end sidebar nav -->
			</div>
			<!-- end sidebar scrollbar -->
		</div>
		<div class="sidebar-bg"></div>
		<!-- end #sidebar -->
		
		<!-- begin #content -->
		<div id="content" class="content">
