<?php
namespace Ingresse\API\v1\Middleware\Users;

use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\SimpleCache\CacheInterface;
use PDO;

class GetAll implements MiddlewareInterface
{
    private $db;

    public function __construct(PDO $db, CacheInterface $cache)
    {
        $this->db = $db;
        $this->cache = $cache;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        if ($this->cache->has('users')) {
            $response = [
                'success' => $result,
                'statusCode' => 200,
                'data' => $this->cache->get('users')
            ];

            $request = $request->withAttribute("response", $response);

            return $delegate->process($request);
        }

        $orderBy = $request->getQueryParams()['orderBy'];
        $orderBy = in_array($orderBy, ['id', 'name', 'email', 'password', 'login']) ? $orderBy : 'id';

        $mode = $request->getQueryParams()['mode'] ?? 'asc';
        $mode = in_array($mode, ['asc', 'desc']) ? $mode : 'asc';

        $sql = "SELECT * FROM users ORDER BY {$orderBy} {$mode}";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(":orderBy", $orderBy, PDO::PARAM_STR);
        $stmt->bindValue(":mode", $mode, PDO::PARAM_STR);

        $result = $stmt->execute();

        if ($result) {
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($users)) {
                $this->cache->set('users', $users);
            }

            $response = [
                'success' => $result,
                'statusCode' => 200,
                'data' => $users
            ];
        } else {
            $response = [
                'success' => $result,
                'statusCode' => 500,
                'message' => "Code: ".$stmt->errorCode()." - Msg: ".print_r($stmt->errorInfo(), true)
            ];

            error_log("Error Get Users: ".$response['message']);
        }

        $request = $request->withAttribute("response", $response);

        return $delegate->process($request);
    }
}
