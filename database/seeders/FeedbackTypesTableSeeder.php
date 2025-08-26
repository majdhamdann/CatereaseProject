<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FeedbackTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // مسح البيانات السابقة إذا وجدت
        DB::table('feedback_types')->delete();
        
        // إدراج بيانات لأنواع التقييم
        DB::table('feedback_types')->insert([
            // مطاعم
            [
                 'id' => 1,
                'target_type' => 'branch',
                'target_ref_id' => 1, // افترض أن لديك مطعم بالرقم 1
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'target_type' => 'branch',
                'target_ref_id' => 2, // مطعم آخر
                'created_at' => now(),
                'updated_at' => now()
            ],
            
            // عناصر طعام
            [
                'id' => 3,
                'target_type' => 'package',
                'target_ref_id' => 3, // عنصر طعام
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 4,
                'target_type' => 'package',
                'target_ref_id' => 2, // عنصر طعام آخر
                'created_at' => now(),
                'updated_at' => now()
            ],
            
          
            
            // باقات
            [
                'id' => 5,
                'target_type' => 'package',
                'target_ref_id' => 1, // باقة
                'created_at' => now(),
                'updated_at' => now()
            ],
            
            // مندوبي التوصيل
            [
                'id' => 6,
                'target_type' => 'delivery_person',
                'target_ref_id' => 1, // مندوب توصيل
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 7,
                'target_type' => 'delivery_person',
                'target_ref_id' => 2, // مندوب توصيل آخر
                'created_at' => now(),
                'updated_at' => now()
            ],
            
          
        ]);
    }
}