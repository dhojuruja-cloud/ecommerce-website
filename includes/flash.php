<?php
require_once __DIR__ . '/functions.php';

$flash = getFlash();
if ($flash):
    echo '<div class="alert alert-' . htmlspecialchars($flash['type']) . ' mt-3">' . htmlspecialchars($flash['message']) . '</div>';
endif;
