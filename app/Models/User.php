<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Http\Requests\UserRegisterRequest_SA;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Models\Role;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
        'email_verified_at' => 'datetime:Y-m-d H:m:s',
        'password' => 'hashed',
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s',
        'deleted_at' => 'datetime:Y-m-d H:m:s',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }

    // user creation / signup
    public function createNewUser($data)
    {
        $user = User::create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => $data->password,
        ]);

        $role = Role::where('name', 'user')->first();
        if ($role) {
            $user->assignRole($role);
        }

        return $user;

    }

    // mutator to encrypt password
    public function setPasswordAttribute($value)
    {
        return $this->attributes['password'] = bcrypt($value);
    }

    // mutator to convert user name into lower case
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtolower($value);
    }

    public function createUser(UserRegisterRequest_SA $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,

        ]);

        $productView = Permission::findByName('product.view', 'api');
        if (!$productView) {
            $productView = Permission::create(['name' => 'product.view']);
        }
        $userRole = Role::where(['name' => 'user'])->first();
        $userRole->givePermissionTo([
            $productView,
        ]);
        if ($userRole) {
            $user->assignRole($userRole);
            $user->givePermissionTo([$productView]);
            return $user;
        }
    }
}
