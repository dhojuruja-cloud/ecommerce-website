<?php
require_once __DIR__ . '/functions.php';
if (!isAdmin()) {
    setFlash('error', 'Access denied.');
    redirect('login.php');
}
