<?php
/**
 * Assignment 2 – Authentication, Authorization, and RBAC
 * Student: Aadithkeshev Anushayamunaithuraivan
 * Course: INFT-2201
 * Date: March 27th, 2026
 */
require __DIR__ . '/../../../autoload.php';

use Application\Mail;
use Application\Database;
use Application\Page;
use Application\Verifier;

$database = new Database('prod');
$page = new Page();
$mail = new Mail($database->getDb());

$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
// Verify JWT from Authorization header
// Reject request if token is missing or invalid
$verifier = new Verifier();
$verifier->decode($authHeader);

if (empty($authHeader) || empty($verifier->userId) || empty($verifier->role)) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (
        is_array($data) &&
        array_key_exists('name', $data) &&
        array_key_exists('message', $data)
    ) {
        $id = $mail->createMail(
            $data['name'],
            $data['message'],
            $verifier->userId
        );

        $page->item(["id" => $id]);
    } else {
        $page->badRequest();
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $page->item($mail->listMail($verifier->userId, $verifier->role));
} else {
    $page->badRequest();
}