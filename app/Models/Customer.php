<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    // Tentukan kolom mana yang dapat diisi (fillable)
    protected $fillable = [
        'name',
        'slug',
        'email',
        'password',
        'remember_token',
    ];

    // Tentukan kolom yang perlu di-casting
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Jika Anda menggunakan hashing untuk password, pastikan untuk melakukan enkripsi saat menyimpan password
    public static function boot()
    {
        parent::boot();
    
        static::creating(function ($customer) {
            // Enkripsi password sebelum disimpan ke database
            if ($customer->password) {
                $customer->password = bcrypt($customer->password);
            }
    
            // Generate slug dari name
            if ($customer->name) {
                $customer->slug = Str::slug($customer->name);
            }
        });
    
        static::updating(function ($customer) {
            // Enkripsi password jika diperbarui
            if ($customer->password && $customer->isDirty('password')) {
                $customer->password = bcrypt($customer->password);
            }
    
            // Update slug jika name berubah
            if ($customer->isDirty('name')) {
                $customer->slug = Str::slug($customer->name);
            }
        });
    }
    

    /**
     * Relasi ke Order (one-to-many).
     * Setiap customer dapat memiliki banyak order.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Relasi atau metode tambahan bisa ditambahkan di sini
}
