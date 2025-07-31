<section class="depth-2 px-4 py-5">
    <h2>{{modeDsc}}</h2>
</section>

<section class="depth-2 px-4 py-4 my-4 grid row">
<form 
    method="POST"
    action="index.php?page=Modules-Orders-Order&mode={{mode}}&id={{orderId}}"
    class="grid col-12 col-m-8 offset-m-2 col-l-6 offset-l-3"
>
    <div class="row my-2">
        <label for="orderId" class="col-12 col-m-4 col-l-3">ID Orden:</label>
        <input 
            type="text"
            name="orderId"
            id="orderId"
            value="{{orderId}}"
            class="col-12 col-m-8 col-l-9"
            {{readonly}}
        />
        <input type="hidden" name="xsrtoken" value="{{xsrtoken}}" />
    </div>

    <div class="row my-2">
        <label class="col-12 col-m-4 col-l-3">Usuario:</label>
        <input 
            type="text"
            value="{{userName}}"
            class="col-12 col-m-8 col-l-9"
            {{readonly}}
        />
    </div>

    <div class="row my-2">
        <label class="col-12 col-m-4 col-l-3">Estado Pago:</label>
        <input 
            type="text"
            value="{{order_status}}"
            class="col-12 col-m-8 col-l-9"
            {{readonly}}
        />
    </div>

    <div class="row my-2">
        <label for="shipping_status" class="col-12 col-m-4 col-l-3">Estado Envío:</label>
        <select 
            name="shipping_status" 
            id="shipping_status" 
            class="col-12 col-m-8 col-l-9"
            {{readonlyShipping}}
        >
            <option value="En Camino" {{shipping_status_CAMINO}}>En camino</option>
            <option value="Listo para recoger" {{shipping_status_RECOGER}}>Listo para recoger</option>
        </select>
        {{foreach errors_shipping_status}}
            <div class="error col-12">{{this}}</div>
        {{endfor errors_shipping_status}}
    </div>

    <div class="row my-2">
        <label class="col-12 col-m-4 col-l-3">Fecha:</label>
        <input 
            type="text"
            value="{{order_date}}"
            class="col-12 col-m-8 col-l-9"
            {{readonly}}
        />
    </div>

    <div class="row">
        <div class="col-12 right">
            <button id="btnCancel" type="button">{{cancelLabel}}</button>
            &nbsp;
            {{if showConfirm}}
                <button class="primary" type="submit">Guardar</button>
            {{endif showConfirm}}
        </div>
    </div>

        {{if errors_global}}
            <div class="row">
                <ul class="col-12">
                {{foreach errors_global}}
                    <li class="error">{{this}}</li>
                {{endfor errors_global}}
                </ul>
            </div>
        {{endif errors_global}}
    </form>
</section>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        document.getElementById("btnCancel")
            .addEventListener("click", (e) => {
                e.preventDefault();
                e.stopPropagation();
                window.location.assign("index.php?page=Modules-Orders-Orders");
            });
    });
</script>