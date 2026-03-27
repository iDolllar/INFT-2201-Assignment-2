<?php
/**
 * Assignment 2 – Authentication, Authorization, and RBAC
 * Student: Aadithkeshev Anushayamunaithuraivan
 * Course: INFT-2201
 * Date: March 27th, 2026
 */
namespace Application;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Verifier
{
    public $userId = null;
    public $role = null;

    public function decode($jwt) 
    {   
        if (!empty($jwt)) {
            $jwt = trim($jwt);

            if (substr($jwt, 0, 7) === 'Bearer ') {
                $jwt = substr($jwt, 7);
            }

            try {
                $token = JWT::decode(
                    $jwt,
                    new Key("a2_9f4c1d7e3b8a6f2c5d1e9b7a3c8f4e", 'HS256')
                );

                $this->userId = $token->userId ?? null;
                $this->role = $token->role ?? null;
            } catch (\Throwable $e) {
                $this->userId = null;
                $this->role = null;
            }
        }
    }
}