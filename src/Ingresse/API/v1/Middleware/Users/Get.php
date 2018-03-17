<?php
namespace Ingresse\API\v1\Middleware\Users;

use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use PDO;

class Get implements MiddlewareInterface
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $orderBy = $request->getQueryParams()['orderBy'] ?? 'id';
        $mode = $request->getQueryParams()['mode'] ?? 'asc';

        $sql = "SELECT * FROM users ORDER BY :orderBy :mode";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(":orderBy", $orderBy, PDO::PARAM_STR);
        $stmt->bindValue(":mode", $mode, PDO::PARAM_STR);
        
        $result = $stmt->execute();

        if ($result) {
            $response = [
                'success' => $result,
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
        } else {
            $response = [
                'success' => $result,
                'msg' => "Code: ".$stmt->errorCode()." - Msg: ".print_r($stmt->errorInfo(), true)
            ];

            error_log("Error Get Users: ".$response['msg']);
        }

        $request = $request->withAttribute("response", $response);

        return $delegate->process($request);
    }

}