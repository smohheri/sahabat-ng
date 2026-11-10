<?php

namespace App\Models;

use CodeIgniter\Model;

class PengaturanModel extends Model
{
    protected $table            = 'pengaturan';
    protected $primaryKey       = 'id_pengaturan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_lksa', 'alamat', 'no_telp', 'email', 'nama_kepala',
        'nama_kepala_putra', 'nama_kepala_putri', 'tahun_berdiri',
        'logo', 'dokumen_legal', 'kop_surat', 'hero_image', 'about_image',
        'facebook', 'twitter', 'instagram', 'youtube', 'linkedin', 'whatsapp',
        'bank_nama', 'bank_nomor_rekening', 'bank_atas_nama', 'qris_image',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
