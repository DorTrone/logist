<?php

return [
    'usdToTmt' => 19.8,
    'transportTypes' => [
        ['id' => 0, 'name' => 'AirTransport'],
        // ['id' => 1, 'name' => 'RoadTransport'],
        // ['id' => 2, 'name' => 'SeaTransport'],
    ],
    'transportStatuses' => [
        ['id' => 0, 'name' => 'BeijingWarehouse', 'color' => 'warning'],
        ['id' => 1, 'name' => 'DepartedToAshgabat', 'color' => 'success'],
    ],
    'locations' => [
        ['id' => 0, 'name' => 'AG'],
        ['id' => 1, 'name' => 'AH'],
        ['id' => 2, 'name' => 'BN'],
        ['id' => 3, 'name' => 'DZ'],
        ['id' => 4, 'name' => 'LB'],
        ['id' => 5, 'name' => 'MR'],
    ],
    'packagePayments' => [
        ['id' => 0, 'name' => 'Weight', 'ext' => 'WeightExt'],
        ['id' => 1, 'name' => 'Volume', 'ext' => 'VolumeExt'],
        ['id' => 2, 'name' => 'Quantity', 'ext' => 'QuantityExt'],
    ],
    'packageTypes' => [
        ['id' => 0, 'name' => 'Standard', 'transportType' => 0, 'packagePayment' => 0, 'price' => 7], // departure => 4. 19:00
        ['id' => 1, 'name' => 'Express', 'transportType' => 0, 'packagePayment' => 0, 'price' => 9], // departure => 6. 19:00
        ['id' => 2, 'name' => 'Healthcare', 'transportType' => 0, 'packagePayment' => 0, 'price' => 12], // departure => 6. 19:00
        ['id' => 3, 'name' => 'Passenger', 'transportType' => 0, 'packagePayment' => 0, 'price' => 13], // departure => 6. 19:00
        ['id' => 4, 'name' => 'Phone', 'transportType' => 0, 'packagePayment' => 2, 'price' => 12], // departure => 6. 19:00
        ['id' => 5, 'name' => 'Laptop', 'transportType' => 0, 'packagePayment' => 2, 'price' => 35], // departure => 6. 19:00
        ['id' => 6, 'name' => 'Document', 'transportType' => 0, 'packagePayment' => 2, 'price' => 5], // departure => 6. 19:00
    ],
    'packageStatuses' => [
        ['id' => 0, 'name' => 'BeijingWarehouse', 'auto' => 0, 'color' => 'warning'],
        ['id' => 1, 'name' => 'DepartedToAshgabat', 'auto' => 0, 'color' => 'primary'],
        ['id' => 2, 'name' => 'ArrivedInAshgabat', 'auto' => 1, 'color' => 'primary'],
        ['id' => 3, 'name' => 'DepartedToRegion', 'auto' => 1, 'color' => 'primary'],
        ['id' => 4, 'name' => 'ArrivedInRegion', 'auto' => 1, 'color' => 'primary'],
        ['id' => 5, 'name' => 'Delivered', 'auto' => 1, 'color' => 'success'],
    ],
    'paymentStatuses' => [
        ['id' => 0, 'name' => 'Unpaid', 'color' => 'warning'],
        ['id' => 1, 'name' => 'PartPaid', 'color' => 'primary'],
        ['id' => 2, 'name' => 'FullyPaid', 'color' => 'success'],
        ['id' => 3, 'name' => 'Pending', 'color' => 'warning'],
    ],
    'payments' => [
        ['id' => 0, 'name' => 'Cash'],
        ['id' => 1, 'name' => 'WeChatPay'],
        ['id' => 2, 'name' => 'Alipay'],
    ],
];
