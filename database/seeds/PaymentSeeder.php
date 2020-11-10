<?php

use Illuminate\Database\Seeder;
use App\Models\Payment;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = date('Y-m-d H:i:s');
        $username = 'admin';
        $paymentList = [
            'iPay88', 'Cash-on delivery'
        ];
        foreach ($paymentList as $key => $value) {
            $count = Payment::where('name', $value)->count();
            if ($count == 0) {
                $insertData = [
                    'uuid' => \App\Models\Helper::getUuid(),
                    'name' => $value,
                    'status' => 1,
                    'updated_at' => $date,
                    'updated_by' => 1
                ];
                Payment::create($insertData);
            }
        }
    }
}
