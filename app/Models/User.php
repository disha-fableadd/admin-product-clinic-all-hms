<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\EncryptsModelData;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, EncryptsModelData;
    // use HasFactory;

    protected $table = 'user';

    protected $fillable = [
        'id',
        'role_id',
        'fullname',
        'username',
        'email',
        'phone',
        'profile',
        'password',
        'branch_id',
        'plan_id',
        'clinic_name',
        'clinic_logo',
        'status',
        'is_deleted',
        'created_by'
    ];
    protected $hidden = ['password', 'remember_token'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected array $encryptedAttributes = ['fullname', 'email', 'phone'];
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function details()
    {
        return $this->hasOne(UserDetails::class, 'user_id', 'id');
    }

    public function permissions()
    {
        return $this->hasMany(UserPermission::class);
    }

    public function treatments()
    {
        return $this->hasMany(Treatment::class, 'doctor_id');
    }
    public function patients()
    {
        return $this->hasManyThrough(Patients::class, Treatment::class, 'doctor_id', 'treatment_id', 'id', 'id');
    }
    public function appointments()
    {
        return $this->hasMany(Appointments::class, 'doctor_id');
    }

    // In the User model
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id', 'id');
    }

    public function getActivePlan()
    {
        if ($this->plan_id) {
            $plan = $this->plan ?? \App\Models\Plan::find($this->plan_id);
            if ($plan && $plan->is_active) {
                return $plan;
            }
        }

        $activePlan = \App\Models\Plan::where('is_active', 1)->latest('id')->first();
        if ($activePlan) {
            return $activePlan;
        }

        return \App\Models\Plan::latest('id')->first();
    }

    public function getLatestPlan()
    {
        if ($this->plan_id) {
            $plan = $this->plan ?? \App\Models\Plan::find($this->plan_id);
            if ($plan) {
                return $plan;
            }
        }

        return \App\Models\Plan::latest('id')->first();
    }

    // public function getProfileAttribute($value)
    // {
    //     return $value ? asset("storage/{$value}") : asset('default-user.png');
    // }

    public function getProfileAttribute($value)
    {
        return $value ? asset(env('IMAGE_PATH') . "{$value}") : asset(env('IMAGE_PATH') . '/admin/assets/img/img1.png');
    }
}
