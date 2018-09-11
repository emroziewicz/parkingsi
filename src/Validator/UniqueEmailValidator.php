<?php
/**
 * Unique Category validator.
 */
namespace Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class UniqueCategoryValidator.
 *
 * @package Validator\Constraints
 */
class UniqueEmailValidator extends ConstraintValidator
{

    /**
     * Class UniqueCategoryValidator
     *
     * @param mixed      $value      Value
     * @param Constraint $constraint Constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint->repository) {
            return;
        }

        $result = $constraint->repository->findForUniqueEmail(
            $value,
            $constraint->elementId
        );

        if ($result && count($result)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ email }}', $value)
                ->addViolation();
        }
    }
}
