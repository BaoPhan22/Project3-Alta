<?php

namespace Database\Seeders;

use Faker\Provider\Lorem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('events')->insert(
            [
                [
                    'name' => 'Sự kiện 1',
                    'location' => 'Đầm Sen Park',
                    'start' => '2023-05-30',
                    'end' => '2023-06-01',
                    'price' => '25000',
                    'detail' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Possimus quae voluptas eius ipsum alias voluptatum est rem similique, voluptate commodi, amet accusamus, reiciendis hic. Quas blanditiis iste officiis voluptatem voluptate.',
                    'imgUrl' => 'https://res.cloudinary.com/dpobeimdp/image/upload/v1688360428/Rectangle_1_1_n2qfdz.svg'
                ],
                [
                    'name' => 'Sự kiện 2',
                    'location' => 'Đầm Sen Park',
                    'start' => '2023-05-30',
                    'end' => '2023-06-01',
                    'price' => '75000',
                    'detail' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Possimus quae voluptas eius ipsum alias voluptatum est rem similique, voluptate commodi, amet accusamus, reiciendis hic. Quas blanditiis iste officiis voluptatem voluptate.',
                    'imgUrl' => 'https://res.cloudinary.com/dpobeimdp/image/upload/v1688360429/Rectangle_1_w9mlbc.svg'
                ],
                [
                    'name' => 'Sự kiện 3',
                    'location' => 'Đầm Sen Park',
                    'start' => '2023-05-30',
                    'end' => '2023-06-01',
                    'price' => '55000',
                    'detail' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Possimus quae voluptas eius ipsum alias voluptatum est rem similique, voluptate commodi, amet accusamus, reiciendis hic. Quas blanditiis iste officiis voluptatem voluptate.',
                    'imgUrl' => 'https://res.cloudinary.com/dpobeimdp/image/upload/v1688360434/Rectangle_1_2_cnw0oy.svg'
                ],
                [
                    'name' => 'Sự kiện 4',
                    'location' => 'Đầm Sen Park',
                    'start' => '2023-05-30',
                    'end' => '2023-06-01',
                    'price' => '100000',
                    'detail' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Possimus quae voluptas eius ipsum alias voluptatum est rem similique, voluptate commodi, amet accusamus, reiciendis hic. Quas blanditiis iste officiis voluptatem voluptate.',
                    'imgUrl' => 'https://res.cloudinary.com/dpobeimdp/image/upload/v1688360428/Rectangle_1_1_n2qfdz.svg'
                ]
            ]
        );
    }
}
