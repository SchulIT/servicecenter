<?php

namespace App\Security\User;

use LightSaml\SpBundle\Security\Authentication\Token\SamlSpResponseToken;
use LightSaml\SpBundle\Security\User\AttributeMapperInterface;

class AttributeMapper implements AttributeMapperInterface {

    const INTERAL_ID_ASSERTION_NAME = 'urn:internal-id';

    public function getAttributes(SamlSpResponseToken $token) {
        return [
            'name_id' => $token->getResponse()->getFirstAssertion()->getSubject()->getNameID()->getValue(),
            'student_id' => $this->getValue($token, static::INTERAL_ID_ASSERTION_NAME),
            'services' => $this->getServices($token)
        ];
    }

    private function getServices(SamlSpResponseToken $token) {
        $values = $this->getValues($token, 'urn:services');

        $services = [ ];

        foreach($values as $value) {
            $services[] = json_decode($value);
        }

        return $services;
    }

    private function getValue(SamlSpResponseToken $token, $attributeName) {
        return $token->getResponse()->getFirstAssertion()->getFirstAttributeStatement()
            ->getFirstAttributeByName($attributeName)->getFirstAttributeValue();
    }

    private function getValues(SamlSpResponseToken $token, $attributeName) {
        return $token->getResponse()->getFirstAssertion()->getFirstAttributeStatement()
            ->getFirstAttributeByName($attributeName)->getAllAttributeValues();
    }
}