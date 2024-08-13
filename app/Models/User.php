<?php

namespace App\Models;

use App\Enums\UserType;
use App\Models\Admin\Permission;
use App\Models\Admin\Role;
use App\Models\Admin\Transaction;
use App\Models\Admin\Wallet;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_name',
        'name',
        'profile',
        'email',
        'password',
        'profile',
        'phone',
        // 'balance',
        'max_score',
        'agent_id',
        'status',
        'type',
        'is_changed_password'
        //'referral_code'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function hasRole($role)
    {
        return $this->roles->contains('title', $role);
    }

    public static function adminUser()
    {
        return self::where('type', UserType::Admin)->first();
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function scopeRoleLimited($query)
    {
        if (! Auth::user()->hasRole('Admin')) {
            return $query->where('agent_id', Auth::id());
        }

        return $query;
    }

    public function scopeHasRole($query, $roleId)
    {
        return $query->whereRelation('roles', 'role_id', $roleId);
    }
    
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }
}
