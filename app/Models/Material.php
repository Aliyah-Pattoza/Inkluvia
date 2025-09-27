<?php
// app/Models/Material.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'deskripsi',
        'tahun_terbit',
        'penerbit',
        'edisi',
        'kategori',
        'tingkat',
        'file_path',
        'braille_data_path',
        'total_halaman',
        'status',
        'akses',
        'created_by',
        'published_at'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function requests()
    {
        return $this->hasMany(MaterialRequest::class);
    }

    public function brailleContents()
    {
        return $this->hasMany(MaterialBrailleContent::class);
    }

    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            'published' => 'success',
            'review' => 'warning',
            'processing' => 'info',
            'draft' => 'secondary',
            'archived' => 'danger',
            default => 'secondary'
        };
    }

    public function getAksesBadgeColorAttribute()
    {
        if ($this->akses === 'public') {
            return 'success';
        } elseif (is_numeric($this->akses)) {
            return 'info';
        } else {
            return 'secondary';
        }
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    public function scopeByTingkat($query, $tingkat)
    {
        return $query->where('tingkat', $tingkat);
    }

    public static function getKategoriOptions()
    {
        return [
            'matematika' => 'Matematika',
            'bahasa' => 'Bahasa',
            'ipa' => 'IPA',
            'ips' => 'IPS',
            'agama' => 'Agama',
            'umum' => 'Umum'
        ];
    }

    public static function getTingkatOptions()
    {
        return [
            'paud' => 'PAUD',
            'sd' => 'SD',
            'smp' => 'SMP',
            'sma' => 'SMA',
            'perguruan_tinggi' => 'Perguruan Tinggi',
            'umum' => 'Umum'
        ];
    }

    public static function getAksesOptions()
    {
        $options = [
            'public' => 'Publik'
        ];
        
        // Add lembaga options
        try {
            $lembagas = \App\Models\Lembaga::orderBy('nama')->get();
            foreach ($lembagas as $lembaga) {
                $options[$lembaga->id] = $lembaga->nama;
            }
        } catch (\Exception $e) {
            \Log::error('Error fetching lembaga options: ' . $e->getMessage());
        }
        
        return $options;
    }
    
    public function getAksesDisplayAttribute()
    {
        if ($this->akses === 'public') {
            return 'Publik';
        } elseif (is_numeric($this->akses)) {
            try {
                $lembaga = \App\Models\Lembaga::find($this->akses);
                if ($lembaga) {
                    return $lembaga->nama;
                } else {
                    // If lembaga not found, log the issue and return unknown
                    \Log::warning("Lembaga with ID {$this->akses} not found for material {$this->id}");
                    return 'Tidak Diketahui (ID: ' . $this->akses . ')';
                }
            } catch (\Exception $e) {
                \Log::error('Error fetching lembaga: ' . $e->getMessage());
                return 'Tidak Diketahui (ID: ' . $this->akses . ')';
            }
        } else {
            // Handle invalid access values
            $invalidValues = ['premium', 'restricted', 'private', 'admin'];
            if (in_array(strtolower($this->akses), $invalidValues)) {
                \Log::warning("Invalid access value '{$this->akses}' found for material {$this->id}. Consider updating to valid value.");
                return 'Akses Tidak Valid (' . $this->akses . ')';
            }
            
            // Try to find lembaga by name if akses is not numeric
            try {
                $lembagaByName = \App\Models\Lembaga::where('nama', 'like', '%' . $this->akses . '%')->first();
                if ($lembagaByName) {
                    return $lembagaByName->nama;
                }
            } catch (\Exception $e) {
                \Log::error('Error fetching lembaga by name: ' . $e->getMessage());
            }
            return 'Tidak Diketahui (' . $this->akses . ')';
        }
    }
}