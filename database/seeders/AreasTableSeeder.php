<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreasTableSeeder extends Seeder
{
    public function run(): void
    {
        // الحصول على IDs النواحي
        $districts = DB::table('districts')->pluck('id', 'name');
        $areas = [
            // Damascus - Al-Midan Areas
            ['name' => 'Al-Midan Al-Tahtani', 'district_id' => $districts['Al-Midan'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Midan Al-Fawqani', 'district_id' => $districts['Al-Midan'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Zain Al-Abidin', 'district_id' => $districts['Al-Midan'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Abu Habl', 'district_id' => $districts['Al-Midan'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Qabr Aatikah', 'district_id' => $districts['Al-Midan'], 'created_at' => now(), 'updated_at' => now()],

            // Damascus - Al-Qanawat Areas
            ['name' => 'Al-Suwayqah', 'district_id' => $districts['Al-Qanawat'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Souq Saroujah', 'district_id' => $districts['Al-Qanawat'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Qaymariyah', 'district_id' => $districts['Al-Qanawat'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Qanawat', 'district_id' => $districts['Al-Qanawat'], 'created_at' => now(), 'updated_at' => now()],

            // Damascus - Al-Shaghour Areas
            ['name' => 'Al-Amara', 'district_id' => $districts['Al-Shaghour'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bab Al-Jabiyah', 'district_id' => $districts['Al-Shaghour'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Shaghour Al-Barani', 'district_id' => $districts['Al-Shaghour'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Shaghour Al-Jawani', 'district_id' => $districts['Al-Shaghour'], 'created_at' => now(), 'updated_at' => now()],

            // Damascus - Al-Marjah Areas
            ['name' => 'Sahat Al-Marjeh', 'district_id' => $districts['Al-Marjah'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Jisr Victoria', 'district_id' => $districts['Al-Marjah'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Hariqah', 'district_id' => $districts['Al-Marjah'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Bahsah', 'district_id' => $districts['Al-Marjah'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Souq Al-Hamidiyah', 'district_id' => $districts['Al-Marjah'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Hamrawin', 'district_id' => $districts['Al-Marjah'], 'created_at' => now(), 'updated_at' => now()],

            // Damascus - Al-Salihiyah Areas
            ['name' => 'Abu Rummanah', 'district_id' => $districts['Al-Salihiyah'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Maliki', 'district_id' => $districts['Al-Salihiyah'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Sheikh Muhyi al-Din', 'district_id' => $districts['Al-Salihiyah'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sharia Al-Hamra', 'district_id' => $districts['Al-Salihiyah'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Afif', 'district_id' => $districts['Al-Salihiyah'], 'created_at' => now(), 'updated_at' => now()],

            // Damascus - Al-Mazraa Areas
            ['name' => 'Al-Baramkeh', 'district_id' => $districts['Al-Mazraa'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Taliani', 'district_id' => $districts['Al-Mazraa'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Mazraa', 'district_id' => $districts['Al-Mazraa'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Fardous', 'district_id' => $districts['Al-Mazraa'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sahat Al-Sabaa Bahrat', 'district_id' => $districts['Al-Mazraa'], 'created_at' => now(), 'updated_at' => now()],

            // Damascus - Rukn al-Din Areas
            ['name' => 'Ash Al-Warwar', 'district_id' => $districts['Rukn al-Din'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hay Al-Wurood', 'district_id' => $districts['Rukn al-Din'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Rukn al-Din', 'district_id' => $districts['Rukn al-Din'], 'created_at' => now(), 'updated_at' => now()],

            // Damascus - Al-Muhajireen Areas
            ['name' => 'Hay Al-Muhajireen', 'district_id' => $districts['Al-Muhajireen'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Nahr Yazeed', 'district_id' => $districts['Al-Muhajireen'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Adwi', 'district_id' => $districts['Al-Muhajireen'], 'created_at' => now(), 'updated_at' => now()],

            // Damascus - Qaboun Areas
            ['name' => 'Qaboun', 'district_id' => $districts['Qaboun'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Masaken Qaboun', 'district_id' => $districts['Qaboun'], 'created_at' => now(), 'updated_at' => now()],

            // Damascus - Jobar Areas
            ['name' => 'Jobar', 'district_id' => $districts['Jobar'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ein Tarma', 'district_id' => $districts['Jobar'], 'created_at' => now(), 'updated_at' => now()],

            // Damascus - Yarmouk Areas
            ['name' => 'Yarmouk Camp', 'district_id' => $districts['Yarmouk'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Tadamun', 'district_id' => $districts['Yarmouk'], 'created_at' => now(), 'updated_at' => now()],

            // Damascus - Kafr Souseh Areas
            ['name' => 'Kafr Souseh Al-Balad', 'district_id' => $districts['Kafr Souseh'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tanzim Kafr Souseh', 'district_id' => $districts['Kafr Souseh'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hay Al-Villas', 'district_id' => $districts['Kafr Souseh'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hay Al-Midan Al-Gharbi', 'district_id' => $districts['Kafr Souseh'], 'created_at' => now(), 'updated_at' => now()],

            // Damascus - Al-Mezzeh Areas
            ['name' => 'Al-Mezzeh 86', 'district_id' => $districts['Al-Mezzeh'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Autostrad Al-Mezzeh', 'district_id' => $districts['Al-Mezzeh'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Villas Al-Gharbiyah', 'district_id' => $districts['Al-Mezzeh'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Villas Al-Sharqiyah', 'district_id' => $districts['Al-Mezzeh'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Sheikh Saad', 'district_id' => $districts['Al-Mezzeh'], 'created_at' => now(), 'updated_at' => now()],

            // Damascus - Barzeh Areas
            ['name' => 'Barzeh Al-Balad', 'district_id' => $districts['Barzeh'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Masaken Barzeh', 'district_id' => $districts['Barzeh'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Barzeh Sharqiyah', 'district_id' => $districts['Barzeh'], 'created_at' => now(), 'updated_at' => now()],

            // Damascus - Qadam Areas
            ['name' => 'Al-Qadam', 'district_id' => $districts['Qadam'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hay Al-Asali', 'district_id' => $districts['Qadam'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hay Boor Saeed', 'district_id' => $districts['Qadam'], 'created_at' => now(), 'updated_at' => now()],

            // Rural Damascus - Douma Areas
            ['name' => 'Douma City', 'district_id' => $districts['Douma'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Harasta', 'district_id' => $districts['Douma'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Arbin', 'district_id' => $districts['Douma'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Saqba', 'district_id' => $districts['Douma'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hamouriyah', 'district_id' => $districts['Douma'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kafr Batna', 'district_id' => $districts['Douma'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Jisreen', 'district_id' => $districts['Douma'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Malihah', 'district_id' => $districts['Douma'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Zamalka', 'district_id' => $districts['Douma'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ein Tarma', 'district_id' => $districts['Douma'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hizzah', 'district_id' => $districts['Douma'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Shifoniya', 'district_id' => $districts['Douma'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Babila', 'district_id' => $districts['Douma'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Yalda', 'district_id' => $districts['Douma'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Beit Sahm', 'district_id' => $districts['Douma'], 'created_at' => now(), 'updated_at' => now()],

            // Rural Damascus - Al-Qutayfah Areas
            ['name' => 'Al-Qutayfah City', 'district_id' => $districts['Al-Qutayfah'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Maaloula', 'district_id' => $districts['Al-Qutayfah'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Ruhaybah', 'district_id' => $districts['Al-Qutayfah'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Jayroud', 'district_id' => $districts['Al-Qutayfah'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Qasimiyah', 'district_id' => $districts['Al-Qutayfah'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Nasiriyah', 'district_id' => $districts['Al-Qutayfah'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Atnah', 'district_id' => $districts['Al-Qutayfah'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Baliyah', 'district_id' => $districts['Al-Qutayfah'], 'created_at' => now(), 'updated_at' => now()],

            // Rural Damascus - Al-Nabk Areas
            ['name' => 'Al-Nabk City', 'district_id' => $districts['Al-Nabk'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Deir Atiyah', 'district_id' => $districts['Al-Nabk'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Qarah', 'district_id' => $districts['Al-Nabk'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Jarajir', 'district_id' => $districts['Al-Nabk'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Flitah', 'district_id' => $districts['Al-Nabk'], 'created_at' => now(), 'updated_at' => now()],

            // Rural Damascus - Yabroud Areas
            ['name' => 'Yabroud City', 'district_id' => $districts['Yabroud'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Sahl', 'district_id' => $districts['Yabroud'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Nashabiyah', 'district_id' => $districts['Yabroud'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ras Al-Maarah', 'district_id' => $districts['Yabroud'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Assal Al-Ward', 'district_id' => $districts['Yabroud'], 'created_at' => now(), 'updated_at' => now()],

            // Rural Damascus - Al-Zabadani Areas
            ['name' => 'Al-Zabadani City', 'district_id' => $districts['Al-Zabadani'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bloudan', 'district_id' => $districts['Al-Zabadani'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Baqin', 'district_id' => $districts['Al-Zabadani'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Madaya', 'district_id' => $districts['Al-Zabadani'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Serghaya', 'district_id' => $districts['Al-Zabadani'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ein Hawr', 'district_id' => $districts['Al-Zabadani'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kafr Yabous', 'district_id' => $districts['Al-Zabadani'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Taybah', 'district_id' => $districts['Al-Zabadani'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Jadeidet Yabous', 'district_id' => $districts['Al-Zabadani'], 'created_at' => now(), 'updated_at' => now()],

            // Rural Damascus - Qatana Areas
            ['name' => 'Qatana City', 'district_id' => $districts['Qatana'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Jadeidet Artouz', 'district_id' => $districts['Qatana'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sahnaya', 'district_id' => $districts['Qatana'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ashrafiyat Sahnaya', 'district_id' => $districts['Qatana'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Kiswah Al-Gharbiyah', 'district_id' => $districts['Qatana'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Artouz', 'district_id' => $districts['Qatana'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Aisan', 'district_id' => $districts['Qatana'], 'created_at' => now(), 'updated_at' => now()],

            // Rural Damascus - Darayya Areas
            ['name' => 'Darayya City', 'district_id' => $districts['Darayya'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Muadamiyat al-Sham', 'district_id' => $districts['Darayya'], 'created_at' => now(), 'updated_at' => now()],

            // Rural Damascus - Al-Tall Areas
            ['name' => 'Al-Tall City', 'district_id' => $districts['Al-Tall'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Manin', 'district_id' => $districts['Al-Tall'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Maaraba', 'district_id' => $districts['Al-Tall'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Barhaliyah', 'district_id' => $districts['Al-Tall'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hafeir al-Tahta', 'district_id' => $districts['Al-Tall'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hafeir al-Fawqa', 'district_id' => $districts['Al-Tall'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Rankous', 'district_id' => $districts['Al-Tall'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Saidnaya', 'district_id' => $districts['Al-Tall'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Talfita', 'district_id' => $districts['Al-Tall'], 'created_at' => now(), 'updated_at' => now()],

            // Rural Damascus - Al-Kiswah Areas
            ['name' => 'Al-Kiswah City', 'district_id' => $districts['Al-Kiswah'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Zakiyeh', 'district_id' => $districts['Al-Kiswah'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Khirbet Ghazalah', 'district_id' => $districts['Al-Kiswah'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Deir Ali', 'district_id' => $districts['Al-Kiswah'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Artouz al-Qadim', 'district_id' => $districts['Al-Kiswah'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Housh', 'district_id' => $districts['Al-Kiswah'], 'created_at' => now(), 'updated_at' => now()],

            // Rural Damascus - Qudsaya Areas
            ['name' => 'Qudsaya City', 'district_id' => $districts['Qudsaya'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dahiyat Qudsaya', 'district_id' => $districts['Qudsaya'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Hamah', 'district_id' => $districts['Qudsaya'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Jadeidet al-Shibani', 'district_id' => $districts['Qudsaya'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Jamraya', 'district_id' => $districts['Qudsaya'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ashrafiyat al-Wadi', 'district_id' => $districts['Qudsaya'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Dreij', 'district_id' => $districts['Qudsaya'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Yaafour', 'district_id' => $districts['Qudsaya'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Aliyah', 'district_id' => $districts['Qudsaya'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Wadi Barada', 'district_id' => $districts['Qudsaya'], 'created_at' => now(), 'updated_at' => now()],

            // Tartus - Tartus City Areas
            ['name' => 'Tartus City Center', 'district_id' => $districts['Tartus City'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Sheikh Saad', 'district_id' => $districts['Tartus City'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Safsafah', 'district_id' => $districts['Tartus City'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ras Al-Shaghri', 'district_id' => $districts['Tartus City'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hosn Suleiman', 'district_id' => $districts['Tartus City'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Khirbet Mohsen', 'district_id' => $districts['Tartus City'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Naqib', 'district_id' => $districts['Tartus City'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Qurm', 'district_id' => $districts['Tartus City'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Mansoura', 'district_id' => $districts['Tartus City'], 'created_at' => now(), 'updated_at' => now()],

            // Tartus - Baniyas Areas
            ['name' => 'Baniyas City', 'district_id' => $districts['Baniyas'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Qastelaniyah', 'district_id' => $districts['Baniyas'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Rawdah', 'district_id' => $districts['Baniyas'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Maneeah', 'district_id' => $districts['Baniyas'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Sarafand', 'district_id' => $districts['Baniyas'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dreikish Gharbiyah', 'district_id' => $districts['Baniyas'], 'created_at' => now(), 'updated_at' => now()],

            // Tartus - Safita Areas
            ['name' => 'Safita City', 'district_id' => $districts['Safita'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Qadmus', 'district_id' => $districts['Safita'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Braiseen', 'district_id' => $districts['Safita'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ghafrah', 'district_id' => $districts['Safita'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mashta Al-Helu', 'district_id' => $districts['Safita'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ein Al-Bayda', 'district_id' => $districts['Safita'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Qarqur', 'district_id' => $districts['Safita'], 'created_at' => now(), 'updated_at' => now()],

            // Tartus - Sheikh Badr Areas
            ['name' => 'Sheikh Badr City', 'district_id' => $districts['Sheikh Badr'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Barmana Al-Mashayekh', 'district_id' => $districts['Sheikh Badr'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hameen', 'district_id' => $districts['Sheikh Badr'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Marj', 'district_id' => $districts['Sheikh Badr'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ein Qain', 'district_id' => $districts['Sheikh Badr'], 'created_at' => now(), 'updated_at' => now()],

            // Tartus - Dreikish Areas
            ['name' => 'Dreikish City', 'district_id' => $districts['Dreikish'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Jneinet Raslan', 'district_id' => $districts['Dreikish'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Heilat', 'district_id' => $districts['Dreikish'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Taliyah', 'district_id' => $districts['Dreikish'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Al-Matariyah', 'district_id' => $districts['Dreikish'], 'created_at' => now(), 'updated_at' => now()],

            ['name' => 'Al-Qardaha City', 'district_id' => $districts['Qardaha'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Harf Al-Musaytirah', 'district_id' => $districts['Qardaha'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Fakhourah', 'district_id' => $districts['Qardaha'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Joubet Barghal', 'district_id' => $districts['Qardaha'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Daliyah Al-Gharbiyah', 'district_id' => $districts['Qardaha'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Ein Al-Hajal', 'district_id' => $districts['Qardaha'], 'created_at' => now(), 'updated_at' => now()],
    
    //  ['name' => 'Al-Qardaha City', 'district_id' => $districts['Al-Qardaha'], 'created_at' => now(), 'updated_at' => now()],
    //  ['name' => 'Harf Al-Musaytirah', 'district_id' => $districts['Al-Qardaha'], 'created_at' => now(), 'updated_at' => now()],
    //  ['name' => 'Al-Fakhourah', 'district_id' => $districts['Al-Qardaha'], 'created_at' => now(), 'updated_at' => now()],
    //  ['name' => 'Joubet Barghal', 'district_id' => $districts['Al-Qardaha'], 'created_at' => now(), 'updated_at' => now()],
    //   ['name' => 'Al-Daliyah Al-Gharbiyah', 'district_id' => $districts['Al-Qardaha'], 'created_at' => now(), 'updated_at' => now()],
    //     ['name' => 'Ein Al-Hajal', 'district_id' => $districts['Al-Qardaha'], 'created_at' => now(), 'updated_at' => now()],
      
    // Raqa City Areas
    ['name' => 'Raqa City Center', 'district_id' => $districts['Raqa City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Karama', 'district_id' => $districts['Raqa City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Madaan', 'district_id' => $districts['Raqa City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Sabkha', 'district_id' => $districts['Raqa City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Tayara', 'district_id' => $districts['Raqa City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Tal Abyad Al-Sagheer', 'district_id' => $districts['Raqa City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Jarniyah', 'district_id' => $districts['Raqa City'], 'created_at' => now(), 'updated_at' => now()],
    
    // Al-Tabqa Areas
    ['name' => 'Al-Tabqa City', 'district_id' => $districts['Al-Tabqa'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Mansoura', 'district_id' => $districts['Al-Tabqa'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Jarniyah', 'district_id' => $districts['Al-Tabqa'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Ein Issa', 'district_id' => $districts['Al-Tabqa'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Safihah', 'district_id' => $districts['Al-Tabqa'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Huwaijeh', 'district_id' => $districts['Al-Tabqa'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Abu Hammam', 'district_id' => $districts['Al-Tabqa'], 'created_at' => now(), 'updated_at' => now()],
    
    // Tal Abyad Areas
    ['name' => 'Tal Abyad City', 'district_id' => $districts['Tal Abyad'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Slouk', 'district_id' => $districts['Tal Abyad'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Ein Issa', 'district_id' => $districts['Tal Abyad'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Tal Madiq', 'district_id' => $districts['Tal Abyad'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Abu Rmeila', 'district_id' => $districts['Tal Abyad'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Umm Al-Khair', 'district_id' => $districts['Tal Abyad'], 'created_at' => now(), 'updated_at' => now()],
    
    // Al-Mansoura Areas
    ['name' => 'Al-Mansoura City', 'district_id' => $districts['Al-Mansoura'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Ulayyaniyah', 'district_id' => $districts['Al-Mansoura'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Khusheib', 'district_id' => $districts['Al-Mansoura'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Rayyan', 'district_id' => $districts['Al-Mansoura'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Hool', 'district_id' => $districts['Al-Mansoura'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Tabiyah', 'district_id' => $districts['Al-Mansoura'], 'created_at' => now(), 'updated_at' => now()],
    
    // Deir ez-Zor City Areas
    ['name' => 'Deir ez-Zor City Center', 'district_id' => $districts['Deir ez-Zor City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Mohassen', 'district_id' => $districts['Deir ez-Zor City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Sour', 'district_id' => $districts['Deir ez-Zor City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Kasra', 'district_id' => $districts['Deir ez-Zor City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Tibni', 'district_id' => $districts['Deir ez-Zor City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Bseira', 'district_id' => $districts['Deir ez-Zor City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Shaafah', 'district_id' => $districts['Deir ez-Zor City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Jalaa', 'district_id' => $districts['Deir ez-Zor City'], 'created_at' => now(), 'updated_at' => now()],
    
    // Al-Mayadeen Areas
    ['name' => 'Al-Mayadeen City', 'district_id' => $districts['Al-Mayadeen'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Asharah', 'district_id' => $districts['Al-Mayadeen'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Dhiban', 'district_id' => $districts['Al-Mayadeen'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Bseira', 'district_id' => $districts['Al-Mayadeen'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Jalaa', 'district_id' => $districts['Al-Mayadeen'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Bghayliyah', 'district_id' => $districts['Al-Mayadeen'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Huwaikah', 'district_id' => $districts['Al-Mayadeen'], 'created_at' => now(), 'updated_at' => now()],
    
    // Al-Bukamal Areas
    ['name' => 'Al-Bukamal City', 'district_id' => $districts['Al-Bukamal'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Hajin', 'district_id' => $districts['Al-Bukamal'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Sousah', 'district_id' => $districts['Al-Bukamal'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Nasiriyah', 'district_id' => $districts['Al-Bukamal'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Shumaytiyah', 'district_id' => $districts['Al-Bukamal'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Ghassaniyah', 'district_id' => $districts['Al-Bukamal'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Qawriyah', 'district_id' => $districts['Al-Bukamal'], 'created_at' => now(), 'updated_at' => now()],
      

    
    // Deir ez-Zor Rural Areas
    ['name' => 'Al-Suwayiyah', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Husain', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Jafrah', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Sayahi', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Kashtah', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Murayiyah', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Tuwayiyah', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    
    // Al-Hasakah City Areas
    ['name' => 'Al-Hasakah City Center', 'district_id' => $districts['Al-Hasakah City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Shaddadi', 'district_id' => $districts['Al-Hasakah City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Markada', 'district_id' => $districts['Al-Hasakah City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Hool', 'district_id' => $districts['Al-Hasakah City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Tal Tamr', 'district_id' => $districts['Al-Hasakah City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Kobani', 'district_id' => $districts['Al-Hasakah City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Ras al-Ayn', 'district_id' => $districts['Al-Hasakah City'], 'created_at' => now(), 'updated_at' => now()],
    
    // Qamishli Areas
    ['name' => 'Qamishli City', 'district_id' => $districts['Qamishli'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Amuda', 'district_id' => $districts['Qamishli'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Tal Hamis', 'district_id' => $districts['Qamishli'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Qahtaniyah', 'district_id' => $districts['Qamishli'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Ras al-Ayn', 'district_id' => $districts['Qamishli'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Darbasiyah', 'district_id' => $districts['Qamishli'], 'created_at' => now(), 'updated_at' => now()],
    
    // Ras al-Ayn Areas
    ['name' => 'Ras al-Ayn City', 'district_id' => $districts['Ras al-Ayn'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Malikiyah', 'district_id' => $districts['Ras al-Ayn'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Yaarabiyah', 'district_id' => $districts['Ras al-Ayn'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Jawadiyah', 'district_id' => $districts['Ras al-Ayn'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Tal Rifaat', 'district_id' => $districts['Ras al-Ayn'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Ein Issa', 'district_id' => $districts['Ras al-Ayn'], 'created_at' => now(), 'updated_at' => now()],
    
    // Al-Hasakah Rural Areas
    ['name' => 'Al-Hasakah Rural', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Ein Al-Bayda', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Shaddadi Rural', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Tal Halaf', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Muhajireen', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Umm Al-Khair', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Khabour', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    
    // Daraa City Areas
    ['name' => 'Daraa City Center', 'district_id' => $districts['Daraa City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Bosra al-Sham', 'district_id' => $districts['Daraa City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Khirbet Ghazalah', 'district_id' => $districts['Daraa City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Muzayrib', 'district_id' => $districts['Daraa City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Daael', 'district_id' => $districts['Daraa City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Shajara', 'district_id' => $districts['Daraa City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Jizeh', 'district_id' => $districts['Daraa City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Musayfirah', 'district_id' => $districts['Daraa City'], 'created_at' => now(), 'updated_at' => now()],
    
    // Izra Areas
    ['name' => 'Izra City', 'district_id' => $districts['Izra'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Jassem', 'district_id' => $districts['Izra'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Nawa', 'district_id' => $districts['Izra'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Sheikh Maskin', 'district_id' => $districts['Izra'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Taseel', 'district_id' => $districts['Izra'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Harak', 'district_id' => $districts['Izra'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Tal Shehab', 'district_id' => $districts['Izra'], 'created_at' => now(), 'updated_at' => now()],
    
    // Al-Sanamayn Areas
    ['name' => 'Al-Sanamayn City', 'district_id' => $districts['Al-Sanamayn'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Masmiyah', 'district_id' => $districts['Al-Sanamayn'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Ghabaghib', 'district_id' => $districts['Al-Sanamayn'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Utman', 'district_id' => $districts['Al-Sanamayn'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Tal Shehab Al-Gharbi', 'district_id' => $districts['Al-Sanamayn'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Ein Zughbah', 'district_id' => $districts['Al-Sanamayn'], 'created_at' => now(), 'updated_at' => now()],
    
    // Daraa Rural Areas
    ['name' => 'Kafr Shams', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Heet', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Sida', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Naseeb', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Ghasam', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Harah', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Sahwah', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Lajat', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    
    // Al-Suwayda City Areas
    ['name' => 'Al-Suwayda City Center', 'district_id' => $districts['Al-Suwayda City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Mazraa', 'district_id' => $districts['Al-Suwayda City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Mashqanaf', 'district_id' => $districts['Al-Suwayda City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Kafr', 'district_id' => $districts['Al-Suwayda City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Qrayya', 'district_id' => $districts['Al-Suwayda City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Shahba', 'district_id' => $districts['Al-Suwayda City'], 'created_at' => now(), 'updated_at' => now()],
    
    // Shahba Areas
    ['name' => 'Shahba City', 'district_id' => $districts['Shahba'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Ara', 'district_id' => $districts['Shahba'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Ariqah', 'district_id' => $districts['Shahba'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Thalah', 'district_id' => $districts['Shahba'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Ein Al-Thurayya', 'district_id' => $districts['Shahba'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Mashnaf', 'district_id' => $districts['Shahba'], 'created_at' => now(), 'updated_at' => now()],
    
    // Salkhad Areas
    ['name' => 'Salkhad City', 'district_id' => $districts['Salkhad'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Dhibin', 'district_id' => $districts['Salkhad'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Malh', 'district_id' => $districts['Salkhad'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Khirbet', 'district_id' => $districts['Salkhad'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Shuaf', 'district_id' => $districts['Salkhad'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Nahtah', 'district_id' => $districts['Salkhad'], 'created_at' => now(), 'updated_at' => now()],
    
    // Al-Suwayda Rural Areas
    ['name' => 'Al-Sheikh Saad', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    //['name' => 'Ein Al-Bayda', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Qanawat', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Hasbiya', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Sama', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Marj Al-Qusour', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    
    // Idlib City Areas
    ['name' => 'Idlib City Center', 'district_id' => $districts['Idlib City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Binnish', 'district_id' => $districts['Idlib City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Sarmin', 'district_id' => $districts['Idlib City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Saraqib', 'district_id' => $districts['Idlib City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Maarat Masrin', 'district_id' => $districts['Idlib City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Taftanaz', 'district_id' => $districts['Idlib City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Abu Al-Dhuhour', 'district_id' => $districts['Idlib City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Kafr Sajnah', 'district_id' => $districts['Idlib City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Kafr Takharim', 'district_id' => $districts['Idlib City'], 'created_at' => now(), 'updated_at' => now()],
    
    // Ariha Areas
    ['name' => 'Ariha City', 'district_id' => $districts['Ariha'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Ihsem', 'district_id' => $districts['Ariha'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Mhambel', 'district_id' => $districts['Ariha'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Armanaz', 'district_id' => $districts['Ariha'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Qurqania', 'district_id' => $districts['Ariha'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Maarat Al-Numan', 'district_id' => $districts['Ariha'], 'created_at' => now(), 'updated_at' => now()],
    
    // Harem Areas
    ['name' => 'Harem City', 'district_id' => $districts['Harem'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Dana', 'district_id' => $districts['Harem'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Salqin', 'district_id' => $districts['Harem'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Kafr Takharim', 'district_id' => $districts['Harem'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Badama', 'district_id' => $districts['Harem'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Armanaz', 'district_id' => $districts['Harem'], 'created_at' => now(), 'updated_at' => now()],
    
    // Jisr al-Shughur Areas
    ['name' => 'Jisr al-Shughur City', 'district_id' => $districts['Jisr al-Shughur'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Badama', 'district_id' => $districts['Jisr al-Shughur'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Darkush', 'district_id' => $districts['Jisr al-Shughur'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Janadiyah', 'district_id' => $districts['Jisr al-Shughur'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Zantan', 'district_id' => $districts['Jisr al-Shughur'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Hawajiz', 'district_id' => $districts['Jisr al-Shughur'], 'created_at' => now(), 'updated_at' => now()],
    
    // Idlib Rural Areas
    ['name' => 'Kafr Sajnah', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Kafr Takharim', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Qminas', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Sinjar', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Habeet', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Tamanah', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    
    // Quneitra City Areas
    ['name' => 'Quneitra City Center', 'district_id' => $districts['Quneitra City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Jabata al-Khashab', 'district_id' => $districts['Quneitra City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Khan Arnabah', 'district_id' => $districts['Quneitra City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Khushniyah', 'district_id' => $districts['Quneitra City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Masadah', 'district_id' => $districts['Quneitra City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Muzayrib', 'district_id' => $districts['Quneitra City'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Harah', 'district_id' => $districts['Quneitra City'], 'created_at' => now(), 'updated_at' => now()],
    
    // Fiq Areas
    ['name' => 'Fiq City', 'district_id' => $districts['Fiq'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Batihah', 'district_id' => $districts['Fiq'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Umm Al-Duyur', 'district_id' => $districts['Fiq'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Beit Jinn', 'district_id' => $districts['Fiq'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Salihiyah', 'district_id' => $districts['Fiq'], 'created_at' => now(), 'updated_at' => now()],
    
    // Quneitra Rural Areas
    ['name' => 'Tal Shehab', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Ein Qain', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Kafr Shams', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Musayfirah', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Shebaa', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Taybah', 'district_id' => $districts['Rural Areas'], 'created_at' => now(), 'updated_at' => now()],
];

DB::table('areas')->insert($areas);
    }
}