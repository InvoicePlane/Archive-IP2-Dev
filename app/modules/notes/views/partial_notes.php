<?php foreach ($notes as $note) : ?>
    <div class="card card-sm">
        <div class="card-block">
            <?php echo nl2br($note->note); ?>
            <br>
            <small class="text-muted">
                <?php
                echo '<a href="' . site_url('users/form/' . $note->user_id) . '" >' . $note->user . '</a>';
                echo ' - ';
                echo datetime_from_mysql($note->date_created, true);

                if (check_permission('notes_delete')) {
                    /*
                     * If the user id and note author id are the same check if the user can delete his own notes
                     * If the user id and note author id are not the same check if the user can delete all notes
                     */
                    if (($this->session->user['id'] != $note->user_id && check_permission('notes_delete_all'))
                        || ($this->session->user['id'] === $note->user_id && check_permission('notes_delete_own'))) {
                        echo '&nbsp;- <a href="#" class="delete-note" data-note-id="' . $note->id . '" data-user-id="' . $this->session->user['id'] . '">';
                        echo '<i class="fa fa-trash text-danger"></i></a>';
                    }
                }
                ?>
            </small>
        </div>
    </div>
<?php endforeach; ?>