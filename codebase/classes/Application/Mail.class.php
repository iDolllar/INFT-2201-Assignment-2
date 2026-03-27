<?php
/**
 * Assignment 2 – Authentication, Authorization, and RBAC
 * Student: Aadithkeshev Anushayamunaithuraivan
 * Course: INFT-2201
 * Date: March 27th, 2026
 */
namespace Application;

use PDO;

class Mail
{
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createMail($name, $message, $userId)
    {
        // Save mail using authenticated user's userId
        $stmt = $this->db->prepare("
            INSERT INTO mail (name, message, userId)
            VALUES (:name, :message, :userId)
        ");

        $stmt->execute([
            'name' => $name,
            'message' => $message,
            'userId' => $userId
        ]);

        return $this->db->lastInsertId();
    }

    public function listMail($userId, $role) 
    {
        // Admin gets all mail, user gets only their own
        if ($role === 'admin') {
            $stmt = $this->db->prepare("
                SELECT id, name, message, userId
                FROM mail
                ORDER BY id
            ");
            $stmt->execute();
        } else {
            $stmt = $this->db->prepare("
                SELECT id, name, message, userId
                FROM mail
                WHERE userId = :userId
                ORDER BY id
            ");
            $stmt->execute([
                'userId' => $userId
            ]);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}