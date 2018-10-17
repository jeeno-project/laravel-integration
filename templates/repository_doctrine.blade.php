<?php echo '<?php'; ?>

namespace App\Domain\{{ $domain }}\Repositories;


use App\Domain\{{ $domain }}\Entities\{{ $entity }};
use Jeeno\LaravelIntegration\Repository\DoctrineEntityRepository;

/**
 * Interface {{ $entity }}Repository
 *
 * @package App\Domain\{{ $domain }}\Repository
 */
class Doctrine{{ $entity }}Repository extends DoctrineEntityRepository implements {{ $entity }}Repository
{
    /**
    * @return string
    */
    public function getEntityClass(): string
    {
        return {{ $entity }}::class;
    }
}