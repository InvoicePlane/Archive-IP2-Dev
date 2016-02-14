<?php if (function_exists('validation_errors')) {
    if (validation_errors()) {
        echo validation_errors('<div class="alert alert-danger">', '</div>');
    }
} ?>

<?php
$alerts = get_alerts();
if ($alerts) {
    foreach ($alerts as $alert) {
        echo '<div class="alert alert-' . $alert['type'] . '">' . $alert['message'] . '</div>';
    }
    clear_alerts();
}
?>