<?php

namespace App\Security\User;

use App\Entity\User;
use LightSaml\ClaimTypes;
use LightSaml\Model\Protocol\Response;

class UserMapper {
    const ROLES_ASSERTION_NAME = 'urn:roles';

    /**
     * @param Response $response
     * @param string[] $valueAttributes
     * @param string[] $valuesAttributes
     * @return array
     */
    private function transformResponseToArray(Response $response, array $valueAttributes, array $valuesAttributes) {
        $result = [ ];

        foreach($valueAttributes as $valueAttribute) {
            $result[$valueAttribute] = $this->getValue($response, $valueAttribute);
        }

        foreach($valuesAttributes as $valuesAttribute) {
            $result[$valuesAttribute] = $this->getValues($response, $valuesAttribute);
        }

        return $result;
    }

    /**
     * @param User $user
     * @param Response|array[] Either a SAMLResponse or an array (keys: SAML Attribute names, values: corresponding values)
     * @return User
     */
    public function mapUser(User $user, $data) {
        if(is_array($data)) {
            return $this->mapUserFromArray($user, $data);
        } else if($data instanceof Response) {
            return $this->mapUserFromResponse($user, $data);
        }
    }

    private function mapUserFromResponse(User $user, Response $response) {
        return $this->mapUserFromArray($user, $this->transformResponseToArray(
            $response,
            [
                ClaimTypes::GIVEN_NAME,
                ClaimTypes::SURNAME,
                ClaimTypes::EMAIL_ADDRESS
            ],
            [
                static::ROLES_ASSERTION_NAME
            ]
        ));
    }

    /**
     * @param User $user User to populate data to
     * @param Response $response SAML response
     * @return User
     */
    private function mapUserFromArray(User $user, array $data) {
        $firstname = $data[ClaimTypes::GIVEN_NAME];
        $lastname = $data[ClaimTypes::SURNAME];
        $email = $data[ClaimTypes::EMAIL_ADDRESS];
        $roles = $data[static::ROLES_ASSERTION_NAME];

        if(!is_array($roles)) {
            $roles = [ $roles ];
        }

        if(count($roles) === 0) {
            $roles = [ 'ROLE_USER' ];
        }
        $user
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setEmail($email)
            ->setRoles($roles);

        return $user;
    }

    private function getValue(Response $response, $attributeName) {
        return $response->getFirstAssertion()->getFirstAttributeStatement()
            ->getFirstAttributeByName($attributeName)->getFirstAttributeValue();
    }

    private function getValues(Response $response, $attributeName) {
        return $response->getFirstAssertion()->getFirstAttributeStatement()
            ->getFirstAttributeByName($attributeName)->getAllAttributeValues();
    }
}