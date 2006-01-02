<?php
/**
* $Id: ajax.group_list.php,v 1.1 2006-01-02 16:36:50 b33blebr0x Exp $
*
* AJAX: lists all registered users
*
* @author       Lars Tiedemann <larstiedemann@yahoo.de>
* @since        2005-12-15
* @copyright    (c) 2005 phpMyFAQ Team
* 
* The contents of this file are subject to the Mozilla Public License
* Version 1.1 (the "License"); you may not use this file except in
* compliance with the License. You may obtain a copy of the License at
* http://www.mozilla.org/MPL/
* 
* Software distributed under the License is distributed on an "AS IS"
* basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
* License for the specific language governing rights and limitations
* under the License.
*/

if (!defined('IS_VALID_PHPMYFAQ_ADMIN')) {
    header('Location: http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME']));
    exit();
}

@header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
@header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
@header("Cache-Control: no-store, no-cache, must-revalidate");
@header("Cache-Control: post-check=0, pre-check=0", false);
@header("Pragma: no-cache");
@header("Content-type: text/xml");
@header("Vary: Negotiate,Accept");

require_once(PMF_ROOT_DIR.'/inc/PMF_User/User.php');

$user = new PMF_User();
$groupList = is_a($user->perm, "PMF_PermMedium") ? $user->perm->getAllGroups() : array();
$data = array(
    'name' => "Name:",
    'description' => "Description:",
    'auto_join' => "Auto-join:",
);

ob_clean();
?>
<xml>
    <grouplist>
        <select_class>ad_select_group</select_class>
<?php
foreach ($groupList as $group_id) {
    //$groupData = $user->perm->getGroupData($group_id);
?>
        <group id="<?php print $groupData['group_id']; ?>">
            <name><?php print $groupData['name']; ?></name>
            <description><?php print $groupData['description']; ?></description>
            <auto_join><?php print $groupData['auto_join']; ?></auto_join>
            <group_rights>
<?php
    $perm = $user->perm;
    $all_rights = $perm->getAllRights();
    foreach ($all_rights as $right_id) {
        $right_data = $perm->getRightData($right_id);
        // right is not for users!
        if (!$right_data['for_groups'])
            continue;
        $isGroupRight = $perm->checkGroupRight($group_id, $right_id) ? '1' : '0';
?>
                <right id="<?php print $right_id; ?>">
                    <name><?php print isset($PMF_LANG['rightsLanguage'][$right_data['name']]) ? $PMF_LANG['rightsLanguage'][$right_data['name']] : $right_data['name']; ?></name>
                    <description><?php print $right_data['description']; ?></description>
                    <is_user_right><?php print $isUserRight; ?></is_user_right>
                </right>
<?php
    } /* end foreach ($all_rights) */
?>
            </group_rights>
        </group>
<?php
} /* end foreach ($userList) */
?>
    </grouplist>
</xml>
