<?php
namespace Ingresse\API\v1\Middleware\Users;

use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use PDO;

class Save implements MiddlewareInterface
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $user = $request->getParsedBody();

        $sql = "INSERT INTO users (login, password, name, email)
                VALUES (:login, :password, :name, :email)";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':login', $user['login'], PDO::PARAM_STR);
        $stmt->bindValue(':password', password_hash($user['password'], PASSWORD_DEFAULT), PDO::PARAM_STR);
        $stmt->bindValue(':name', $user['name'], PDO::PARAM_STR);
        $stmt->bindValue(':email', $user['email'], PDO::PARAM_STR);

        try{
            $saved = $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error saving User: " . PHP_EOL . $e->getMessage());
            $saved = false;
        }

        if ($saved) {
            $response = [
                'success' => true,
                'message' => "User saved successfully",
                'statusCode' => 201,
                'headers' => [
                    'Location' => '/api/v1/users/'.$this->db->lastInsertId()
                ]
            ];
        } else {
            error_log("Error saving User: " . PHP_EOL . $stmt->errorCode() . ": " . $stmt->errorInfo()[2]);
            $response = [
                'success' => false,
                'statusCode' => 500,
                'message' => "An error has ocurred. Try again later"
            ];

        }

        $request = $request->withAttribute('response', $response);

        return $delegate->process($request);
    }

}