<?php namespace Jones\VisitorLog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Jones\VisitorLog\Useragent;

class Visitor extends Model {
	protected $table = 'visitors';
	protected $primaryKey = 'sid';
	public $incrementing = false;
	
	protected $agents = array();

	public static function isOnline($uid)
	{
		$user = Visitor::findUser($uid);
		
		return isset($user->sid);
	}
	
	public static function getCurrent()
	{
		if(!Session::has('visitor_log_sid'))
		    return false;

		$sid = Session::get('visitor_log_sid');

		return Visitor::find($sid);
	}
	
	public static function clear()
	{
		$instance = new static;
		
		return $instance->newQuery()->where('updated_at', '<', time()-Config::get('visitor-log::onlinetime')*60)->delete();
	}

	public static function loggedIn()
	{
		$instance = new static;
		
		return $instance->newQuery()->whereNotNull('user')->get();
	}

	public static function guests()
	{
		$instance = new static;

		return $instance->newQuery()->whereNull('user')->get();
	}

	public static function findUser($id)
	{
		$instance = new static;

		return $instance->newQuery()->where('user', '=', (int)$id)->first();
	}

	public static function findIP($ip)
	{
		$instance = new static;

		return $instance->newQuery()->where('ip', '=', $ip)->first();
	}

	public function isUser()
	{
		return ($this->user != 0);
	}

	public function isGuest()
	{
		return ($this->user == 0);
	}

	public function getAgentAttribute()
	{
		if(isset($this->agents[$this->useragent]))
		    return $this->agents[$this->useragent];
		
		if($this->useragent == "")
		    return null;
		
		return $this->agents[$this->useragent] = new Useragent($this->useragent);
	}
	
	public function getAgentsAttribute()
	{
		if ($this->is_browser())
		{
		    $agent = $this->browser.' '.$this->version;
		}
		elseif ($this->is_robot())
		{
		    $agent = $this->robot;
		}
		elseif ($this->is_mobile())
		{
		    $agent = $this->mobile;
		}
		else
		{
		    $agent = 'Unidentified User Agent';
		}
		
		return $agent;
	}
	
	public function setSidAttribute($value)
	{
		$this->attributes['sid'] = $value;
		
		Session::put('visitor_log_sid', $value);
	}
	
	/* Wrapper for the Useragent class */
	
	public function is_browser($key = null)
	{
		if(is_null($this->agent)) return null;
		return $this->agent->is_browser($key);
	}

	public function is_robot($key = null)
	{
		if(is_null($this->agent)) return null;
		return $this->agent->is_robot($key);
	}

	public function is_mobile($key = null)
	{
		if(is_null($this->agent)) return null;
		return $this->agent->is_mobile($key);
	}

	public function is_referral()
	{
		if(is_null($this->agent)) return null;
		return $this->agent->is_referral();
	}
	
	public function getPlatformAttribute()
	{
		if(is_null($this->agent)) return null;
		return $this->agent->platform();
	}

	public function getBrowserAttribute()
	{
		if(is_null($this->agent)) return null;
		return $this->agent->browser();
	}

	public function getVersionAttribute()
	{
		if(is_null($this->agent)) return null;
		return $this->agent->version();
	}

	public function getRobotAttribute()
	{
		if(is_null($this->agent)) return null;
		return $this->agent->robot();
	}

	public function getMobileAttribute()
	{
		if(is_null($this->agent)) return null;
		return $this->agent->mobile();
	}

	public function getReferrerAttribute()
	{
		if(is_null($this->agent)) return null;
		return $this->agent->referrer();
	}
}