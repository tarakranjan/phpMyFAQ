<?php
/**
 * The page with the ratings of the votings
 *
 * PHP Version 5.3
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at http://mozilla.org/MPL/2.0/.
 *
 * @category  phpMyFAQ
 * @package   Administration
 * @author    Thorsten Rinne <thorsten@phpmyfaq.de>
 * @copyright 2003-2013 phpMyFAQ Team
 * @license   http://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @link      http://www.phpmyfaq.de
 * @since     2003-02-24
 */

if (!defined('IS_VALID_PHPMYFAQ')) {
    $protocol = 'http';
    if (isset($_SERVER['HTTPS']) && strtoupper($_SERVER['HTTPS']) === 'ON'){
        $protocol = 'https';
    }
    header('Location: ' . $protocol . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']));
    exit();
}

if ($permission['viewlog']) {

    $category = new PMF_Category($faqConfig, array(), false);
    $category->setUser($currentAdminUser);
    $category->setGroups($currentAdminGroups);
    $ratings     = new PMF_Rating($faqConfig);
    $ratingdata  = $ratings->getAllRatings();
    $numratings  = count($ratingdata);
    $oldcategory = 0;
?>
        <header>
            <h2><i class="icon-tasks"></i> <?php echo $PMF_LANG["ad_rs"] ?></h2>
        </header>

        <table class="table table-striped">
        <tbody>
<?php
    foreach ($ratingdata as $data) {
        if ($data['category_id'] != $oldcategory) {
?>
            <tr>
                <th colspan="6" style="text-align: left;">
                    <h4><?php echo $category->categoryName[$data['category_id']]['name']; ?></h4>
                </th>
            </tr>
<?php
        }

        $question = PMF_String::htmlspecialchars(trim($data['question']));
        $url      = sprintf(
            '../index.php?action=artikel&amp;cat=%d&amp;id=%d&amp;artlang=%s',
            $data['category_id'],
            $data['id'],
            $data['lang']
        );
?>
            <tr>
                <td><?php echo $data['id']; ?></td>
                <td><?php echo $data['lang']; ?></td>
                <td>
                    <a href="<?php echo $url ?>" title="<?php echo $question; ?>">
                        <?php echo PMF_Utils::makeShorterText($question, 14); ?>
                    </a>
                </td>
                <td style="width: 60px;"><?php echo $data['usr']; ?></td>
                <td style="width: 60px;">
                    <?php
                    if (round($data['num'] * 20) > 75) {
                        $progressBar = 'success';
                    } elseif (round($data['num'] * 20) < 25) {
                        $progressBar = 'danger';
                    } else {
                        $progressBar = 'info';
                    }
                    ?>
                    <div class="progress progress-<?php echo $progressBar ?>" style="width: 50px;">
                        <div class="bar" style="width: <?php echo round($data['num'] * 20); ?>%;"></div>
                    </div>
                </td>
                <td style="width: 60px;"><?php echo round($data['num'] * 20); ?>%</td>
            </tr>
<?php
        $oldcategory = $data['category_id'];
    }
?>
        </tbody>
<?php if ($numratings > 0) { ?>
        <tfoot>
            <tr>
                <td colspan="6">
                    <small>
                    <span style="color: green; font-weight: bold;">
                        <?php echo $PMF_LANG["ad_rs_green"] ?>
                    </span>
                    <?php echo $PMF_LANG["ad_rs_ahtf"] ?>,
                    <span style="color: red; font-weight: bold;">
                        <?php echo $PMF_LANG["ad_rs_red"] ?>
                    </span>
                    <?php echo $PMF_LANG["ad_rs_altt"] ?>
                    </small>
                </td>
            </tr>
        </tfoot>
<?php } else { ?>
        <tfoot>
            <tr>
                <td colspan="6"><?php echo $PMF_LANG["ad_rs_no"] ?></td>
            </tr>
        </tfoot>
<?php } ?>
        </table>
<?php
} else {
    echo $PMF_LANG["err_NotAuth"];
}