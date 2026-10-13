<?php

return [
    // Fallback for any category not matched below
    'default' => [
        'Additional details' => [
            'Brand',
            'Model Number',
            'Color',
            'Weight',
            'Warranty',
        ],
        'Item details' => [
            'Manufacturer',
            'Country of Origin',
            'Item Package Quantity',
        ],
    ],

    // Match by lowercased category name
    'laptop' => [
        'Processor' => [
            'Brand',
            'Model',
            'Cores',
            'Speed',
            'Cache',
            'Generation',
        ],
        'Display' => [
            'Screen Size',
            'Resolution',
            'Panel Type',
            'Refresh Rate',
            'Touchscreen',
        ],
        'Memory' => [
            'RAM',
            'RAM Type',
            'RAM Speed',
            'Max RAM Support',
        ],
        'Storage' => [
            'Storage Type',
            'Storage Capacity',
            'Expandable',
        ],
        'Ports & Slots' => [
            'USB Ports',
            'HDMI',
            'Thunderbolt',
            'SD Card Slot',
            'Headphone Jack',
        ],
        'Connectivity' => [
            'Wi-Fi',
            'Bluetooth',
            'Ethernet',
            'NFC',
        ],
        'Battery' => [
            'Battery Type',
            'Capacity',
            'Life',
            'Fast Charging',
        ],
        'Graphics' => [
            'GPU Brand',
            'GPU Model',
            'Dedicated VRAM',
        ],
        'Additional details' => [
            'Operating System',
            'Weight',
            'Dimensions',
            'Warranty',
        ],
    ],

    'smartphone' => [
        'Display'   => ['Screen Size', 'Resolution', 'Refresh Rate', 'Protection'],
        'Processor' => ['Chipset', 'CPU', 'GPU'],
        'Memory'    => ['RAM', 'Storage', 'Expandable'],
        'Camera'    => ['Rear Camera', 'Front Camera', 'Video Recording'],
        'Battery'   => ['Capacity', 'Charging Speed', 'Wireless Charging'],
        'Connectivity' => ['Network', 'Wi-Fi', 'Bluetooth', 'NFC'],
        'Additional details' => ['SIM Type', 'Weight', 'Warranty'],
    ],

    'headphones' => [
        'Audio'      => ['Driver Size', 'Frequency Response', 'Impedance'],
        'Connectivity' => ['Bluetooth Version', 'Wireless Range', 'Wired Option'],
        'Battery'    => ['Playtime', 'Charging Time', 'Fast Charging'],
        'Additional details' => ['Weight', 'Color', 'Warranty'],
    ],
];