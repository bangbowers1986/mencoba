<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $casts = [
        'items' => 'array'
    ];
    
    protected $fillable = [
        'local_id', 
        'user_id', 
        'total', 
        'status', 
        'items'
    ];

    // Tambahkan nilai default untuk status
    protected $attributes = [
        'status' => 'completed'
    ];

    // Jika Anda memiliki relasi dengan TransactionItem
    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }
}