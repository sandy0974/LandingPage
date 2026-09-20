<?php
// Buka sekali di browser, salin hasil hash ke SQL, lalu HAPUS file ini.
$password = 'GANTI_PASSWORD_ANDA';
echo '<pre>'.htmlspecialchars(password_hash($password, PASSWORD_DEFAULT)).'</pre>';
