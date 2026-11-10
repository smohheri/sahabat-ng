<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CharacterSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        if ($this->db->table('character_scale')->countAllResults() === 0) {
            $this->db->table('character_scale')->insertBatch([
                ['score' => 1, 'category' => 'Perlu Pendampingan Intensif', 'description' => 'Belum menunjukkan perilaku yang diharapkan', 'created_at' => $now],
                ['score' => 2, 'category' => 'Mulai Berkembang', 'description' => 'Kadang menunjukkan perilaku, belum konsisten', 'created_at' => $now],
                ['score' => 3, 'category' => 'Baik', 'description' => 'Sering menunjukkan perilaku dengan sedikit arahan', 'created_at' => $now],
                ['score' => 4, 'category' => 'Sangat Baik', 'description' => 'Konsisten tanpa diingatkan', 'created_at' => $now],
            ]);
        }

        if ($this->db->table('character_aspects')->countAllResults() > 0) {
            return;
        }

        $aspects = [
            ['Kesadaran Diri', 'SELF_AWARENESS', 'Kemampuan mengenali kelebihan, kekurangan, dan perasaan diri sendiri', [
                ['Mengenali kelebihan diri', 'SA_001'],
                ['Mengakui kesalahan', 'SA_002'],
                ['Mampu menyebutkan perasaan sendiri', 'SA_003'],
                ['Percaya diri saat berbicara', 'SA_004'],
            ]],
            ['Pengendalian Emosi', 'EMOTION_CONTROL', 'Kemampuan mengelola dan mengontrol emosi dengan baik', [
                ['Tidak mudah marah berlebihan', 'EC_001'],
                ['Mampu menenangkan diri', 'EC_002'],
                ['Tidak meluapkan emosi secara agresif', 'EC_003'],
                ['Mampu menyelesaikan konflik tanpa kekerasan', 'EC_004'],
            ]],
            ['Kedisiplinan & Tanggung Jawab', 'DISCIPLINE', 'Ketaatan terhadap aturan dan tanggung jawab terhadap kewajibannya', [
                ['Datang tepat waktu', 'DISC_001'],
                ['Menyelesaikan tugas', 'DISC_002'],
                ['Menjaga kebersihan', 'DISC_003'],
                ['Bertanggung jawab atas kesalahan', 'DISC_004'],
            ]],
            ['Interaksi Sosial', 'SOCIAL_INTERACTION', 'Kemampuan berinteraksi dan berkolaborasi dengan orang lain', [
                ['Berbicara sopan', 'SI_001'],
                ['Mau bekerja sama', 'SI_002'],
                ['Menghargai perbedaan', 'SI_003'],
                ['Mau membantu teman', 'SI_004'],
            ]],
            ['Life Skills & Kemandirian', 'LIFE_SKILLS', 'Kemampuan merawat diri, mengelola waktu, dan membuat keputusan', [
                ['Mengurus kebutuhan pribadi', 'LS_001'],
                ['Mengatur waktu', 'LS_002'],
                ['Mengelola uang sederhana', 'LS_003'],
                ['Mengambil keputusan sederhana', 'LS_004'],
            ]],
        ];

        foreach ($aspects as $order => $aspect) {
            $this->db->table('character_aspects')->insert([
                'aspect_name' => $aspect[0],
                'aspect_code' => $aspect[1],
                'description' => $aspect[2],
                'sort_order'  => $order + 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
            $idAspect = (int) $this->db->insertID();

            $rows = [];
            foreach ($aspect[3] as $i => $indicator) {
                $rows[] = [
                    'id_aspect'      => $idAspect,
                    'indicator_name' => $indicator[0],
                    'indicator_code' => $indicator[1],
                    'sort_order'     => $i + 1,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];
            }
            $this->db->table('character_indicators')->insertBatch($rows);
        }
    }
}
