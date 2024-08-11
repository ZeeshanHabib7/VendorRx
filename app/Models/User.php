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
use Exception;
use Illuminate\Database\Eloquent\SoftDeletes;
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes; //added softdeletes

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
        'stripe_customer_id',
        'stripe_payment_method_id'
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

    // relationship with the CouponUsage model
    public function couponUsages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    // user creation / signup
    public function createNewUser($payload, $paymentService)
    {
        try {
            // get role to be assigned
            $role = $this->getRole($payload);
            // create stripe customer if role is user
            if ($role->name == "user") {
                $payload = $this->createStripeCustomer($payload, $paymentService);
            }
            // Store User in DB 
            $user = self::create($payload);
            // sync role
            if ($role) {
                $user->syncRoles($role);
            }
            return $user;
        } catch (Exception $e) {
            throw $e;
        }
    }

    // Assign role based on provided role ID or default to 'user' role
    public function getRole($payload)
    {
        $roleId = $payload['role_ids'] ?? null;
        $role = $roleId ? Role::find($roleId) : Role::where('name', 'user');
        return $role->first();
    }

    // Create Stripe Customer
    public function createStripeCustomer($payload, $paymentService)
    {
        try {
            // generate payload for stripe service
            $customerPayload = $this->generateStripeCustomerPayload($payload);
            // Create Customer on stripe
            $stripeCustomer = $paymentService->createCustomer($customerPayload);
            // added stripe customer id in payload
            $payload["stripe_customer_id"] = $stripeCustomer->id;
            return $payload;
        } catch (Exception $e) {
            throw $e;
        }
    }

    // generate payoad for stripe customer
    public function generateStripeCustomerPayload($payload)
    {
        return [
            'name' => $payload['name'],
            'email' => $payload['email'],
        ];
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
