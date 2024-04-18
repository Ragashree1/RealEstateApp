<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;

    const CREATED_AT = 'create_date';
    const UPDATED_AT = null;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'email',
        'contact',
        'password',
        'dob',
        'user_profile_id',
        'profile_photo_path',
        'status',
        'nationality',
        'residence_country',
        'user_profile_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        //
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];


    public function userProfile(): BelongsTo
    {
        return $this->belongsTo(UserProfile::class);
    }

    // public function __destruct()
    // {
    //     parent::__destruct();
    // }

    public function updateUserAccount($variables)
    {
        try {
            if (isset($variables['username'])) {
                $this->username = $variables['username'];
            }
            if (isset($variables['first_name'])) {
                $this->first_name = $variables['first_name'];
            }
            if (isset($variables['last_name'])) {
                $this->last_name = $variables['last_name'];
            }
            if (isset($variables['email'])) {
                $this->email = $variables['email'];
            }
            if (isset($variables['contact'])) {
                $this->contact = $variables['contact'];
            }
            if (isset($variables['password'])) {
                $this->password = bcrypt($variables['password']);
            }
            if (isset($variables['user_profile_id'])) {
                $this->user_profile_id = $variables['user_profile_id'];
            }
            if (isset($variables['nationality'])) {
                $this->nationality = $variables['nationality'];
            }
            if (isset($variables['residence_country'])) {
                $this->residence_country = $variables['residence_country'];
            }
            if (isset($variables['status'])) {
                $this->status = $variables['status'];
            }
            if (isset($variables['dob'])) {
                $this->dob = $variables['dob'];
            }
            if (isset($variables['profile_photo_path'])) {
                $this->profile_photo_path = $variables['profile_photo_path'];
            }

            if (Request::hasFile('photo')) {
                $photoPath = Request::file('photo')->store('users');
                $this->profile_photo_path = $photoPath;
            }

            $this->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function createUserAccount($variables)
    {
        try {
            $user = new User;

            if (isset($variables['username'])) {
                $user->username = $variables['username'];
            }
            if (isset($variables['first_name'])) {
                $user->first_name = $variables['first_name'];
            }
            if (isset($variables['last_name'])) {
                $user->last_name = $variables['last_name'];
            }
            if (isset($variables['email'])) {
                $user->email = $variables['email'];
            }
            if (isset($variables['contact'])) {
                $user->contact = $variables['contact'];
            }
            if (isset($variables['password'])) {
                $user->password = bcrypt($variables['password']); // Make sure to hash passwords
            } else {
                $user->password = bcrypt('password');
            }
            if (isset($variables['user_profile_id'])) {
                $user->user_profile_id = $variables['user_profile_id'];
            }
            if (isset($variables['nationality'])) {
                $user->nationality = $variables['nationality'];
            }
            if (isset($variables['residence_country'])) {
                $user->residence_country = $variables['residence_country'];
            }
            if (isset($variables['status'])) {
                $user->status = $variables['status'];
            } else {
                $user->status = 'active';
            }
            if (isset($variables['dob'])) {
                $user->dob = $variables['dob'];
            }
            if (isset($variables['profile_photo_path'])) {
                $user->profile_photo_path = $variables['profile_photo_path'];
            }
            if (isset($variables['created_by'])) {
                $user->created_by = Auth::user()->id;
            }
            if (Request::hasFile('photo')) {
                $photoPath = Request::file('photo')->store('users');
                $user->profile_photo_path = $photoPath;
            }
            $user->save();
            return $user;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function suspendUserAccount()
    {
        try {
            $this->user = 'suspended';
            $this->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function activateUserAccount()
    {
        try {
            $this->user = 'activate';
            $this->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function assignRole($roleId)
    {
        try {
            $this->user_profile_id = $roleId;
            $this->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function deleteUserAccount($user)
    {
        DB::table('users')->where('username', $user->username)->delete();

        return true;
    }
}
