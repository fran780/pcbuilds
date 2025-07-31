<?php

namespace Controllers\Checkout;

use Controllers\PrivateController;
use Views\Renderer;
use Utilities\Security;
use Utilities\Site;
use Dao\Orders\Orders;
use Dao\Orders\OrderItems;

class HistoryDetail extends PrivateController
{
  private $orderId = 0;
    private $order = [];
    private $items = [];
    private $viewData = [];

    public function run(): void
    {
        $this->orderId = intval($_GET['id'] ?? 0);
        if ($this->orderId <= 0) {
            Site::redirectTo('index.php?page=Checkout_History');
            return;
        }

        $userId = Security::getUserId();
        $order = Orders::getById($this->orderId);
        if (!$order || intval($order['usercod']) !== intval($userId)) {
            Site::redirectTo('index.php?page=Checkout_History');
            return;
        }

        $items = OrderItems::getByOrderId($this->orderId);
        foreach ($items as &$item) {
            $item['precio'] = number_format($item['precio'], 2);
            $item['subtotal'] = number_format($item['precio'] * $item['cantidad'], 2);
        }

        $date = new \DateTime($order['orderdate']);
        $order['orderdate'] = $date->format('Y-m-d');
        $order['statusClass'] = $this->getStatusClass($order['order_status']);
        $order['shippingStatusClass'] = $this->getShippingStatusClass($order['shipping_status']);
        $order['total'] = number_format($order['total'], 2);

        $this->viewData['order'] = $order;
        $this->viewData['items'] = $items;

        Renderer::render('paypal/history_detail', $this->viewData);
    }

    private function getStatusClass(string $status): string
    {
        $map = [
            'COMPLETED' => 'text-success',
            'PENDING' => 'text-warning',
            'FAILED' => 'text-danger'
        ];
        return $map[$status] ?? '';
    }

    private function getShippingStatusClass(string $status): string
    {
        $map = [
            'SHIPPED' => 'text-success',
            'PENDING' => 'text-warning',
            'DELIVERED' => 'text-info'
        ];
        return $map[$status] ?? '';
    }
}