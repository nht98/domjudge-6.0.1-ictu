<?php
/**
 * View/download problem texts and sample testcases
 *
 * Part of the DOMjudge Programming Contest Jury System and licenced
 * under the GNU GPL. See README and COPYING for details.
 */

require('init.php');

$title = 'Nộp bài';
require(LIBWWWDIR . '/header.php');

echo "<pre>\n\n\n</pre>";

$fdata = calcFreezeData($cdata);
if (! $fdata['started'] && ! checkrole('jury')) {
    echo '<div class="container submitform"><div class="alert alert-danger" role="alert">Contest has not yet started - cannot submit.</div></div>';
    require(LIBWWWDIR . '/footer.php');
    exit;
}

$langdata = $DB->q('KEYTABLE SELECT langid AS ARRAYKEY, name, extensions, require_entry_point, entry_point_description
                    FROM language WHERE allow_submit = 1');

$probdata = $DB->q('TABLE SELECT probid, shortname, name FROM problem
                    INNER JOIN contestproblem USING (probid)
                    WHERE cid = %i AND allow_submit = 1
                    ORDER BY shortname', $cid);

print "<script>";
putgetMainExtension($langdata);
print "</script>";

$maxfiles = dbconfig_get('sourcefiles_limit', 100);

$probs = array();
$probs[''] = 'Chọn bài nộp';
foreach ($probdata as $probinfo) {
    $probs[$probinfo['probid']]=$probinfo['shortname'] . ' - ' .$probinfo['name'];
}

$langs = array();
$langs[''] = 'Chọn ngôn ngữ';
foreach ($langdata as $langid => $langdata) {
    $langs[$langid] = $langdata['name'];
}

?>
<div class="container submitform">
<form action="upload.php" method="post" enctype="multipart/form-data" onsubmit="return checkUploadForm();">

  <div class="form-group">
    <label for="maincode">Mã nguồn:</label>
    <input type="file" class="form-control-file" name="code[]" id="maincode" required <?=($maxfiles > 1 ? 'multiple': '')?> />
  </div>
 
  <div class="form-group">
    <label for="probid">Bài tập:</label>
    <select class="custom-select" name="probid" id="probid" required>
<?php
    foreach ($probs as $probid => $probname) {
        print '      <option value="' .specialchars($probid). '">' . specialchars($probname) . "</option>\n";
    }
?>
    </select>
  </div>
  <div class="form-group">
    <label for="langid">Ngôn ngữ:</label>
    <select class="custom-select" name="langid" id="langid" required>
<?php
    foreach ($langs as $langid => $langname) {
        print '      <option value="' .specialchars($langid). '">' . specialchars($langname) . "</option>\n";
    }
?>
    </select>
  </div>
  <div class="form-group">
    <label for="entry_point" id="entry_point_text">Entry point:</label>
    <input type="text" class="form-control" name="entry_point" id="entry_point" aria-describedby="entrypointhelp">
    <small id="entrypointhelp" class="form-text text-muted">The entry point for your code.</small>
  </div>
  <input type="submit" name="submit" value="Submit" class="btn btn-primary" />
</form>
    <script type="text/javascript">initFileUploads(<?=$maxfiles?>);</script>
</div>

<?php

require(LIBWWWDIR . '/footer.php');
