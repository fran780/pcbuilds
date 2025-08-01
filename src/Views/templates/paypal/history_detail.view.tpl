<section class="container-m row px-4 py-4">
    <h1>Detalle de Orden #{{order.orderid}}</h1>
</section>

<section class="container-m row px-4 py-4">
    {{with order}}
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
    {{endwith order}}
</section>

<section class="container-m row px-4 py-4">
    <div class="col-12 col-m-8 offset-m-2">
        <h2>Productos</h2>
        <table class="col-12">
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
