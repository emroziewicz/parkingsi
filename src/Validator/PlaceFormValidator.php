<?php
namespace Validator;

use Silex\Application;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\HttpFoundation\Request;


class PlaceFormValidator
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
                'name' => new Constraints\NotBlank(),
            )
        );
    }

    public function validate() {
        return $this->app['validator']->validate($this->request->request->all(), $this->constraint);
    }
}