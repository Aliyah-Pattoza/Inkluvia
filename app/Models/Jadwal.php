<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Jadwal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'judul',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'materi',
        'pengulangan',
        'status'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Relationship dengan User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'sedang_berlangsung' => 'green',
            'selesai' => 'gray',
            'belum_mulai' => 'blue',
            default => 'gray'
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'sedang_berlangsung' => 'Sedang Berlangsung',
            'selesai' => 'Selesai',
            'belum_mulai' => 'Belum Mulai',
            default => 'Unknown'
        };
    }

    /**
     * Check if jadwal is today
     */
    public function isToday()
    {
        return $this->tanggal->isToday();
    }

    /**
     * Check if can start session
     */
    public function canStart()
    {
        return $this->status === 'belum_mulai';
    }

    /**
     * Get original material ID for edit form
     * This method tries to find the material ID based on the saved title
     */
    public function getOriginalMaterialId()
    {
        if ($this->materi) {
            $material = \App\Models\Material::where('judul', $this->materi)->first();
            return $material ? $material->id : null;
        }
        return null;
    }

    /**
     * Get related material model
     */
    public function getMaterialAttribute()
    {
        if ($this->materi) {
            return \App\Models\Material::where('judul', $this->materi)->first();
        }
        return null;
    }

    /**
     * Scope untuk filter jadwal hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('tanggal', Carbon::today());
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}