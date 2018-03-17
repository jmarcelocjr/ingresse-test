<?php
namespace Ingresse\API\v1\Middleware\Users;

use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\SimpleCache\CacheInterface;
use PDO;

class Get implements MiddlewareInterface
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

        if ($user = $this->getUserInCache($id)) {
            $response = [
                'success' => $result,
                'statusCode' => 200,
                'data' => $this->cache->get('users')
            ];

            $request = $request->withAttribute("response", $response);

            return $delegate->process($request);
        }


        $sql = "SELECT * FROM users WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $result = $stmt->execute();

        if ($result) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $users = $this->cache->get('users', []);
            $users[] = $user;
            $this->cache->set('users', $users);

            $response = [
                'success' => $result,
                'statusCode' => 200,
                'data' => $user
            ];
        } else {
            $response = [
                'success' => $result,
                'statusCode' => 500,
                'message' => "Code: ".$stmt->errorCode()." - Msg: ".print_r($stmt->errorInfo(), true)
            ];

            error_log("Error Get Users: ".$response['msg']);
        }

        $request = $request->withAttribute("response", $response);

        return $delegate->process($request);
    }

    private function getUserInCache($id)
    {
        $users = $this->cache->get('users', []);

        $user = array_filter($users, function ($user) use ($id) {
            return $user['id'] == $id;
        });

        return $user[0] ?? false;
    }
}
