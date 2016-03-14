<!doctype html>
<html class="no-js" lang="en">

<head>
    <?php $this->view('includes/header') ?>
</head>

<body>

<div id="modal-placeholder"></div>

<div id="app">

    <div id="sidebar">
        <?php $this->view('includes/sidebar') ?>
    </div>

    <?php $this->view('includes/sidebar_toggle') ?>

    <div id="main">

        <?php $this->view('includes/no_js_info') ?>

        <?php echo $content; ?>

    </div>

</div>

<?php $this->view('includes/loader'); ?>

<div id="site-url"><?php echo site_url(); ?></div>

</body>
</html>
