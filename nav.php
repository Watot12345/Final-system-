<nav class="side-bar">
			<div class="user-p">
				<img src="3.png">
				<h4>@<?=$_SESSION['role']?></h4>
			</div>
        <!-- Employee navigation-->
        <?php
        $user = $_SESSION["role"];

		if($user == "employee" ) {
        ?>
        
			<ul>
				<li>
					<a href="#">
						<i class="fa fa-tachometer" aria-hidden="true"></i>
						<span>Dashboard</span>
					</a>
				</li>
				<li>
					<a href="attendance.php">
						<i class="fa-solid fa-list-check" aria-hidden="true"></i>
						<span>My Attendance</span>
					</a>
				</li>
				<li>
					<a href="#">
						<i class="fa fa-tasks" aria-hidden="true"></i>
						<span>My Task</span>
					</a>
				</li>
				<li class="active">
					<a href="profile.php">
						<i class="fa fa-user" aria-hidden="true"></i>
						<span>profile</span>
					</a>
				</li>
                <li>
					<a href="#">
						<i class="fa fa-bell" aria-hidden="true"></i>
						<span>Notifications</span>
					</a>
				</li>
				<li>
					<a href="logout.php">
						<i class="fa fa-sign-out" aria-hidden="true"></i>
						<span>Logout</span>
					</a>
				</li>
			</ul>
			<?php }else {  ?>
				<!-- admin navigation-->
				<ul>
				<li>
					<a href="#">
						<i class="fa fa-tachometer" aria-hidden="true"></i>
						<span>Dashboard</span>
					</a>
				</li>
				<li>
					<a href="#">
						<i class="fa fa-users" aria-hidden="true"></i>
						<span>Manage Employee</span>
					</a>
				</li>
				<li>
					<a href="#">
						<i class="fa fa-plus" aria-hidden="true"></i>
						<span>create task</span>
					</a>
				</li>
                <li>
					<a href="#">
						<i class="fa fa-tasks" aria-hidden="true"></i>
						<span>all task</span>
					</a>
				</li>
				<li>
					<a href="#">
						<i class="fa fa-bell" aria-hidden="true"></i>
						<span>Notifications</span>
					</a>
				</li>
				<li>
					<a href="logout.php">
						<i class="fa fa-sign-out" aria-hidden="true"></i>
						<span>Logout</span>
					</a>
				</li>
			</ul>
			<?php }  ?>
		</nav>