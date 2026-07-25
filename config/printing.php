<?php

return [
    'driver' => env('PRINTING_DRIVER', 'raw'),

    'printer_name' => env('PRINTING_PRINTER_NAME', 'Microsoft Print to PDF'),

    'host' => env('PRINTING_HOST', '192.168.1.100'),

    'port' => env('PRINTING_PORT', 9100),

    'path' => env('PRINTING_PATH', '/dev/usb/lp0'),

    'default_format' => env('PRINTING_DEFAULT_FORMAT', '58mm'),
];
