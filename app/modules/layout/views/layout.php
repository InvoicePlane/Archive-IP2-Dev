<!doctype html>
<html class="no-js" lang="en">

<head>
    <?php $this->view('includes/header') ?>
</head>

<body>

<noscript>
    <div class="alert alert-danger no-margin"><?php echo lang('please_enable_js'); ?></div>
</noscript>

<div id="modal-placeholder"></div>

<div id="app">

    <div id="sidebar">
        <?php $this->view('includes/sidebar') ?>
    </div>

    <div class="sidebar-toggle-wrapper hidden-lg-up">
        <a href="#" class="sidebar-toggle">
            <i class="cursor-pointer fa fa-bars fa-margin-right hidden-lg-up"></i> <?php echo lang('menu'); ?>
        </a>
    </div>

    <div id="main">

        <?php echo $content; ?>

    </div>

</div>

<?php $this->view('includes/loader') ?>

</body>
</html>
