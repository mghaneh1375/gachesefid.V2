<?php

namespace App\models;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Auth\UserTrait;
//use Illuminate\Auth\UserInterface;
//use Illuminate\Auth\Reminders\RemindableTrait;
//use Illuminate\Auth\Reminders\RemindableInterface;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * An Eloquent Model: 'User'
 *
 * @property integer $id
 * @property integer $sex
 * @property integer $introducer
 * @property integer $status
 * @property integer $level
 * @property string $invitationCode
 * @property string $username
 * @property string $password
 * @property string $firstName
 * @property string $phoneNum
 * @property string $NID
 * @property string $lastName
 * @method static \Illuminate\Database\Query\Builder|\App\models\User whereUsername($value)
 * @method static \Illuminate\Database\Query\Builder|\App\models\User whereNID($value)
 * @method static \Illuminate\Database\Query\Builder|\App\models\User whereLevel($value)
 * @method static \Illuminate\Database\Query\Builder|\App\models\User whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\models\User wherePhoneNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\models\User whereInvitationCode($value)
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @mixin \Eloquent
 * @property-read \App\models\User $students
 * @property-read \App\models\User $advisers
 */

class User extends Authenticatable{

	use Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */


	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */

	protected $fillable = [
		'name', 'password'
	];

	protected $hidden = array('password', 'remember_token');

	public function scopeStudents($query) {
		return $query->where('level', '=', getValueInfo('studentLevel'));
	}

	public function scopeAdvisers($query) {
		$query->where('level', '=', getValueInfo('adviserLevel'));
	}

	public function scopeSchools($query) {
		$query->where('level', '=', getValueInfo('schoolLevel'));
	}

	public function getRememberToken()
	{
		return $this->remember_token;
	}

	public function setRememberToken($value)
	{
		$this->remember_token = $value;
	}

	public function getRememberTokenName()
	{
		return 'remember_token';
	}

	public function getAuthIdentifier() {
		return $this->getKey();
	}
	public function getAuthPassword() {
		return $this->password;
	}

	public static function whereId($value) {
		return User::find($value);
	}
}
