 <section class="inner-middle-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <aside class="left-part">
                            <div class="profile-info">
                                <div class="profile-setting">
                                    <ul>
                                    	<?php foreach($list as $kk => $aa){?>
                                        	<li class="<?php echo $data['Page']['url'] == $kk ? 'active' : ''?>">
                                        		<?php 
                                        			echo $this->Html->link(
                                        				$aa, array(
                                        					'controller' => 'pages', 'action' => 'terms', $kk
														)
													);
                                        		?>
                                    		</li>
                                    	<?php }?>
                                    </ul>
                                </div>
                            </div>
                        </aside>
                        <div class="full-profile-info">
                                    <!-- Tab panes -->
                            <section class="right-tabs">
                   			<div class="tab-content col-md-12 terms">
                     			  <div class="tab-pane active" id="<?php echo $data['Page']['url']?>"> 
		                            <div class="title-bar">
		                                <h1><?php echo $data['Page']['page_title']?> </h1>
		                            </div> 
		                       	<?php echo $data['Page']['page_content']?>
		                        
		                      </div>
                    		</div>
                    <div class="clear"></div>
                    </section>    
                                    <!-- Tabs section ends here-->    
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<!--Instructors section Ends Here-->
<?php echo $this->element('trending'); ?>
<?php echo $this->element('newsletter'); ?>