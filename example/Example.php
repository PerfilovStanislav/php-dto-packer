<?php

class Example
{
    public function __construct()
    {
        $data = [
            'id'     => 1,
            'name'   => 'USA',
            'citizens' => [
                [
                    'lastname'  => 'Mask',
                    'email'     => 'elon@tesla.com',
                    'birthdate' => '1971-06-28',
                    'purchases' => [
                        [
                            'id'        => 10,
                            'status'    => 'CREATED',
                            'products'  => [
                                [
                                    'id'        => 1000,
                                    'name'      => 'Tesla',
                                    'price'     => 99.99,
                                    'currency'  => 'usd',
                                ]
                            ],
                        ]
                    ]
                ],
                [
                    'surname'   => 'Trump',
                    'email'     => 'donald@trump.com',
                    'birthdate' => new \DateTime('1946-06-14'),
                    'purchases' => [
                        [
                            'id'        => 11,
                            'status'    => PurchaseStatus::DELIVERED,
                            'products'  => [
                                [
                                    'id'        => 1001,
                                    'name'      => 'Trump Tower',
                                    'price'     => 999.99,
                                    'currency'  => Currency::EUR,
                                ]
                            ],
                        ]
                    ]
                ],
            ],
        ];

        $dto = new CountryDto($data);

        echo "$dto";            // {"id":1,"name":"USA","citizens":[{"surname":"Mr Mask","email":"elon@tesla.com","birthdate":"1971-06-28T00:00:00.000+00:00","purchases":[{"id":10,"status":"CREATED","products":[{"id":1000,"name":"Tesla","price":99.99,"currency":"usd"}]}]},{"surname":"Mr Trump","email":"donald@trump.com","birthdate":"1946-06-14T00:00:00.000+00:00","purchases":[{"id":11,"status":"DELIVERED","products":[{"id":1001,"name":"Trump Tower","price":999.99,"currency":"eur"}]}]}]}
        echo "{$dto->pack()}";  // {"id":1,"name":"USA","citizens":[{"surname":"Mr Mask","email":"elon@tesla.com","birthdate":"1971-06-28T00:00:00.000+00:00","purchases":[{"id":10,"products":[{"id":1000,"name":"Tesla","price":99.99}]}]},{"surname":"Mr Trump","email":"donald@trump.com","birthdate":"1946-06-14T00:00:00.000+00:00","purchases":[{"id":11,"status":"DELIVERED","products":[{"id":1001,"name":"Trump Tower","price":999.99,"currency":"eur"}]}]}]}
    }
}