<?php
namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $restaurants = [8, 9, 10]; // Restaurant IDs
        $userIds = [8,9,11,13,14,16,17];
        $deliveryCharges = [0, 50, 100]; // Example delivery charges

        // Loop through September and October
        foreach (['2024-09', '2024-10'] as $month) {
            $daysInMonth = Carbon::parse($month . '-01')->daysInMonth;

            for ($day = 1; $day <= $daysInMonth; $day++) {
                foreach ($restaurants as $restaurantId) {
                    // Generate 10 orders for each day and restaurant
                    for ($i = 0; $i < 10; $i++) {
                        $createdAt = Carbon::create($month . '-' . $day, rand(9, 20), rand(0, 59)); // Random time between 9 AM and 8 PM
                        $estimatedDeliveryTime = $createdAt->copy()->addMinutes(15); // Add 15 minutes to created_at

                        DB::table('orders')->insert([
                            'user_id' => $faker->randomElement($userIds), // Assuming user IDs range from 1 to 100
                            'restaurant_id' => $restaurantId,
                            'branch_id' => $restaurantId, // Assuming branch IDs range from 1 to 5
                            'total_amount' => $faker->randomFloat(2, 100, 1000), // Random total amount between 100 and 1000
                            'status' => 'delivered',
                            'order_type' => 'delivery',
                            'delivery_charges' => $faker->randomElement($deliveryCharges),
                            'estimated_delivery_time' => $estimatedDeliveryTime,
                            'created_at' => $createdAt,
                            'updated_at' => $createdAt, // Assuming created_at and updated_at are the same for seeding
                        ]);
                    }
                }
            }
        }
    }
}
