<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/*
 * Output a configuration checking page body
 */

if (!defined('XLITE_INSTALL_MODE')) {
    die('Incorrect call of the script. Stopping.');
}

?>

<div class="requirements-report">

<div class="requirements-list">

<?php

$reqsNotes = array();

// Go through steps list...
foreach ($steps as $stepData) {
?>

    <div class="section-title"><?php echo $stepData['title']; ?></div>
    <div class="section-requirements">

<?php

    // Go through requirements list of current step...
    foreach ($stepData['requirements'] as $reqName) {

        $reqData = $requirements[$reqName];

        $errorsFound = ($errorsFound || (!$reqData['status'] && $reqData['critical']));
        $warningsFound = ($warningsFound || (!$reqData['status'] && !$reqData['critical']));

?>

    <div class="list-row">
        <div class="field-left"><?php echo $reqData['title']; ?> ... <?php echo $reqData['value']; ?></div>
        <div class="field-right">
<?php

        echo isset($reqData['skipped']) ? status_skipped() : status($reqData['status'], $reqName);

        if (!$reqData['status']) {

            if (isHardError($reqName)) {
                ga_event('error', 'reqs', $reqName);

            } else {
                ga_event('warning', 'reqs', $reqName);
            }
?>

            <img id="failed-image-<?php echo $reqName; ?>" class="link-expanded" style="display: none;" src="<?php echo $skinsDir; ?>images/arrow_red.png" alt="" />

<?php
        }
?>
        </div>
    </div>

<?php

        if ($reqName === 'file_permissions') {
            $labelText = $reqData['description'];
        } else {
            $label     = $reqName . '.label_message';
            $labelText = xtr($label, $reqData['messageData']);
            $labelText = $labelText === $label ? null : $labelText;
        }

        $kbLabel = $reqName . '.kb_message';
        $kbNote = xtr($kbLabel, $reqData['messageData']);
        $kbNote = $kbNote === $kbLabel ? '' : $kbNote;

        if ($labelText !== null) {
            $reqsNotes[] = array(
                'reqname' => $reqName,
                'title'   => $stepData['error_msg'],
                'text'    => $labelText,
                'kb_note' => $kbNote,
            );
        }

    } // foreach ($stepData['requirements']...
?>
    </div>
<?php
} // foreach ($steps...

?>


</div>

<div class="requirements-notes">

<div id="headerElement"></div>

<div id="status-report" class="status-report-box <?= $errorsFound ? 'danger' : 'warning'?> " style="display: none;">

    <div id="status-report-detailsElement"></div>

    <div id="detailsElement"></div>

    <div class="status-report-box-text">
        <?php echo xtr('requirements_failed_text'); ?>
    </div>

    <div class="buttons">

        <input id="re-check-button" name="try_again" type="button" class="btn btn-lg" value="<?php echo xtr('Re-check'); ?>" onclick="javascript:document.ifrm.go_back.value='2'; document.ifrm.current.value='2'; ga('send', 'event', 'button', 'click', 'try'); document.ifrm.submit();" />

        <input type="button" class="btn btn-lg btn-warning" value="<?php echo xtr('Send a report'); ?>" onclick="javascript: document.getElementById('report-layer').style.display = 'block'; ga('send', 'event', 'button', 'click', 'send report popup');" />

    </div>
</div>

<?php

x_display_help_block();

foreach ($reqsNotes as $reqNote) {

?>

    <div id="<?php echo $reqNote['reqname']; ?>" style="display: none">
        <div id="<?php echo $reqNote['reqname']; ?>-error-title"><div class="error-title <?php echo $reqNote['reqname']; ?>"><?php echo $reqNote['title']; ?></div></div>
        <div id="<?php echo $reqNote['reqname']; ?>-error-text">
            <div class="error-text <?php echo $reqNote['reqname']; ?>"><?php echo $reqNote['text']; ?></div>
            <?php if($reqNote['kb_note']): ?>
            <div class="error-text kb-note"><?php echo $reqNote['kb_note']; ?></div>
            <?php endif; ?>
        </div>
    </div>

<?php

}

?>

<div class="requirements-success" style="display: none;" id="test_passed_icon">
   <img class="requirements-success-image" src="<?php echo $skinsDir; ?>images/passed_icon.png" border="0" alt="" />
   <br />
   <?php echo xtr('Passed'); ?>
</div>

</div>

<div class="clear"></div>

</div>


<script type="text/javascript">
    var first_code = '<?php echo ($first_error) ? $first_error : ''; ?>';
    showDetails(first_code, <?php echo isHardError($first_error) ? 'true' : 'false'; ?>);
</script>

<?php

    if (!$requirements['file_permissions']['status']) {

?>

<P>
<?php $requirements['file_permissions']['description'] ?>
</P>

<?php

    }

	// Save report to file if errors found
	if ($errorsFound || $warningsFound) {

?>

        <script type="text/javascript">visibleBox("status-report", true);</script>

<?php

	}

    if (false && !$errorsFound && $warningsFound) {

?>

<div class="requirements-warning-text"><?php echo xtr('requirement_warning_text'); ?></div>

<span class="checkbox-field">
    <input type="checkbox" id="continue" onclick="javascript: setNextButtonDisabled(!this.checked);" />
    <label for="continue"><?php echo xtr('Yes, I want to continue the installation.'); ?></label>
</span>

<?php
    }
?>
