<div id="header">
    <div id="head_lt">
	    <!--Logo Start from Here-->
	    <span class="floatleft">
			<?php
				echo $this->Html->image("/admin_images/logo.png", array(
						"alt" => "Home",
						'url' => array('controller' => 'users', 'action' => 'dashboard', 'admin' => true, 'plugin' => false)
					));
			
			?>
		</span>
		<span class="slogan">administration suite</span>
	    <!--Logo end  Here-->
    </div>  
	
	<?php if (!empty($userinfo)): ?>
	<div id="head_rt">
		Welcome <span><?php echo $userinfo['username']; ?></span>
		&nbsp;&nbsp;&nbsp;&nbsp; <?php echo date('d M, Y h:i A'); ?>
	</div>
	<?php endif; ?>
</div>
		<?php if (!empty($userinfo)): ?>
		<div class="menubg">
			<div class="nav">
				<ul id="navigation">
					<li onmouseout="this.className=''" onmouseover="this.className='hov'">
						<?php echo $this->Html->link('Home', array('controller'=>'users', 'action'=>'dashboard', 'admin'=> true, 'plugin' => false), array('class' => '')); ?>
					</li>
					<li onmouseout="this.className=''" onmouseover="this.className='hov'">
						<?php echo $this->Html->link('User Management',  array('controller'=>'users', 'action'=>'admin_manage_users', 'admin'=> true, 'plugin' => false), array('class' => '')); ?>
						<div class="sub">
							<ul>
								<li>
									<?php echo $this->Html->link('Manage Users', array('controller'=>'users', 'action'=>'admin_manage_users', 'admin'=> true, 'plugin' => false), array('class'=>'')); ?>
								</li>
								<?php if($this->Session->read('Auth.User.group_id') == SUPER_ADMIN) {?>
								<li>
									<?php echo $this->Html->link('Manage Admins', array('controller'=>'users', 'action'=>'admin_manage_admins', 'admin'=> true, 'plugin' => false), array('class'=>'')); ?>
								</li>
								<?php }?>
							</ul>
						</div>
					</li>
					<li onmouseout="this.className=''" onmouseover="this.className='hov'">
						<?php echo $this->Html->link('Order Management',  array('controller'=>'orders', 'action'=>'admin_index', 'admin'=> true, 'plugin' => false), array('class' => '')); ?>
						<div class="sub">
							<ul>
								<li>
									<?php echo $this->Html->link('Manage Orders', array('controller'=>'orders', 'action'=>'index', 'admin'=> true, 'plugin' => false), array('class'=>'')); ?>
								</li>
							</ul>
						</div>
					</li>
					<li onmouseout="this.className=''" onmouseover="this.className='hov'">
						<?php echo $this->Html->link('Location Management',  array('controller'=>'countries', 'action'=>'admin_index', 'admin'=> true, 'plugin' => false), array('class' => '')); ?>
						<div class="sub">
							<ul>
								<li>
									<?php echo $this->Html->link('Manage Countries', array('controller'=>'countries', 'action'=>'admin_index', 'admin'=> true, 'plugin' => false), array('class'=>'')); ?>
								</li>
								<!--
								<li>
									<?php echo $this->Html->link('Manage Cities', array('controller'=>'countries', 'action'=>'admin_city', 'admin'=> true, 'plugin' => false), array('class'=>'')); ?>
								</li>
								-->
							</ul>
						</div>
					</li>
					<li onmouseout="this.className=''" onmouseover="this.className='hov'">
						<?php echo $this->Html->link('Preferences',  'javascript:void(0)', array('class' => '')); ?>
						<div class="sub">
							<ul>						
								<li>
									<?php echo $this->Html->link('Manage Allergens', array('controller'=>'dishes', 'action'=>'allergy', 'admin'=> true, 'plugin' => false), array('class'=>'')); ?>
								</li>		
								<li>
									<?php echo $this->Html->link('Manage Cuisines', array('controller'=>'dishes', 'action'=>'cuisine', 'admin'=> true, 'plugin' => false), array('class'=>'')); ?>
								</li>
								<li>
									<?php echo $this->Html->link('Manage Dish Price', array('controller'=>'dishes', 'action'=>'portion', 'admin'=> true, 'plugin' => false), array('class'=>'')); ?>
								</li>	
								<li>
									<?php echo $this->Html->link('Manage Custom Dish Price', array('controller'=>'dishes', 'action'=>'manage_custom_price', 'admin'=> true, 'plugin' => false), array('class'=>'')); ?>
								</li>						
							</ul>
						</div>
					</li>
					<li onmouseout="this.className=''" onmouseover="this.className='hov'">
						<?php echo $this->Html->link('Manage Kitchen',  array('controller' => 'kitchens', 'action' => 'index','admin'=> true, 'plugin' => false), array('class' => '')); ?>
						<div class="sub">
						</div>
					</li>
					<li onmouseout="this.className=''" onmouseover="this.className='hov'">
						<?php echo $this->Html->link('Discussion Management',  array('controller'=>'discussions', 'action'=>'admin_discussion', 'admin'=> true, 'plugin' => false), array('class' => '')); ?>
						<div class="sub">
							<ul>						
								<li>
									<?php echo $this->Html->link('Manage Communities', array('controller'=>'discussions', 'action'=>'community', 'admin'=> true, 'plugin' => false), array('class'=>'')); ?>
								</li>						
							</ul>
						</div>
					</li>
					<li onmouseout="this.className=''" onmouseover="this.className='hov'">
						<?php echo $this->Html->link('Conversations',  array('controller' => 'conversations', 'action' => 'index','admin'=> true, 'plugin' => false), array('class' => '')); ?>
						<div class="sub">
						</div>
					</li>	
					<li onmouseout="this.className=''" onmouseover="this.className='hov'">
						<?php echo $this->Html->link('CMS Manager', array('controller'=>'cmspages', 'action'=>'index', 'admin'=> true, 'plugin' => false), array('class' => '')); ?>
						<div class="sub">
							<ul>
								<li>
									<?php echo $this->Html->link('Manage Static Pages', array('controller'=>'cmspages', 'action'=>'index', 'admin'=> true, 'plugin' => false), array('class'=>'')); ?>
								</li>
								<li>
									<?php echo $this->Html->link('Manage Email Templates', array('controller'=>'emailTemplates', 'action'=>'index', 'admin'=> true, 'plugin' => false), array('class'=>'')); ?>
								</li>							
								<li>
									<?php echo $this->Html->link('Manage Newsletters', array('controller'=>'newsletters', 'action'=>'index', 'admin'=> true, 'plugin' => false), array('class'=>'')); ?>
								</li>
								<li>
									<?php echo $this->Html->link('Send Newsletter', array('controller'=>'newsletters', 'action'=>'admin_send', 'admin'=> true, 'plugin' => false), array('class'=>'')); ?>
								</li>
								<li>
									<?php echo $this->Html->link('Manage Testemonials', array('controller'=>'testimonials', 'action'=>'index', 'admin'=> true, 'plugin' => false), array('class'=>'')); ?>
								</li>
								<li>
									<?php echo $this->Html->link('Manage Videos', array('controller'=>'videos', 'action'=>'index', 'admin'=> true, 'plugin' => false), array('class'=>'')); ?>
								</li>
								<li>
									<?php echo $this->Html->link('Manage Cmspage Cities', array('controller'=>'cms_cities', 'action'=>'index', 'admin'=> true, 'plugin' => false), array('class'=>'')); ?>
								</li>							
							</ul>
						</div>
					</li>
					<!--
					<?php if($this->Session->read('Auth.User.id') == SUPER_ADMIN) {?>	
					<li onmouseout="this.className=''" onmouseover="this.className='hov'">
						<?php echo $this->Html->link('Role Management', array('controller' => 'acos', 'action' => 'index', 'plugin' => 'acl', 'admin' => true), array('class' => '')); ?>
						<div class="sub">
							<ul>
								<li>
									<?php echo $this->Html->link('Manage Permissions', array('controller'=>'aros', 'action'=>'ajax_role_permissions', 'admin'=> true, 'plugin' => 'acl'), array('class'=>'')); ?>
								</li>
								<li>
									<?php echo $this->Html->link('Build Missing Aros', array('controller'=>'aros', 'action'=>'check', 'admin'=> true, 'plugin' => 'acl'), array('class'=>'')); ?>
								</li>							
								<li>
									<?php echo $this->Html->link('Syncronize Actions', array('controller'=>'acos', 'action'=>'synchronize', 'admin'=> true, 'plugin' => 'acl'), array('class'=>'')); ?>
								</li>
							</ul>
						</div>
					</li>
					<?php }?>
					-->
					<li onmouseout="this.className=''" onmouseover="this.className='hov'">
						<?php echo $this->Html->link('Site Settings', array('controller'=>'users', 'action'=>'change_password', 'admin'=> true, 'plugin' => false), array('class' => '')); ?>
						<div class="sub">
							<ul>														
								<li>
									<?php echo $this->Html->link('Manage Label', array('controller'=>'users', 'action'=>'label_list', 'admin'=> true, 'plugin' => false), array('class'=>'')); ?>
								</li>
								<li>
									<?php echo $this->Html->link('Change Password', array('controller'=>'users', 'action'=>'change_password', 'admin'=> true, 'plugin' => false), array('class'=>'')); ?>
								</li>
								<li>
									<?php echo $this->Html->link('Payment Setting', array('controller'=>'orders', 'action'=>'paymentsetting', 'admin'=> true, 'plugin' => false), array('class'=>'')); ?>
								</li>
							</ul>
						</div>
					</li>			

				</ul>
			</div>
			<div class="logout">
				<?php
					echo $this->Html->image("/admin_images/logout.gif", array(
							"alt" => "Logout",
							'url' => array('controller' => 'users', 'action' => 'logout', 'admin'=> true, 'plugin' => false)
						));
				?>
			</div>
		</div>
		<?php endif; ?>
