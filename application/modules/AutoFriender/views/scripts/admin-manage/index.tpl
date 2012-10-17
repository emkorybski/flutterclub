<div class="settings">
    <form method="post" action="" class="global_form">
        <div>
            <h3><?php echo $this->translate("Auto-Friendship Settings") ?></h3>
						<p>This is a great little plugin with easy install and even less hassle. Just go over to the plugin settings page and
						set the email id of the member account who will be added as friend to every new user who signs up. Honestly, that's all you have to do.
						There is no dependency and no core files are modified.</p>
						<p>&nbsp;</p>
						<h3>Other Features:</h3>
						&#8226; You can also add a member as friend to the existing users.<br />
						&#8226; Also you can disable the functionality any time.<br />
						&#8226; You can also <a href="http://technobd.com/socialengine-customization.php">contact us</a> for install and customization support.<br />
						<?php
						if ( $this->status ) {
							?>
							<ul class="form-notices">
								<li><?php echo $this->status ?></li>
							</ul>
							<?php
						}
						?>
            <div class="form-elements">
                <div class="form-wrapper">
                    <div class="form-label">
                        <label><?php echo $this->translate("Enable/disable") ?></label>
                    </div>
                    <div class="form-element">
                        <input type="checkbox" name="enable" id="enable" <?php echo $this->enable ? 'checked="checked"' : '' ?> />
                        <label class="optional" for="enable"><?php echo $this->translate("Enable Auto-friendship") ?></label>
                    </div>
                </div>
                <div class="form-wrapper">
                    <div class="form-label">
                        <label><?php echo $this->translate("Email ID of user") ?></label>
                    </div>
                    <div class="form-element">
                        <?php
                        $emails = array();
                        if ( count($this->friendUsers) ) {
                            foreach ( $this->friendUsers as $friendUser ) {
                                $emails[] = $friendUser->email;
                            }
                        }
                        ?>
                        <input type="text" value="<?php echo implode(', ', $emails); ?>" name="friendusers" /><br />
                        <p class="description"><?php echo $this->translate("Enter multiple email id seperated by comma."); ?></p>
                        <input type="checkbox" name="applyAll" id="applyAll" <?php echo $this->applyAll ? 'checked="checked"' : '' ?> />
                        <label class="optional" for="applyAll"><?php echo $this->translate("Apply to existing users?"); ?></label>
                    </div>
                </div>
                <div class="form-wrapper">
                    <div class="form-label">
                        <label><?php echo $this->translate("Chunk size") ?></label>
                    </div>
                    <div class="form-element">
                        <input type="text" value="50" name="limit" /><br />
                        <p class="description"><?php echo $this->translate("This is the number of users that will be processed at each step if you select 'Apply to existing users'.
																																					If you increase it, the processing will be quicker but may create extra load on server. Default is 50."); ?></p>
                    </div>
                </div>
                <div class="form-wrapper">
                    <div class="form-label">
                        <label>&nbsp;</label>
                    </div>
                    <div class="form-element">
                        <button type="submit" name="submit"><?php echo $this->translate("Submit"); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>