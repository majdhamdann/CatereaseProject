<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FeedbackTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('feedback')->delete();
        
        DB::table('feedback')->insert([
            [
                'user_id' => 1, 
                'FeedbackType_id' => 1, 
                'type' => 'rating',
                'status' => 'resolved',
                'score' => 4.5,
                'message' => 'مطعم رائع والطعام لذيذ، الخدمة سريعة والجو جميل',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'user_id' => 2,
                'FeedbackType_id' => 2,
                'type' => 'rating',
                'status' => 'resolved',
                'score' => 5.0,
                'message' => 'البرجر كان ممتازاً ونظيف جداً، أنصح بتجربته',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'user_id' => 3,
                'FeedbackType_id' => 3, 
                'type' => 'rating',
                'status' => 'resolved',
                'score' => 4.0,
                'message' => 'التوصيل كان سريعاً والطلب وصل بحالة جيدة',
                'created_at' => now(),
                'updated_at' => now()
            ],
            
            [
                'user_id' => 4,
                'FeedbackType_id' => 1, 
                'type' => 'complaint',
                'status' => 'under_review',
                'score' => null,
                'message' => 'الطلب تأخر كثيراً عن الوقت المحدد دون إشعار مسبق',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'user_id' => 5,
                'FeedbackType_id' => 2, 
                'type' => 'complaint',
                'status' => 'resolved',
                'score' => null,
                'message' => 'الطعام لم يكن طازجاً كما هو متوقع، وكانت النكهة غير مقبولة',
                'created_at' => now()->subDays(2),
                'updated_at' => now()
            ],
            [
                'user_id' => 1,
                'FeedbackType_id' => 5, 
                'type' => 'complaint',
                'status' => 'under_review',
                'score' => null,
                'message' => 'مندوب التوصيل كان غير مهذب ولم يلتزم بموعد التسليم',
                'created_at' => now()->subDays(1),
                'updated_at' => now()
            ],
            
            [
                'user_id' => 2,
                'FeedbackType_id' => 6,
                'type' => 'rating',
                'status' => 'resolved',
                'score' => 3.5,
                'message' => 'الفرع نظيف ولكن المساحة صغيرة نوعاً ما',
                'created_at' => now()->subDays(5),
                'updated_at' => now()
            ],
            [
                'user_id' => 3,
                'FeedbackType_id' => 7, 
                'type' => 'rating',
                'status' => 'resolved',
                'score' => 4.0,
                'message' => 'الباقة جيدة والسعر معقول بالنسبة للكمية',
                'created_at' => now()->subDays(3),
                'updated_at' => now()
            ]
        ]);
    }
}