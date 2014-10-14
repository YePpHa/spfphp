<?php

// Create a template
$template = new SPFTemplate();

// Create an instance of SPF
$spf = new SPF($template);

$template->start();

?>
<!doctype HTML>
<html>
  <head>
    <title><?php $template->insertFlag("title"); ?></title>
    <?php $template->insertFlag("stylesheet"); ?>
  </head>
  <body>
    <div class="nav">
      I am your navigation. Mu ha hah. <a href="demo.php" class="spf-link">#1</a>, <a href="demo2.php" class="spf-link">#2</a>
    </div>
    <div id="content">
      <?php $template->insertFlag("#content"); ?>
    </div>
    <script src="../vendor/spf.js" name="spf"></script>
    <script>
      spf.init();
    </script>
    <?php $template->insertFlag("javascript"); ?>
  </body>
</html>
<?php

$template->stop();

?>