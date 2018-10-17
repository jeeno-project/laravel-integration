<?php echo '<?php'; ?>

namespace App\Domain\{{ $domain }};

use Illuminate\Support\ServiceProvider;
use App\Domain\{{ $domain }}\Repositories\{{ $entity }}Repository;
use App\Domain\{{ $domain }}\Repositories\Doctrine{{ $entity }}Repository;

/**
 * Class {{ $domain }}ServiceProvider
 *
 * @package App\Domain\{{ $domain }};
 */
class {{ $domain }}ServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton({{ $entity }}Repository::class, Doctrine{{ $entity }}Repository::class);
    }
}
