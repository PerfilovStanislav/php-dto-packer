<?php

declare(strict_types=1);

namespace Example;

class Example
{
    public function __construct()
    {
        $data = [
            'id'   => 1,
            'name' => 'USA',
            'fake' => 'fake',
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
                                    'price'     => [
                                        'amount' => 99.99,
                                        'currency'  => 'usd',
                                    ],
                                ]
                            ],
                            'tags' => [[Tag::TRANSPORT], [Tag::METAL, Tag::RED]],
                        ]
                    ],
                    'additional' => new \stdClass(),
                    'friends' => ['Trump'],
                ],
                [
                    'surname'   => 'Trump',
                    'email'     => null,
                    'birthdate' => new \DateTime('1946-06-14'),
                    'purchases' => [
                        [
                            'id'        => 11,
                            'status'    => PurchaseStatus::DELIVERED,
                            'products'  => [
                                [
                                    'id'        => 1001,
                                    'name'      => 'Trump Tower',
                                    'price'     => [
                                        'amount' => 999.99,
                                        'currency'  => Currency::EUR,
                                    ],
                                    'tags' => [[Tag::REAL_ESTATE], [Tag::METAL, Tag::BLUE], ['wood', 'yellow']],
                                ]
                            ],
                        ]
                    ],
                    'additional' => ['loves_dogs' => true],
                    'friends' => ['Elon'],
                ],
            ],
        ];

        $dto = new CountryDto($data);

        echo "$dto";            // {"id":1,"name":"USA","citizens":[{"surname":"Mr Mask","email":"elon@tesla.com","birthdate":"1971-06-28T00:00:00.000+00:00","purchases":[{"id":10,"status":"CREATED","products":[{"id":1000,"name":"Tesla","price":99.99,"currency":"usd"}]}]},{"surname":"Mr Trump","email":"donald@trump.com","birthdate":"1946-06-14T00:00:00.000+00:00","purchases":[{"id":11,"status":"DELIVERED","products":[{"id":1001,"name":"Trump Tower","price":999.99,"currency":"eur"}]}]}]}
        echo "{$dto->pack()}";  // {"id":1,"name":"USA","citizens":[{"surname":"Mr Mask","email":"elon@tesla.com","birthdate":"1971-06-28T00:00:00.000+00:00","purchases":[{"id":10,"products":[{"id":1000,"name":"Tesla","price":99.99}]}]},{"surname":"Mr Trump","email":"donald@trump.com","birthdate":"1946-06-14T00:00:00.000+00:00","purchases":[{"id":11,"status":"DELIVERED","products":[{"id":1001,"name":"Trump Tower","price":999.99,"currency":"eur"}]}]}]}
    }
}