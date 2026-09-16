<?php
declare(strict_types=1);

// Complete the legacy event-status widening if 027 was interrupted after its
// structural changes. Keeping the 0.4 values avoids truncating old source
// history while allowing the current posted/corrected/reversed states.
return [
    "ALTER TABLE pl_ar_document_events MODIFY from_status ENUM('draft','sent','accepted','declined','expired','cancelled','posted','corrected','reversed') NULL, MODIFY to_status ENUM('draft','sent','accepted','declined','expired','cancelled','posted','corrected','reversed') NOT NULL",
];
