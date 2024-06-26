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

    //testing

    public function __construct($userDetails = null)
    {
        parent::__construct();

        if ($userDetails) {
            if (isset($userDetails['username'])) {
                $this->username = $userDetails['username'];
            }
            if (isset($userDetails['first_name'])) {
                $this->first_name = $userDetails['first_name'];
            }
            if (isset($userDetails['last_name'])) {
                $this->last_name = $userDetails['last_name'];
            }
            if (isset($userDetails['email'])) {
                $this->email = $userDetails['email'];
            }
            if (isset($userDetails['contact'])) {
                $this->contact = $userDetails['contact'];
            }
            if (isset($userDetails['password'])) {
                $this->password = bcrypt($userDetails['password']);  //store hashed id in database
            } else {
                $this->password = bcrypt('password');
            }
            if (isset($userDetails['user_profile_id'])) {
                $this->user_profile_id = $userDetails['user_profile_id'];
            }
            if (isset($userDetails['nationality'])) {
                $this->nationality = $userDetails['nationality'];
            }
            if (isset($userDetails['residence_country'])) {
                $this->residence_country = $userDetails['residence_country'];
            }
            if (isset($userDetails['status'])) {
                $this->status = $userDetails['status'];
            } else {
                $this->status = 'active';
            }
            if (isset($userDetails['dob'])) {
                $this->dob = $userDetails['dob'];
            }
            if (isset($userDetails['profile_photo_path'])) {
                $this->profile_photo_path = $userDetails['profile_photo_path'];
            }

            if (isset($userDetails['created_by'])) {
                $this->created_by = Auth::user() ? Auth::user()->id : null;
            }
            
            if (Request::hasFile('photo')) {
                $photoPath = Request::file('photo')->store('users');
                $this->profile_photo_path = $photoPath;
            }
            // $this->create_date = 
        }
    }
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
        'created_by',
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

    public function updateUserAccount($userDetails)
    {
        try {
            if (isset($userDetails['username'])) {
                $this->username = $userDetails['username'];
            }
            if (isset($userDetails['first_name'])) {
                $this->first_name = $userDetails['first_name'];
            }
            if (isset($userDetails['last_name'])) {
                $this->last_name = $userDetails['last_name'];
            }
            if (isset($userDetails['email'])) {
                $this->email = $userDetails['email'];
            }
            if (isset($userDetails['contact'])) {
                $this->contact = $userDetails['contact'];
            }
            if (isset($userDetails['password'])) {
                $this->password = bcrypt($userDetails['password']);
            }
            if (isset($userDetails['user_profile_id'])) {
                $this->user_profile_id = $userDetails['user_profile_id'];
            }
            if (isset($userDetails['nationality'])) {
                $this->nationality = $userDetails['nationality'];
            }
            if (isset($userDetails['residence_country'])) {
                $this->residence_country = $userDetails['residence_country'];
            }
            if (isset($userDetails['status'])) {
                $this->status = $userDetails['status'];
            }
            if (isset($userDetails['dob'])) {
                $this->dob = $userDetails['dob'];
            }
            if (isset($userDetails['profile_photo_path'])) {
                $this->profile_photo_path = $userDetails['profile_photo_path'];
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

    public static function createUserAccount($userDetails)
    {
        try {
            $user = new User($userDetails);
            $user->save();
            return $user;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function suspendAccount()
    {
        try {
            $this->status = 'suspended';
            $this->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function activateAccount()
    {
        try {
            $this->status = 'active';
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

    //retrieves users other than system admin
    public static function getUsers()
    {
        return User::with('userProfile')->where('user_profile_id', null)->orWhere('user_profile_id', '!=', 1)->get();
    }

    public static function deleteUserAccount($user)
    {
        DB::table('users')->where('username', $user->username)->delete();

        return true;
    }

    public static function searchUser($name)
    {
        return User::where('username', 'like', $name.'%')->get();
    }
}
