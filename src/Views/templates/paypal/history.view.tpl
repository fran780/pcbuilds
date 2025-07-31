<h1>Histórico de Ordenes</h1>

<section class="grid">
    <div class="row">
        <form class="col-12 col-m-8" action="index.php" method="get">
            <div class="flex align-center">
                <div class="col-8 row">
                    <input type="hidden" name="page" value="Checkout_History">

                    <label class="col-3" for="orderid">Orden</label>
                    <input class="col-9" type="text" name="orderid" id="orderid" value="{{orderid}}" />

                    <label class="col-3" for="status">Estado de Pedido</label>
                    <select class="col-9" name="status" id="status">
                        <option value="ALL" {{status_ALL}}>Todos</option>
                        <option value="COMPLETED" {{status_COMPLETED}}>Completado</option>
                        <option value="PENDING" {{status_PENDING}}>Pendiente</option>
                        <option value="FAILED" {{status_FAILED}}>Fallido</option>
                    </select>

                    <label class="col-3" for="shippingStatus">Estado de Envío</label>
                    <select class="col-9" name="shippingStatus" id="shippingStatus">
                        <option value="ALL" {{shippingStatus_ALL}}>Todos</option>
                        <option value="SHIPPED" {{shippingStatus_SHIPPED}}>Enviado</option>
                        <option value="PENDING" {{shippingStatus_PENDING}}>Pendiente</option>
                        <option value="DELIVERED" {{shippingStatus_DELIVERED}}>Entregado</option>
                    </select>
                </div>
                <div class="col-4 align-end">
                    <button type="submit">Filtrar</button>
                </div>
            </div>
        </form>
    </div>
</section>

<section class="WWList">
    <table>
        <thead>
            <tr>
                <th>Orden</th>
                <th>Fecha</th>
                <th>Estado de Pedido</th>
                <th>Estado de Envío</th>
                <th class="right">Monto</th>
                <th class="center">Moneda</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            {{foreach orders}}
            <tr>
                <td class="center">{{orderid}}</td>
                <td class="center">{{orderdate}}</td>
                <td class="center">
                    <span class="{{statusClass}}">{{order_status}}</span>
                </td>
                <td class="center">
                    <span class="{{shippingStatusClass}}">{{shipping_status}}</span>
                </td>
                <td class="right">{{total}}</td>
                <td class="center">{{currency}}</td>
                <td class="center">
                    <a href="index.php?page=Checkout_HistoryDetail&id={{orderid}}">Ver</a>
                </td>
            </tr>
            {{endfor orders}}
        </tbody>
    </table>
    {{pagination}}
</section>