<?php
header('Location: login.php' . (isset($_GET['erro']) ? '?erro=login' : ''));
exit;