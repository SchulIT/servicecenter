<?php

namespace App\Validator;

use App\Entity\Device;
use App\Entity\DeviceType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class SameProblemTypeValidator extends ConstraintValidator {
    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void {
        if (!$constraint instanceof SameProblemType) {
            throw new UnexpectedTypeException($constraint, SameProblemType::class);
        }

        if (!is_iterable($value)) {
            throw new UnexpectedTypeException($value, 'iterable');
        }

        /** @var DeviceType|null $type */
        $type = null;

        /** @var Device $device */
        foreach ($value as $device) {
            if (!$device instanceof Device) {
                throw new UnexpectedTypeException($device, DeviceType::class);
            }

            if ($type === null) {
                $type = $device->getType();
            } else {
                if ($type->getId() !== $device->getType()->getId()) {
                    $this->context->buildViolation($constraint->message)
                        ->addViolation();
                }
            }
        }
    }
}