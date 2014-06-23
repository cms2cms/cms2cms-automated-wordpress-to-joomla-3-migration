<?php
/**
 * @copyright   Copyright (C) 2013 - 2014 Cms2Cms. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CmsPluginData
{

    const CMS2CMS_OPTION_TABLE = 'cms2cms_options';

    public function getUserEmail()
    {
        $user = JFactory::getUser();
        return $user->email;
    }

    public function getSiteUrl()
    {
        return JURI::root();
    }

    public function getOption($name)
    {
        $this->install();
        $sql = sprintf(
            "
                SELECT `option_value`
                FROM `#__%s`
                WHERE `option_name` = '%s'
                LIMIT 1
            ",
            self::CMS2CMS_OPTION_TABLE,
            $name
        );

        $db = JFactory::getDBO();
        $db->setQuery($sql);
        $value = $db->loadResult();

        return $value;
    }
    public function setOption($name, $value)
    {
        $this->install();
        $sql = sprintf(
            "
                INSERT INTO #__%s(`option_name`, `option_value`)
                VALUES ('%s', '%s')
            ",
            self::CMS2CMS_OPTION_TABLE,
            $name,
            $value
        );

        $db = JFactory::getDBO();
        $db->setQuery($sql);
        $db->query();

        return $db->insertid();
    }

    public function deleteOption($name)
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $conditions = array(
            $db->quoteName('option_name') . ' = "'.$name.'"',
        );

        $query->delete($db->quoteName('#__'.self::CMS2CMS_OPTION_TABLE));
        $query->where($conditions);

        $db->setQuery($query);
        $db->query();
    }
    public function getFrontUrl()
    {
        return $this->getSiteUrl() . 'components/com_cms2cmsautomatedwordpresstojmigration/';
    }


    public function getBridgeUrl()
    {
        $cms2cms_bridge_url = str_replace($this->getSiteUrl(), '', $this->getFrontUrl());
        $cms2cms_bridge_url = '/' . trim($cms2cms_bridge_url, DIRECTORY_SEPARATOR);

        return $cms2cms_bridge_url;
    }

    public function getAuthData()
    {
        $cms2cms_access_login = $this->getOption('cms2cms-login');
        $cms2cms_access_key = $this->getOption('cms2cms-key');

        return array(
            'email' => $cms2cms_access_login,
            'accessKey' => $cms2cms_access_key
        );
    }

    public function isActivated()
    {
        $cms2cms_access_key = $this->getOption('cms2cms-key');

        return ($cms2cms_access_key != false);
    }

    public function install()
    {
        $sql = sprintf(
            "
                CREATE TABLE IF NOT EXISTS `#__%s` (
                    `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                    `option_name` VARCHAR(64) DEFAULT '' NOT NULL,
                    `option_value` VARCHAR(64) DEFAULT '' NOT NULL,
                    UNIQUE KEY `id` (`id`)
                )
            ",
            self::CMS2CMS_OPTION_TABLE
        );

        $db = JFactory::getDBO();
        $db->setQuery($sql);
        $db->query();
    }
    public function getOptions()
    {
        $key = $this->getOption('cms2cms-key');
        $login = $this->getOption('cms2cms-login');

        $response = 0;

        if ( $key && $login ) {
            $response = array(
                'email' => $login,
                'accessKey' => $key,
            );
        }

        return $response;
    }

    public function saveOptions()
    {
        $key = substr( $_POST['accessKey'], 0, 64 );
        $login = $_POST['login']; //todo filter email

        $cms2cms_site_url = $this->getSiteUrl();
        $bridge_depth = str_replace($cms2cms_site_url, '', $this->getFrontUrl());
        $bridge_depth = trim($bridge_depth, DIRECTORY_SEPARATOR);
        $bridge_depth = explode(DIRECTORY_SEPARATOR, $bridge_depth);
        $bridge_depth = count( $bridge_depth );

        $response = array(
            'errors' => _('Provided credentials are not correct: ' . $key . ' = ' . $login )
        );

        if ( $key && $login ) {
            $this->deleteOption('cms2cms-key');
            $this->setOption('cms2cms-key', $key);

            $this->deleteOption('cms2cms-login');
            $this->setOption('cms2cms-login', $login);

            $this->deleteOption('cms2cms-depth');
            $this->setOption('cms2cms-depth', $bridge_depth);

            $response = array(
                'success' => true
            );
        }

        return $response;
    }
    public function clearOptions()
    {
        $this->deleteOption('cms2cms-login');
        $this->deleteOption('cms2cms-key');
        $this->deleteOption('cms2cms-depth');
    }


}
