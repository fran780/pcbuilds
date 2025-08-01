{{with order}}
<section class="container-m row px-4 py-4">
    <h1 class="col-12">Detalle de Orden #{{orderid}}</h1>
</section>

<section class="container-m row px-4 py-4">
    <form class="col-12 col-m-8 offset-m-2">
        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="orderdate">Fecha</label>
            <input class="col-12 col-m-9" type="text" id="orderdate" readonly value="{{orderdate}}" />
        </div>

        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="total">Monto Total</label>
            <input class="col-12 col-m-9" type="text" id="total" readonly value="{{total}} {{currency}}" />
        </div>

        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="order_status">Estado de Pedido</label>
            <input class="col-12 col-m-9 {{statusClass}}" type="text" id="order_status" readonly value="{{order_status}}" />
        </div>

        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="shipping_status">Estado de Envío</label>
            <input class="col-12 col-m-9 {{shippingStatusClass}}" type="text" id="shipping_status" readonly value="{{shipping_status}}" />
        </div>
    </form>
</section>
{{endwith order}}

<section class="container-m row px-4 py-4">
    <div class="col-12 col-m-8 offset-m-2">
        <h2>Productos</h2>
        <table class="order-table col-12">
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
    </div>
</section>

<section class="container-m row px-4 py-4">
    <div class="col-12 col-m-8 offset-m-2 align-end">
        <button class="col-12 col-m-2" type="button" id="btnCancelar">Regresar</button>
    </div>
</section>

<style>
    /* Estilo general de la tabla */
    .order-table {
        border-collapse: collapse;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0px 2px 8px rgba(0,0,0,0.05);
    }

    /* Encabezado */
    .order-table thead {
        background: #f5f5f5;
    }

    .order-table th {
        padding: 12px;
        font-weight: 600;
        font-size: 14px;
        text-align: left;
        color: #333;
        border-bottom: 2px solid #e0e0e0;
        font-weight: bold;
        font-size: 18px;
    }

    /* Filas */
    .order-table td {
        padding: 10px 12px;
        font-size: 16px;
        color: rgb(59, 56, 56);
        border-bottom: 1px solid #eee;
    }

    /* Zebra striping */
    .order-table tbody tr:nth-child(even) {
        background: #fafafa;
    }

    /* Alineaciones */
    .order-table .center {
        text-align: center;
    }

    .order-table .right {
        text-align: right;
    }

    /* Hover fila */
    .order-table tbody tr:hover {
        background: #f0f8ff;
        transition: background 0.2s;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const btnCancelar = document.getElementById("btnCancelar");
        btnCancelar.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();
            window.location.assign("index.php?page=Checkout_History");
        });
    });
</script>
