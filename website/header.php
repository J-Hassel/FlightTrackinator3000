<?php
$page = basename($_SERVER['PHP_SELF']);
?>

<header>
		<div class "row">
			<div class="logo">
 				<img src="logo.png">
			</div>
		
			<ul class="main-nav">
				<li class="<?php if($page == 'home.php'){ echo ' active"';}?>"><a href="home.php">Home</a></li>
				<li class="<?php if($page == 'allflights.php'){ echo ' active"';}?>"><a href="allflights.php">Map</a></li>
				<li class="<?php if($page == 'database.php'){ echo ' active"';}?>"><a href="database.php?table_name=flight&Submit=Submit">Database</a></li>
				<li class="<?php if($page == 'logout.php'){ echo ' active"';}?>"><a href="logout.php">Sign Out</a></li>
				<li class="<?php if($page == 'reset-password.php'){ echo ' active"';}?>"><a href="reset-password.php">Reset Password</a></li>
            <li class="<?php if($page == 'my_reviews.php'){ echo ' active"';}?>"><a href="my_reviews.php">Signed in as: <?php echo $_SESSION['username']; ?></a></li>
			</ul>
		</div>
</header>