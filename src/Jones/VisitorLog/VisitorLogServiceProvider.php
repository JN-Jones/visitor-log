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

			$page = Request::path();
			$ignore = Config::get('visitor-log::ignore');
			if(is_array($ignore) && in_array($page, $ignore))
			    //We ignore this site
			    return;

			$visitor = Visitor::getCurrent();
			
			if(!$visitor)
			{
				//We need to add a new user
				$visitor = new Visitor;
				$visitor->ip = Request::getClientIp();
				$visitor->useragent = Request::server('HTTP_USER_AGENT');
				$visitor->sid = str_random(25);
			}

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

			//Save/Update the rest
			$visitor->user = $user;
			$visitor->page = $page;
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