<?php
namespace Validator;

use Silex\Application;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\HttpFoundation\Request;


class ReservationFormValidator
{
    private $request;

    private $app;

    private $constraint;

    /**
     * RegisterFormValidator constructor.
     * @param $data
     */
    public function __construct(Request $request, Application $app)
    {
        $this->request = $request;
        $this->app = $app;

        $this->constraint = new Collection(
            array(
                'car_id' => new Constraints\NotBlank(),
                'place_id' => new Constraints\NotBlank(),
                'date' => array(new Constraints\NotBlank(), new Constraints\Date())
            )
        );
    }

    public function validate() {
        return $this->app['validator']->validate($this->request->request->all(), $this->constraint);
    }
}