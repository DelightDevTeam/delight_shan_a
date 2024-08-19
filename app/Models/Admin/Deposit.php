<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;


    protected $fillable = ['user_id', 'bank_id', 'reference_number', 'status', 'amount'];
}
