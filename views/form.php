<?php
/*
$form_error
If there has been a form error, this field will contain the details

$form_success
If the user was registered successfully, there will contain the 'Thankyou' message


*/
?>
<div class="sreg-format_text-container">
    <?php if (isset($form_error)) { ?>
        <p class="sreg-form-error"><?php echo $form_error; ?></p>
    <?php } ?>
    <?php if (isset($form_success)) { ?>
        <p class="sreg-form-success"><?php echo $form_success; ?></p>
    <?php } ?>
    
    <?php if ($show_form) { ?>
        <form method="post" action="<?php the_permalink(); ?>" id="sreg-form" class="sreg-form">
            <fieldset>
                <legend>Contact Information</legend>
                <?php if ($shopp_account_type == 'wordpress') : ?>
                <p>
                    <label for="sreg-username">Username</label>
                    <input type="text" name="customer[loginname]" id="sreg-username" autocomplete="off" value="<?php echo (isset($_POST['customer']['loginname'])) ? $_POST['customer']['loginname'] : ''; ?>" />
                </p>            
                <?php endif; ?>
                <p>
                    <label for="sreg-email">Email</label>
                    <input type="text" name="customer[email]" id="sreg-email" autocomplete="off" value="<?php echo (isset($_POST['customer']['email'])) ? $_POST['customer']['email'] : ''; ?>" />
                </p>
                <p>
                    <label for="sreg-password">Password</label>
                    <input type="password" name="customer[password]" id="sreg-password" autocomplete="off" value="" />
                </p>
                <p>
                    <label for="sreg-confirm-password">Confirm Password</label>
                    <input type="password" name="customer[confirm-password]" id="sreg-confirm-password" autocomplete="off" value="" />
                </p>            
                <p>
                    <label for="sreg-firstname">Firstname</label>
                    <input type="text" name="customer[firstname]" id="sreg-firstname" autocomplete="off" value="<?php echo (isset($_POST['customer']['firstname'])) ? $_POST['customer']['firstname'] : ''; ?>" />
                </p>
                <p>
                    <label for="sreg-lastname">Lastname</label>
                    <input type="text" name="customer[lastname]" id="sreg-lastname" autocomplete="off" value="<?php echo (isset($_POST['customer']['lastname'])) ? $_POST['customer']['lastname'] : ''; ?>" />
                </p>
                <p>
                    <label for="sreg-phone">Phone</label>
                    <input type="text" name="customer[phone]" id="sreg-phone" autocomplete="off" value="<?php echo (isset($_POST['customer']['phone'])) ? $_POST['customer']['phone'] : ''; ?>" />
                </p> 
                <p>
                    <label for="sreg-company">Company</label>
                    <input type="text" name="customer[company]" id="sreg-company" autocomplete="off" value="<?php echo (isset($_POST['customer']['company'])) ? $_POST['customer']['company'] : ''; ?>" />
                </p> 
                <p>
                    <label for="sreg-marketing">Yes, I would like to receive e-mail updates and special offers!</label>
                    <input type="checkbox" name="customer[marketing]" id="sreg-marketing" value="yes" <?php echo (isset($_POST['customer']['marketing']) && $_POST['customer']['marketing'] == 'yes') ? 'checked="checked"' : ''; ?> />
                </p> 
                
            </fieldset>
            <?php if ($show_billing) : ?>
            <fieldset>
                <legend>Billing Address</legend>
                <p>
                    <label for="sreg-billing-address">Street Address</label>
                    <input type="text" name="billing[address]" id="sreg-billing-address" autocomplete="off" value="<?php echo (isset($_POST['billing']['address'])) ? $_POST['billing']['address'] : ''; ?>" />
                </p>
                <p>
                    <label for="sreg-billing-xaddress">Address Line 2</label>
                    <input type="text" name="billing[xaddress]" id="sreg-billing-xaddress" autocomplete="off" value="<?php echo (isset($_POST['billing']['xaddress'])) ? $_POST['billing']['xaddress'] : ''; ?>" />
                </p>
                <p>
                    <label for="sreg-billing-city">City</label>
                    <input type="text" name="billing[city]" id="sreg-billing-city" autocomplete="off" value="<?php echo (isset($_POST['billing']['city'])) ? $_POST['billing']['city'] : ''; ?>" />
                </p>
                <p>
                    <label for="sreg-billing-state">State / Province</label>
                    <input type="text" name="billing[state]" id="sreg-billing-state" autocomplete="off" value="<?php echo (isset($_POST['billing']['state'])) ? $_POST['billing']['state'] : ''; ?>" />
                </p>
                <p>
                    <label for="sreg-billing-postcode">Postal / Zip Code</label>
                    <input type="text" name="billing[postcode]" id="sreg-billing-postcode" autocomplete="off" value="<?php echo (isset($_POST['billing']['postcode'])) ? $_POST['billing']['postcode'] : ''; ?>" />
                </p>                
                <p>
                    <label for="sreg-billing-country">Country</label>
                    <select name="billing[country]" id="sreg-billing-country">
                    <?php echo $countries_select_html; ?>
                    </select>
                </p>
            </fieldset>
            <?php endif; ?>
            <p>
                <input type="submit" value="Send" />
                <input type="hidden" name="customer[type]" value="Retail" />
            </p>
        </form>
    <?php } ?>
</div>