<?php namespace Jones\VisitorLog;

use Illuminate\Support\ServiceProvider;
use Jones\VisitorLog\Visitor;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Cartalyst\Sentry\Facades\Laravel\Sentry;

class VisitorLogServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('jones/visitor-log');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['router']->before(function ($request) {
			// First clear out all "old" visitors
			Visitor::clear();

			$ip = Request::getClientIp();
			
			//Was this user online before?
			$visitor = Visitor::where('ip', '=', $ip)->first();

			$user = null;
			$usermodel = strtolower(Config::get('visitor-log::usermodel'));
			if(($usermodel == "auth" || $usermodel == "laravel") && Auth::check())
			{
				$user = Auth::user()->id;
			}

			if($usermodel == "sentry" && class_exists('Cartalyst\Sentry\SentryServiceProvider') && Sentry::check())
			{
				$user = Sentry::getUser()->id;
			}

   			if(!$visitor)
			{
				//We need to add a new user

				$visitor = new Visitor;
				
				$visitor->ip = $ip;
			}

			//Save/Update the rest
			$visitor->user = $user;
			$visitor->useragent = Request::server('HTTP_USER_AGENT');
			$visitor->page = Request::path();
			$visitor->save();
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('visitor');
	}

}