<h1><?php echo $filename ?></h1>
<?php foreach ($options as $key => $name) { if (empty($$key)) continue; ?>
  <h2 id="<?php echo $key?>"><?php echo $name ?> <span><?php echo $$key ?></span></h2>
<?php } ?>
<hr />

<?php
if ($output instanceof sfOutputEscaper) {
  $output = $output->getRawValue();
} elseif (method_exists('sfOutputEscaper', 'unescape')) {
  $output = sfOutputEscaper::unescape($output); 
}?>
<div id="body">
  <?php echo base64_decode($output) ?>
</div>

<hr />
<?php echo link_to('Back to emails list', sfContext::getInstance()->getModuleName() . '/index') ?>