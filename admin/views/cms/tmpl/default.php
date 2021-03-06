<?php
/**
 * @copyright	Copyright (C) 2013 - 2014 Cms2Cms. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if ( !defined('CMS2CMS_VERSION') ) {
    die();
}
// Add Javascript
// Add stylesap
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_cms2cmsautomatedwordpresstojmigration/css/cms2cms.css');
$document->addScript('http://code.jquery.com/jquery-1.9.0.min.js');
$document->addScript('components/com_cms2cmsautomatedwordpresstojmigration/js/cms2cms.js');
$document->addScript('components/com_cms2cmsautomatedwordpresstojmigration/js/jsonp.js');
//

$dataProvider = new CmsPluginData();
$viewProvider = new CmsPluginView();

$nonce = $_REQUEST['_wpnonce'];
if ( $viewProvider->verifyFormTempKey($nonce, 'cms2cms_logout')
    && $_POST['cms2cms_logout'] == 1
) {
    $dataProvider->clearOptions();
}

$cms2cms_access_key = $dataProvider->getOption('cms2cms-key');
$cms2cms_is_activated =  $dataProvider->isActivated();

$cms2cms_target_url = $dataProvider->getSiteUrl();
$cms2cms_bridge_url = $dataProvider->getBridgeUrl();

$cms2cms_authentication = $dataProvider->getAuthData();
$cms2cms_download_bridge = $viewProvider->getDownLoadBridgeUrl($cms2cms_authentication);

$cms2cms_ajax_nonce = $viewProvider->getFormTempKey('cms2cms-ajax-security-check');
?>


<div class="wrap">

<div class="cms2cms-plugin">

    <div id="icon-plugins" class="icon32"><br></div>
    <h2><?php echo $viewProvider->getPluginNameLong() ?></h2>

    <?php if ($cms2cms_is_activated) { ?>
        <div class="cms2cms-message">
                <span>
                    <?php echo sprintf(
                        $viewProvider->__('You are logged in CMS2CMS as %s', 'cms2cms-migration'),
                        $dataProvider->getOption('cms2cms-login')
                    ); ?>
                </span>
            <div class="cms2cms-logout">
                <form action="" method="post">
                    <input type="hidden" name="task" value="clear-auth"/>
                    <input type="hidden" name="cms2cms_logout" value="1"/>
                    <input type="hidden" name="_wpnonce" value="<?php echo $viewProvider->getFormTempKey('cms2cms_logout') ?>"/>
                    <button class="button">
                        &times;
                        <?php $viewProvider->_e('Logout', 'cms2cms-migration');?>
                    </button>
                </form>
            </div>
        </div>
    <?php } ?>

    <ol id="cms2cms_accordeon">
        <?php

        $cms2cms_step_counter = 1;

        if ( !$cms2cms_is_activated ) { ?>
            <li id="cms2cms_accordeon_item_id_<?php echo $cms2cms_step_counter++;?>" class="cms2cms_accordeon_item cms2cms_accordeon_item_register">
                <h3>
                    <?php $viewProvider->_e('Sign In', 'cms2cms-migration'); ?>
                    <span class="spinner"></span>
                </h3>
                <form action="<?php echo $viewProvider->getRegisterUrl() ?>"
                      callback="callback_auth"
                      validate="auth_check_password"
                      class="step_form"
                      id="cms2cms_form_register">

                    <h3 class="nav-tab-wrapper">
                        <a href="<?php echo $viewProvider->getRegisterUrl() ?>" class="nav-tab nav-tab-active" change_li_to=''>
                            <?php $viewProvider->_e('Register CMS2CMS Account', 'cms2cms-migration'); ?>
                        </a>
                        <a href="<?php echo $viewProvider->getLoginUrl() ?>" class="nav-tab">
                            <?php $viewProvider->_e('Login', 'cms2cms-migration'); ?>
                        </a>
                        <a href="<?php echo $viewProvider->getForgotPasswordUrl() ?>" class="nav-tab cms2cms-real-link">
                            <?php $viewProvider->_e('Forgot password?', 'cms2cms-migration'); ?>
                        </a>
                    </h3>

                    <table class="form-table">
                        <tbody>
                        <tr valign="top">
                            <th scope="row">
                                <label for="cms2cms-user-email"><?php $viewProvider->_e('Email:', 'cms2cms-migration');?></label>
                            </th>
                            <td>
                                <input type="text" id="cms2cms-user-email" name="email" value="<?php echo $dataProvider->getUserEmail() ?>" class="regular-text"/>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="cms2cms-user-password"><?php $viewProvider->_e('Password:', 'cms2cms-migration'); ?></label>
                            </th>
                            <td>
                                <input type="password" id="cms2cms-user-password" name="password" value="" class="regular-text"/>
                                <p class="description for__cms2cms_accordeon_item_register">
                                    <?php $viewProvider->_e('Minimum 6 characters', 'cms2cms-migration'); ?>
                                </p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div>
                        <input type="hidden" id="cms2cms-site-url" name="siteUrl" value="<?php echo $cms2cms_target_url; ?>"/>
                        <input type="hidden" id="cms2cms-bridge-url" name="sourceBridgePath" value="<?php echo $cms2cms_bridge_url; ?>"/>
                        <input type="hidden" id="cms2cms-access-key" name="accessKey" value="<?php echo $cms2cms_access_key; ?>"/>
                        <input type="hidden" name="termsOfService" value="1">
                        <input type="hidden" name="jklsdfl" value="">
                        <div class="error_message"></div>

                        <button type="submit" class="button button-primary button-large">
                            <?php $viewProvider->_e('Continue', 'cms2cms-migration'); ?>
                        </button>
                    </div>
                </form>
            </li>

        <?php } /* cms2cms_is_activated */ ?>

        <li id="cms2cms_accordeon_item_id_<?php echo $cms2cms_step_counter++;?>" class="cms2cms_accordeon_item">
            <h3>
                <?php echo sprintf(
                    $viewProvider->__('Connect %s', 'cms2cms-migration'),
                    $viewProvider->getPluginSourceName()
                ); ?>
                <span class="spinner"></span>
            </h3>
            <form action="<?php echo $viewProvider->getVerifyUrl() ?>"
                  callback="callback_verify"
                  validate="verify"
                  class="step_form"
                  id="cms2cms_form_verify">
                <ol>
                    <li>
                        <a href="<?php echo $cms2cms_download_bridge ?>" class="button">
                            <?php echo $viewProvider->__('Download the Bridge file', 'cms2cms-migration'); ?>
                        </a>
                    </li>
                    <li>
                        <?php $viewProvider->_e('Unzip it', 'cms2cms-migration');?>
                        <p class="description">
                            <?php $viewProvider->_e('Find the cms2cms.zip on your computer, right-click it and select Extract in the menu.', 'cms2cms-migration'); ?>
                        </p>
                    </li>
                    <li>
                        <?php echo sprintf(
                            $viewProvider->__('Upload to the root folder on your %s website.', 'cms2cms-migration'),
                            $viewProvider->getPluginSourceName()
                        ); ?>
                        <a href="<?php echo $viewProvider->getVideoLink() ?>" target="_blank"><?php $viewProvider->_e('Watch the video', 'cms2cms-migration');?></a>
                    </li>
                    <li>
                        <?php echo sprintf(
                            $viewProvider->__('Specify %s website URL', 'cms2cms-migration'),
                            $viewProvider->getPluginSourceName()
                        ); ?>
                        <br/>
                        <input type="text" name="sourceUrl" value="" class="regular-text" placeholder="<?php
                        echo sprintf(
                            $viewProvider->__('http://your_%s_website.com/', 'cms2cms-migration'),
                            strtolower($viewProvider->getPluginSourceType())
                        );
                        ?>"/>
                        <input type="hidden" name="targetType" value="<?php echo $viewProvider->getPluginTargetType(); ?>" />
                        <input type="hidden" name="targetUrl" value="<?php echo $cms2cms_target_url;?>" />
                        <input type="hidden" name="sourceType" value="<?php echo $viewProvider->getPluginSourceType(); ?>" />
                        <input type="hidden" name="sourceBridgePath" value="<?php echo $cms2cms_bridge_url;?>" />
                    </li>
                </ol>
                <div class="error_message"></div>
                <button type="submit" class="button button-primary button-large">
                    <?php $viewProvider->_e('Verify connection', 'cms2cms-migration'); ?>
                </button>
            </form>
        </li>

        <li id="cms2cms_accordeon_item_id_<?php echo $cms2cms_step_counter++;?>" class="cms2cms_accordeon_item">
            <h3>
                <?php $viewProvider->_e('Configure and Start Migration', 'cms2cms-migration'); ?>
                <span class="spinner"></span>
            </h3>
            <form action="<?php echo $viewProvider->getWizardUrl(); ?>"
                  class="cms2cms_step_migration_run step_form"
                  method="post"
                  id="cms2cms_form_run">
                <?php $viewProvider->_e("You'll be redirected to CMS2CMS application website in order to select your migration preferences and complete your migration.", 'cms2cms-migration'); ?>
                <input type="hidden" name="sourceUrl" value="">
                <input type="hidden" name="sourceType" value="">
                <input type="hidden" name="targetUrl" value="">
                <input type="hidden" name="targetType" value="">
                <input type="hidden" name="migrationHash" value="">
                <input type="hidden" name="sourceBridgePath" value="<?php echo $cms2cms_bridge_url; ?>"/>
                <div class="error_message"></div>
                <button type="submit" class="button button-primary button-large">
                    <?php $viewProvider->_e('Start migration', 'cms2cms-migration'); ?>
                </button>
            </form>
        </li>
    </ol>

</div> <!-- /plugin -->

<div id="cms2cms-description">
    <p>
        <?php
        $sourceName = $viewProvider->getPluginSourceType();
        $targetName = $viewProvider->getPluginTargetType();
        $viewProvider->_e(
            'CMS2CMS.com is the one-of-its kind tool for fast, accurate and trouble-free website migration from '. $sourceName .' to '. $targetName .'. Just a few mouse clicks - and your '. $sourceName .' articles, categories, images, users, comments, internal links etc are safely delivered to the new '. $targetName .' website.',
            'cms2cms-migration'
        );
        ?>
    </p>
    <p>
        <a href="http://www.cms2cms.com/how-it-works/" class="button" target="_blank">
            <?php $viewProvider->_e('See How it Works', 'cms2cms-migration'); ?>
        </a>
    </p>
    <p>
        <?php
        $viewProvider->_e('Take a quick demo tour to get the idea about how your migration will be handled.', 'cms2cms-migration');
        ?>
    </p>
</div>

</div> <!-- /wrap -->
