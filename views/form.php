
<div class="sreg-format_text-container">
    <?php if (isset($form_error)) { ?>
        <p class="sreg-form-error"><?php echo $form_error; ?></p>
    <?php } ?>
    <?php if (isset($form_success)) { ?>
        <p class="sreg-form-success"><?php echo $form_success; ?></p>
    <?php } ?>
    <?php if ($show_form) { ?>
        <form method="post" action="<?php the_permalink(); ?>" id="sreg-form" class="sreg-form">
            <?php if ($shopp_account_type == 'wordpress') : ?>
            <p>
                <label for="sreg-username">Username</label>
                <input type="text" name="customer[loginname]" id="sreg-username" value="<?php echo (isset($_POST['customer']['loginname'])) ? $_POST['customer']['loginname'] : ''; ?>" />
            </p>            
            <?php endif; ?>
            <p>
                <label for="sreg-email">Email</label>
                <input type="text" name="customer[email]" id="sreg-email" value="<?php echo (isset($_POST['customer']['email'])) ? $_POST['customer']['email'] : ''; ?>" />
            </p>
            <p>
                <label for="sreg-password">Password</label>
                <input type="password" name="customer[password]" id="sreg-password" value="" />
            </p>
            <p>
                <label for="sreg-confirm-password">Confirm Password</label>
                <input type="password" name="customer[confirm-password]" id="sreg-confirm-password" value="" />
            </p>            
            <p>
                <label for="sreg-firstname">Firstname</label>
                <input type="text" name="customer[firstname]" id="sreg-firstname" value="<?php echo (isset($_POST['customer']['firstname'])) ? $_POST['customer']['firstname'] : ''; ?>" />
            </p>
            <p>
                <label for="sreg-lastname">Lastname</label>
                <input type="text" name="customer[lastname]" id="sreg-lastname" value="<?php echo (isset($_POST['customer']['lastname'])) ? $_POST['customer']['lastname'] : ''; ?>" />
            </p>
            <p>
                <label for="sreg-phone">Phone</label>
                <input type="text" name="customer[phone]" id="sreg-phone" value="<?php echo (isset($_POST['customer']['phone'])) ? $_POST['customer']['phone'] : ''; ?>" />
            </p> 
            <p>
                <label for="sreg-company">Company</label>
                <input type="text" name="customer[company]" id="sreg-company" value="<?php echo (isset($_POST['customer']['company'])) ? $_POST['customer']['company'] : ''; ?>" />
            </p> 

            <p>
                <input type="submit" value="Send" />
                <input type="hidden" name="customer[type]" value="Retail" />
            </p>
        </form>
    <?php } ?>
</div>