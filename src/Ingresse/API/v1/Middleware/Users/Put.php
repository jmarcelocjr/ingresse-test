<?php
namespace Ingresse\API\v1\Middleware\Users;

use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\SimpleCache\CacheInterface;
use PDO;

class Put implements MiddlewareInterface
{
    private $db;

    public function __construct(PDO $db, CacheInterface $cache)
    {
        $this->db = $db;
        $this->cache = $cache;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {

        $id = $request->getAttribute('id');

        $user = $request->getParsedBody();

        $sql = "UPDATE users
                SET login = :login,
                    password = :password,
                    name = :name,
                    email = :email
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':login', $user['login'], PDO::PARAM_STR);
        $stmt->bindValue(':password', password_hash($user['password'], PASSWORD_DEFAULT), PDO::PARAM_STR);
        $stmt->bindValue(':name', $user['name'], PDO::PARAM_STR);
        $stmt->bindValue(':email', $user['email'], PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        try {
            $saved = $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error saving User: " . PHP_EOL . $e->getMessage());
            $saved = false;
        }

        if ($saved) {
            if ($this->cache->has('users')) {
                $this->cache->delete('users');
            }

            $response = [
                'success' => true,
                'message' => "User updated successfully",
                'statusCode' => 200
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
