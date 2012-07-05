<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Authorization
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: changelog.php 9689 2012-04-19 00:22:35Z richard $
 * @author     John
 */
return array(
  '4.2.3' => array(
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/admin-level/index.tpl' => 'Added support links in admin panel',
  ),
  '4.2.2' => array(
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/admin-level/index.tpl' => 'MooTools 1.4 compatibility',
  ),
  '4.2.0' => array(
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.1.8' => array(
    'Api/Core.php' => 'Refactored deprecated method calls; fixed issue where exception would be throw if resource was null',
    'externals/.htaccess' => 'Updated with far-future expires headers for static resources',
    'Model/DbTable/Levels.php' => 'Fixed typo',
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/admin-level/deleteselected.tpl' => 'Removed deprecated routes',
    'views/scripts/admin-level/index.tpl' => 'Added static base URL for CDN support',
  ),
  '4.1.7' => array(
    'controllers/AdminLevelController.php' => 'Removing deprecated usage of $this->_helper->api()',
    'Model/DbTable/Levels.php' => 'Added utility method to get an associative array of level_id => title',
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.1.3' => array(
    'Model/Level.php' => 'Levels no longer get indexed in search',
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.1.2p1-4.1.3.sql' => 'Added',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.1.2p1' => array(
    'Controller/Action/Helper/RequireAuth.php' => 'Patched vulnerability when setAuthParams() is called previously without clearing it.',
  ),
  '4.1.2' => array(
    'controllers/AdminLevelController.php' => 'Added ability to limit messaging to friends',
    'Form/Admin/Level/Edit.php' => 'Added ability to limit messaging to friends',
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.1.1' => array(
    '/application/languages/en/authorization.csv' => 'Fixed minor admin panel description typos',
    'externals/.htaccess' => 'Added keywords; removed deprecated code',
    'Form/Admin/Level/Edit.php' => 'Changes for storage system modifications',
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.1.0' => array(
    'controllers/AdminLevelController.php' => 'Added notice on form save',
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/admin-level/index.tpl' => 'Adding link from member levels page to filtered list of members in that level',
  ),
  '4.0.5' => array(
    'Controller/Action/Helper/RequireAuth.php' => 'Added support for nested auth actions',
    'Form/Admin/Level/Edit.php' => 'Code formatting',
    'Model/DbTable/Allow.php' => 'Fixes issue with permissions granted to specific resources',
    'Model/DbTable/Permissions.php' => 'Compat for logging modifications',
    'Model/Level.php' => 'Added support for granting authorization to members (for forums)',
    'settings/changelog.php' => 'Added',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.0.4' => array(
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.3-4.0.4.sql' => 'Added to purge levels from search index',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.0.3' => array(
    'Model/Level.php' => 'Code optimizations; fixed nested transaction error with pdo_mysql',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.0.2' => array(
    'controllers/AdminLevelController.php' => 'Various level settings fixes and enhancements',
    'Form/Admin/Level/Abstract.php' => 'Various level settings fixes and enhancements',
    'Form/Admin/Level/Create.php' => 'Various level settings fixes and enhancements; added level type',
    'Form/Admin/Level/Edit.php' => 'Various level settings fixes and enhancements',
    'Model/DbTable/Allow.php' => 'Added auth type for members invited to a group or event',
    'Model/DbTable/Permissions.php' => 'Fixes issue when an empty array is passed to getAllowed()',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.1-4.0.2.sql' => 'Added',
    'settings/my.sql' => 'Various level settings fixes and enhancements',
    'views/scripts/admin-level/index.tpl' => 'Added column for level type; added missing translation',
  ),
  '4.0.1' => array(
    'Form/Admin/Level/Edit.php' => 'Storage quotas are now level-based',
    'settings/manifest.php' => 'Incremented version',
  ),
) ?>