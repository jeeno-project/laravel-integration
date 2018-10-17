<?php echo '<?php'; ?>

namespace App\Domain\{{ $domain }}\Entities;

use Doctrine\ORM\Mapping as ORM;
use Jeeno\Core\Exception\ValidationException;
use Jeeno\Core\Entity\AbstractEntity;

/**
 * Class {{ $entity }}
 *
 * @package App\Domain\{{ $domain }}\Entities;
 *
 * @ORM\Entity
 * @ORM\Table(name="{{ $table }}")
 */
class {{ $entity }} extends AbstractEntity
{
    /**
    * @var string
    *
    * @ORM\Column(name="my_property", type="string")
    *
    */
    private $myProperty;


    /**
    * {{ $entity }} constructor.
    *
    * @param string $myProperty
    *
    * @throws ValidationException
    */
    public function __construct(string $myProperty) {
        $this->setMyProperty($myProperty);
    }

    /**
    * @return string
    */
    public function getMyProperty():string {
        return $this->myProperty;
    }

    /**
    * @param string $myProperty
    *
    * @throws ValidationException
    */
    private function setMyProperty(string $myProperty):void {
        if (empty($myProperty)) {
            throw new ValidationException('myProperty', 'Empty');
        }
    }
}