<section class="body-sec">
        <div class="title-row">
            <div class="container">
                <h2>How do you want to make a difference?</h2>   
            </div>
        </div>
        <div class="become-choose">
            <div class="container">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="center-col">
                            <figure class="profile-img">
                                <img src="<?php echo SITE_URL?>/images/img1.png" alt="" />                               
                                <a href="#" class="hover-caption"><i class="course-icon icon-sprites"></i></a>
                            </figure>
                             <?php
                            	echo $this->Html->link(
                            		'I want to create a course', array(
                            			'controller' => 'courses', 'action' => 'add'
									), array(
										'class' => 'button-style green-bg'
									)
								)
							?>
                        </div>
                    </div>                        
                    <div class="col-sm-6">
                        <div class="center-col">
                            <figure class="profile-img">
                                <img src="<?php echo SITE_URL?>/images/img1.png" alt="" />                               
                                <a href="#" class="hover-caption"><i class="tutor-icon icon-sprites"></i></a>
                            </figure>
                            <?php
                            	echo $this->Html->link(
                            		'I want to become a tutor', array(
                            			'controller' => 'users', 'action' => 'tutor_profile'
									), array(
										'class' => 'button-style check_login_action'
									)
								)
							?>
                        </div>                             
                    </div>                             
                </div>
            </div>
        </div>
        <div class="an-educator">
            <div class="sec-wrap">
                <h3>Anyone can become an educator!</h3>
                <ul>
                	 <li>
                        <figure><i class="icon-sprites compress-icon"></i></figure>
                        <p>No sign up fees. Completely free access to your own learning suite</p>
                    </li>
                    <li>
                        <figure><i class="icon-sprites dollor-icon"></i></figure>
                        <p>Set your own prices. we take
                            a small 25% commission,
                            you keep the rest</p>
                    </li>
                    <li>
                        <figure><i class="icon-sprites team-icon"></i></figure>
                        <p>Dedicated support team
                            and resources to help
                            you succeed</p>
                    </li>
                    <li>
                        <figure><i class="icon-sprites audience-icon"></i></figure>
                        <p>Sell your knowledge to a worldwide audience</p>
                    </li>
                    <li>
                        <figure><i class="icon-sprites ownership-icon"></i></figure>
                        <p>Retain 100% ownership of your content</p>
                    </li>
                </ul>
            </div>
        </div>

    </section>