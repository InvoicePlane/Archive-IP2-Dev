<!doctype html>
<html class="no-js" lang="en">

<head>
    <title>InvoicePlane</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="robots" content="NOINDEX,NOFOLLOW">

    <link rel="icon" type="image/png" href="<?php echo THEME_URL; ?>img/favicon.png">

    <link rel="stylesheet" href="<?php echo THEME_URL; ?>css/app.min.css">

    <script src="<?php echo base_url(); ?>themes/core/js/dependencies.min.js"></script>
    <script src="<?php echo base_url(); ?>themes/core/js/app.min.js"></script>
</head>

<body>

<div id="app">

    <div id="main-base">

        <?php $this->view('includes/no_js_info') ?>

        <?php echo $content; ?>

    </div>

</div>

</body>
</html>
