<?php use_helper('sfEmail'); ?>

<?php if (isset($files)) $i = 0; foreach ($files as $f): ?>
<?php echo link_to_file($f, null, array('id' => 'email_' . ++$i)) ?><br />
<?php endforeach ?>