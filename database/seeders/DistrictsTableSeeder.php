<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictsTableSeeder extends Seeder
{
    public function run(): void
    {
        // الحصول على IDs المدن
        $cities = DB::table('cities')->pluck('id', 'name');

        $districts = [
            // Damascus Districts
            ['name' => 'Al-Midan', 'city_id' => $cities['Damascus'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Qanawat', 'city_id' => $cities['Damascus'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Shaghour', 'city_id' => $cities['Damascus'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Marjah', 'city_id' => $cities['Damascus'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Salihiyah', 'city_id' => $cities['Damascus'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Mazraa', 'city_id' => $cities['Damascus'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Rukn al-Din', 'city_id' => $cities['Damascus'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Muhajireen', 'city_id' => $cities['Damascus'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Qaboun', 'city_id' => $cities['Damascus'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Jobar', 'city_id' => $cities['Damascus'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Yarmouk', 'city_id' => $cities['Damascus'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kafr Souseh', 'city_id' => $cities['Damascus'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Mezzeh', 'city_id' => $cities['Damascus'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Barzeh', 'city_id' => $cities['Damascus'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Qadam', 'city_id' => $cities['Damascus'], 'created_at' => now(), 'updated_at' => now()],

            // Rural Damascus Districts
            ['name' => 'Douma', 'city_id' => $cities['Rural Damascus'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Qutayfah', 'city_id' => $cities['Rural Damascus'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Nabk', 'city_id' => $cities['Rural Damascus'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Yabroud', 'city_id' => $cities['Rural Damascus'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Zabadani', 'city_id' => $cities['Rural Damascus'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Qatana', 'city_id' => $cities['Rural Damascus'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Darayya', 'city_id' => $cities['Rural Damascus'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Tall', 'city_id' => $cities['Rural Damascus'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Kiswah', 'city_id' => $cities['Rural Damascus'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Qudsaya', 'city_id' => $cities['Rural Damascus'], 'created_at' => now(), 'updated_at' => now()],

            // Tartus Districts
            ['name' => 'Tartus City', 'city_id' => $cities['Tartus'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Baniyas', 'city_id' => $cities['Tartus'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Safita', 'city_id' => $cities['Tartus'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sheikh Badr', 'city_id' => $cities['Tartus'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dreikish', 'city_id' => $cities['Tartus'], 'created_at' => now(), 'updated_at' => now()],

            // Aleppo Districts
            ['name' => 'Aleppo City', 'city_id' => $cities['Aleppo'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Manbij', 'city_id' => $cities['Aleppo'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Azaz', 'city_id' => $cities['Aleppo'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Jarabulus', 'city_id' => $cities['Aleppo'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Afrin', 'city_id' => $cities['Aleppo'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Bab', 'city_id' => $cities['Aleppo'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Deir Hafer', 'city_id' => $cities['Aleppo'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'As-Safira', 'city_id' => $cities['Aleppo'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kobani', 'city_id' => $cities['Aleppo'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Jabal Saman', 'city_id' => $cities['Aleppo'], 'created_at' => now(), 'updated_at' => now()],

            // Hama Districts
            ['name' => 'Hama City', 'city_id' => $cities['Hama'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Salamiyah', 'city_id' => $cities['Hama'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Masyaf', 'city_id' => $cities['Hama'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mahardah', 'city_id' => $cities['Hama'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Suqaylabiyah', 'city_id' => $cities['Hama'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Ghab', 'city_id' => $cities['Hama'], 'created_at' => now(), 'updated_at' => now()],

            // Homs Districts
            ['name' => 'Homs City', 'city_id' => $cities['Homs'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Rastan', 'city_id' => $cities['Homs'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Qusayr', 'city_id' => $cities['Homs'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Palmyra', 'city_id' => $cities['Homs'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Taldou', 'city_id' => $cities['Homs'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Talkalakh', 'city_id' => $cities['Homs'], 'created_at' => now(), 'updated_at' => now()],

            // Latakia Districts
            ['name' => 'Latakia City', 'city_id' => $cities['Latakia'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Jableh', 'city_id' => $cities['Latakia'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Haffah', 'city_id' => $cities['Latakia'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Qardaha', 'city_id' => $cities['Latakia'], 'created_at' => now(), 'updated_at' => now()],
            // ['name' => 'Qardaha', 'city_id' => $cities['Latakia'], 'created_at' => now(), 'updated_at' => now()],
    
    // Raqa Districts
    ['name' => 'Raqa City', 'city_id' => $cities['Raqqa'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Tabqa', 'city_id' => $cities['Raqqa'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Tal Abyad', 'city_id' => $cities['Raqqa'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Mansoura', 'city_id' => $cities['Raqqa'], 'created_at' => now(), 'updated_at' => now()],
    
    // Deir ez-Zor Districts
    ['name' => 'Deir ez-Zor City', 'city_id' => $cities['Deir ez-Zor'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Mayadeen', 'city_id' => $cities['Deir ez-Zor'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Bukamal', 'city_id' => $cities['Deir ez-Zor'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Rural Areas', 'city_id' => $cities['Deir ez-Zor'], 'created_at' => now(), 'updated_at' => now()],
    
    // Al-Hasakah Districts
    ['name' => 'Al-Hasakah City', 'city_id' => $cities['Al-Hasakah'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Qamishli', 'city_id' => $cities['Al-Hasakah'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Ras al-Ayn', 'city_id' => $cities['Al-Hasakah'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Rural Areas', 'city_id' => $cities['Al-Hasakah'], 'created_at' => now(), 'updated_at' => now()],
    
    // Daraa Districts
    ['name' => 'Daraa City', 'city_id' => $cities['Daraa'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Izra', 'city_id' => $cities['Daraa'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Sanamayn', 'city_id' => $cities['Daraa'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Rural Areas', 'city_id' => $cities['Daraa'], 'created_at' => now(), 'updated_at' => now()],
    
    // Al-Suwayda Districts
    ['name' => 'Al-Suwayda City', 'city_id' => $cities['Al-Suwayda'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Shahba', 'city_id' => $cities['Al-Suwayda'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Salkhad', 'city_id' => $cities['Al-Suwayda'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Rural Areas', 'city_id' => $cities['Al-Suwayda'], 'created_at' => now(), 'updated_at' => now()],
    
    // Idlib Districts
    ['name' => 'Idlib City', 'city_id' => $cities['Idlib'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Ariha', 'city_id' => $cities['Idlib'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Harem', 'city_id' => $cities['Idlib'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Jisr al-Shughur', 'city_id' => $cities['Idlib'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Rural Areas', 'city_id' => $cities['Idlib'], 'created_at' => now(), 'updated_at' => now()],
    
    // Quneitra Districts
    ['name' => 'Quneitra City', 'city_id' => $cities['Quneitra'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Fiq', 'city_id' => $cities['Quneitra'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Rural Areas', 'city_id' => $cities['Quneitra'], 'created_at' => now(), 'updated_at' => now()],

            // Add districts for other provinces...
        ];

        DB::table('districts')->insert($districts);
    }
}