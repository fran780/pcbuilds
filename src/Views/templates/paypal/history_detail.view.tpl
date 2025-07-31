<h1>Detalle de Orden #{{order.orderid}}</h1>
<section class="order-info">
    <div class="row">
        <div class="col-6">Fecha: {{order.orderdate}}</div>
        <div class="col-6">Monto Total: {{order.total}} {{order.currency}}</div>
    </div>
    <div class="row">
        <div class="col-6">Estado de Pedido:
            <span class="{{order.statusClass}}">{{order.order_status}}</span>
        </div>
        <div class="col-6">Estado de Envío:
            <span class="{{order.shippingStatusClass}}">{{order.shipping_status}}</span>
        </div>
    </div>
</section>
<section class="WWList">
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th class="center">Cantidad</th>
                <th class="right">Precio</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            {{foreach items}}
            <tr>
                <td>{{nombre_producto}}</td>
                <td class="center">{{cantidad}}</td>
                <td class="right">{{precio}}</td>
                <td class="right">{{subtotal}}</td>
            </tr>
            {{endfor items}}
        </tbody>
    </table>
</section>
<div class="return-button">
    <a href="index.php?page=Checkout_History">Volver</a>
</div>