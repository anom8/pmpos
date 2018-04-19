<?php namespace Vansteen\Sendinblue;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Sendinblue\Mailin;

class SendinblueServiceProvider extends ServiceProvider {

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
		$app = app();
		$version = $app::VERSION;

		if ($version[0] == 4)
		{
			$this->package('vansteen/sendinblue');
		}
		elseif ($version[0] == 5)
		{
			$this->publishes([
				__DIR__.'/../../config/config.php' => config_path('sendinblue.php'),
			]);
		}
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('sendinblue_wrapper', function() {

			$app = app();
			$version = $app::VERSION;

			if ($version[0] == 4)
			{
				$ml = new Mailin('https://api.sendinblue.com/v2.0', Config::get('sendinblue::apikey'));
				return new SendinblueWrapper($ml);
			}
			elseif ($version[0] == 5)
			{
				$ml = new Mailin('https://api.sendinblue.com/v2.0', Config::get('sendinblue.apikey'));
				return new SendinblueWrapper($ml);
			}

		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('sendinblue_wrapper');
	}

}
