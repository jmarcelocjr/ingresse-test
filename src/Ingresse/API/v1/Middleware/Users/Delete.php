<?php
namespace Ingresse\API\v1\Middleware\Users;

use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\SimpleCache\CacheInterface;
use PDO;

class Delete implements MiddlewareInterface
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

        $sql = "DELETE FROM users WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        try {
            $deleted = $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error deleting User: " . PHP_EOL . $e->getMessage());
            $deleted = false;
        }

        if ($deleted) {
            if ($this->cache->has('users')) {
                $this->cache->delete('users');
            }

            $response = [
                'success' => true,
                'message' => "User deleted successfully",
                'statusCode' => 200,
            ];
        } else {
            error_log("Error deleting User: " . PHP_EOL . $stmt->errorCode() . ": " . $stmt->errorInfo()[2]);
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
