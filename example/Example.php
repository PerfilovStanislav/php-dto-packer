<?php

class Example
{
    public function __construct()
    {
        $family = new Family([
            'surname' => 'Perfilov',
            'persons' => [
                [ // from array
                    'name'      => 'Stanislav',
                    'birthday'  => '1987-12-13T12:05:55+03:00',
                    'type'      => 'HUSBAND',
                    'friends'   => ['Elon Musk', 'Guy Ritchie'],
                ], new Person([ // or object
                    'name'      => 'Natali',
                    'type'      => PersonTypeEnum::WIFE,
                    'birthday'  => \DateTime::createFromFormat('d.m.Y', '28.11.1994'),
                ]),[
                    'name'      => 'Leo',
                    'type'      => 'CHILD',
                ],
            ],
        ]);
        $family->persons[2]->friends = ['Jason Statham', 'John Depp'];

        $convertedToString = (string)$family;

        $convertedToArray = $family->toArray();

        $createdFromJsonString = new Family($convertedToString);
    }
}