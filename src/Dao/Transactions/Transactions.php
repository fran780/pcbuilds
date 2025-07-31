<?php
namespace Dao\Transactions;

use Dao\Table;

class Transactions extends Table
{
    public static function addTransaction(
    int $usercod,
    string $orderId,
    string $status,
    float $amount,
    string $currency,
    string $orderJson
    ) {
        $sqlstr = "INSERT INTO transactions 
            (usercod, orderid, transdate, transstatus, amount, currency, orderjson)
            VALUES (:usercod, :orderid, NOW(), :transstatus, :amount, :currency, :orderjson);";

        $conn = self::getConn(); // Usa tu método para obtener la conexión PDO
        $stmt = $conn->prepare($sqlstr);
        $stmt->execute([
            'usercod' => $usercod,
            'orderid' => $orderId,
            'transstatus' => $status,
            'amount' => $amount,
            'currency' => $currency,
            'orderjson' => $orderJson
        ]);

        return $conn->lastInsertId(); // Este es el transactionId autogenerado
    }



    public static function getByUser(int $usercod)
    {
        $sqlstr = "SELECT transactionId, orderid, transdate, transstatus, amount, currency FROM transactions WHERE usercod = :usercod ORDER BY transdate DESC;";
        return self::obtenerRegistros($sqlstr, ['usercod' => $usercod]);
    }
     public static function getTransactions(
        int $usercod,
        string $orderId = "",
        string $status = "",
        int $page = 0,
        int $itemsPerPage = 10
    ) {
        $baseSql = "SELECT transactionId, orderid, transdate, transstatus, amount, currency FROM transactions WHERE usercod = :usercod";
        $baseSqlCount = "SELECT COUNT(*) as total FROM transactions WHERE usercod = :usercod";
        $conditions = [];
        $params = ["usercod" => $usercod];

        if ($orderId !== "") {
            $conditions[] = "orderid LIKE :orderid";
            $params["orderid"] = "%" . $orderId . "%";
        }

        if (in_array($status, ["COMPLETED", "PENDING", "FAILED"])) {
            $conditions[] = "transstatus = :transstatus";
            $status = strtoupper($status);
            $params["transstatus"] = $status;
        }

        if (count($conditions) > 0) {
            $where = " AND " . implode(" AND ", $conditions);
            $baseSql .= $where;
            $baseSqlCount .= $where;
        }

        $total = intval(self::obtenerUnRegistro($baseSqlCount, $params)["total"]);
        $pagesCount = max(ceil($total / $itemsPerPage), 1);
        if ($page > $pagesCount - 1) {
            $page = $pagesCount - 1;
        }
        if ($page < 0) {
            $page = 0;
        }

        $baseSql .= " ORDER BY transdate DESC LIMIT " . ($page * $itemsPerPage) . ", " . $itemsPerPage;

        $transactions = self::obtenerRegistros($baseSql, $params);

        return [
            "transactions" => $transactions,
            "total" => $total,
            "page" => $page,
            "itemsPerPage" => $itemsPerPage
        ];
    }
    
       public static function getById(int $transactionId)
    {
        $sqlstr = "SELECT * FROM transactions WHERE transactionId = :transactionId;";
        return self::obtenerUnRegistro($sqlstr, ['transactionId' => $transactionId]);
    }

}
?>