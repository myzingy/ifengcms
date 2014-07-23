<h3><?php print $header?></h3>
<?php print form_open('auth/register',array('class'=>'horizontal'))?>
    <fieldset>
        <ol>
            <li>
                <label for="username"><?php print $this->lang->line('userlib_username')?>:</label>
                <input type="text" name="username" id="username" size="32" class="text" value="<?php print $this->validation->username?>" />
                
                <button onclick="sendcode();" type="button">
        			发送验证码
        		</button>
            </li>
            <li>
                <label for="code">验证码:</label>
                <input type="text" name="code" id="code" class="text"  value="<?php print $this->validation->code?>" />
            </li>
            <li>
                <label for="password"><?php print $this->lang->line('userlib_password')?>:</label>
                <input type="password" name="password" id="password" size="32" class="text" />
            </li>
            <li>
                <label for="confirm_password"><?php print $this->lang->line('userlib_confirm_password')?>:</label>
                <input type="password" name="confirm_password" id="confirm_password" size="32" class="text" />
            </li>
            <?php
            // Only display captcha if needed
            if($this->preference->item('use_registration_captcha')){
            ?>
            <li class="captcha">
                <label for="recaptcha_response_field"><?php print $this->lang->line('userlib_captcha')?>:</label>
                <?php print $captcha?>
            </li>
            <?php } ?>
            <li class="submit">
            	<div class="buttons">
            		<button type="submit" class="positive" name="submit" value="submit">
            			<?php print $this->bep_assets->icon('user') ?>
            			<?php print $this->lang->line('userlib_register')?>
            		</button>
            		
            		<a href="<?php print site_url('auth/login') ?>" class="negative">
            			<?php print $this->bep_assets->icon('cross') ?>
            			<?php print $this->lang->line('general_cancel')?>
            		</a>
            	</div>
            </li>
        </ol>
    </fieldset>
<?php print form_close()?>
<script type="text/javascript">
	function sendcode(){
		var phone=$.trim($('#username').val());
		if(!phone){
			alert('请输入手机号');
			return false;
		}
		$.ajax({
			url:'<?php print site_url('auth/sendcode') ?>',
			data:'username='+phone,
			dataType:'json',
			type:'POST',
			success:function(json){
				if(json.status==0){
					alert('发送成功，请查收！');
				}else{
					alert(json.error);
				}
			}
		});
	}
</script>