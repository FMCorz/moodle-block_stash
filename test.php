<?php

require(__DIR__ . '/../../config.php');

$id = optional_param('id', 0, PARAM_INT);
$stashid = optional_param('stashid', 0, PARAM_INT);

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/block/stash/test.php');

$item = $id ? new block_stash\item($id) : null;

if (!$item && !$stashid) {
    $stash = new block_stash\stash(null, (object) ['courseid' => 2]);
    $stash->create();
} else if ($item) {
    $stash = new block_stash\stash($item->get_stashid());
} else {
    $stash = new block_stash\stash($stashid);
}

$form = new block_stash\form\item(null, ['persistent' => $item, 'stash' => $stash]);

if ($data = $form->get_data()) {
    $item = new block_stash\item(null, $data);
    $item->create();
}

echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();
