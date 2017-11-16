<?php

include 'setup.php';

?>
<html lang="en">
<head>
    <meta charset="utf-8">

    <title>localhost development</title>

    <link rel="stylesheet" href="php-setup-guide/assets/style.css">
    <script type="text/javascript">
      var projects = <?php echo $projects; ?>;
      var vhosts   = <?php echo $vhosts; ?>;
    </script>
</head>

<body>

<h1>Local Development</h1>

<div id="projects">
</div>
<div id="project-template">
    <div class="project">
        <a href="http://title.test">
            <span></span><strong>Title</strong>
        </a>
    </div>
</div>

<div class="clear"></div>

<h1>Your PHP Info</h1>

<?php phpinfo(); ?>

<script src="php-setup-guide/assets/jquery-3.2.1.min.js"></script>
<script src="php-setup-guide/assets/script.js"></script>
</body>
</html>