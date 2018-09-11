<?php
/**
 * Unique Email constraint.
 */
namespace Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueLogin.
 *
 * @package Validator\Constraints
 */
class UniqueEmail extends Constraint
{
    /**
     * Message.
     *
     * @var string $message
     */
    public $message = '{{ email }} is not unique email.';

    /**
     * Element id.
     *
     * @var int|string|null $elementId
     */
    public $elementId = null;

    /**
     * User repository.
     *
     * @var null|\Repository\UserRepository $repository
     */
    public $repository = null;
}
