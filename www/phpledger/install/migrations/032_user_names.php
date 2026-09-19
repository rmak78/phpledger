<?php
declare(strict_types=1);

// Owner request, 19 September 2026: the installing owner chooses a username as well as an
// email address and may sign in with either. Usernames are optional, so existing accounts
// keep signing in by email. They are stored in lowercase ASCII and compared exactly.
return [
    'ALTER TABLE pl_users ADD COLUMN username VARCHAR(60) CHARACTER SET ascii COLLATE ascii_bin NULL AFTER email, ADD UNIQUE KEY uq_users_username (username)',
];
