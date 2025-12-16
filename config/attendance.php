<?php

return [
    /**
     * When true, QR scans from residents outside the target audience (Purok)
     * are blocked with a 403. Manual/guest entries are still allowed but will
     * return a warning message.
     */
    'strict_audience' => env('ATTENDANCE_STRICT_AUDIENCE', false),
];


