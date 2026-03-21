<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Sr. No. display offset
    |--------------------------------------------------------------------------
    |
    | Shown on PDF/preview as: pad8(stored_marksheet_serial + offset).
    | New rows get the next global serial. Migration 2026_03_19_000004 renumbers
    | existing rows to 1..N in id order so the first certificate (lowest id) is
    | serial 1 → 00000151 with the default offset below.
    |
    | Default 150 → stored serial 1 displays as 00000151, 2 as 00000152, etc.
    | Set MARKSHEET_SERIAL_DISPLAY_OFFSET=0 in .env to show raw stored numbers only.
    |
    */
    'serial_display_offset' => (int) env('MARKSHEET_SERIAL_DISPLAY_OFFSET', 150),
];
