<?php echo '<?php'; ?>


namespace App\Domain\{{ $domain }}\Controllers;

use Jeeno\LaravelIntegration\Controller\CrudController;
use App\Domain\{{ $domain }}\Entities\{{ $entity }};

/**
 * Class {{ $entity }}Controller
 *
 * @package App\Http\Controllers
 */
class {{ $entity }}Controller extends CrudController
{
    /**
     * @return string
     */
    static protected function getEntityClass(): string
    {
        return {{ $entity }}::class;
    }

    /**
     * @return string
     */
    static protected function getSlug(): string
    {
        return "{{  str_slug(str_plural($entity)) }}";
    }
}