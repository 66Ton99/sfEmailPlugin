<h1><?php echo $filename ?></h1>
<?php foreach ($options as $key => $name) { if (!empty($$key)) ?>
  <h2 id="<?php echo $key?>"><?php echo $name ?> <span><?php echo $$key ?></span></h2>
<?php } ?>
<hr />

<div id="body">
  <pre>
    <?php
    foreach ($messages as $type => $content) {
      require __DIR__ . '/contentSuccess.php';
    }

    ?>



  </pre>
</div>

  <?php
  foreach ($types as $type) {
    echo link_to($type, '@sfEmail_showFile?filename=' . $filename . '&type=' . $type);
  }
  ?>

<hr />
<?php echo link_to('Back to emails list', '@sfEmail') ?>