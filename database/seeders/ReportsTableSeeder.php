<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // مسح البيانات السابقة إذا وجدت
        DB::table('reports')->delete();
        
        // إدراج بيانات تقارير نموذجية
        DB::table('reports')->insert([
            // تقرير عن المبيعات
            [
                'branch_id' => 1, // فرع رئيسي
                'manager_id' => 2, // مستخدم لديه صلاحية مدير
                'subject' => 'تقرير المبيعات الأسبوعي',
                'details' => 'تقرير مفصل عن المبيعات للأسبوع الماضي مع مقارنة بالأسبوع السابق وتحليل للأداء.',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2)
            ],
            // تقرير عن المخزون
            [
                'branch_id' => 1,
                'manager_id' => 2,
                'subject' => 'تقرير المخزون الشهري',
                'details' => 'تقرير شامل عن حالة المخزون للفرع الرئيسي، مع تحديد المواد التي تحتاج إلى إعادة تخزين.',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5)
            ],
            // تقرير عن الموظفين
            [
                'branch_id' => 2, // فرع آخر
                'manager_id' => 3, // مدير آخر
                'subject' => 'تقرير أداء الموظفين',
                'details' => 'تقييم أداء الموظفين للشهر الحالي مع ملاحظات حول التدريب والتطوير المطلوب.',
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(7)
            ],
            // تقرير عن الشكاوى
            [
                'branch_id' => 1,
                'manager_id' => 2,
                'subject' => 'تقرير الشكاوى والعملاء',
                'details' => 'تحليل للشكاوى الواردة من العملاء خلال الشهر الماضي ومقترحات للتحسين.',
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(10)
            ],
            // تقرير مالي
            [
                'branch_id' => 2,
                'manager_id' => 3,
                'subject' => 'التقرير المالي الربع سنوي',
                'details' => 'تحليل للأداء المالي للفرع خلال الربع الأخير مع توقعات للربع القادم.',
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(15)
            ],
            // تقرير عن الصيانة
            [
                'branch_id' => 1,
                'manager_id' => 2,
                'subject' => 'تقرير صيانة المعدات',
                'details' => 'تقرير عن حالة المعدات والأجهزة في الفرع ومواعيد الصيانة الدورية القادمة.',
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(20)
            ],
            // تقرير تسويقي
            [
                'branch_id' => 2,
                'manager_id' => 3,
                'subject' => 'تقرير الحملة التسويقية الأخيرة',
                'details' => 'تحليل لنتائج الحملة التسويقية التي تمت الشهر الماضي وتأثيرها على المبيعات.',
                'created_at' => Carbon::now()->subDays(25),
                'updated_at' => Carbon::now()->subDays(25)
            ]
        ]);
    }
}