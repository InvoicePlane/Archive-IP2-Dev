<?php foreach ($client_notes as $client_note) : ?>
    <div class="card card-sm">
        <div class="card-block">
            <?php echo nl2br($client_note->note); ?>
        </div>
        <div class="card-footer text-right small">
            <?php echo date_from_mysql($client_note->date_created, true); ?>
        </div>
    </div>
<?php endforeach; ?>