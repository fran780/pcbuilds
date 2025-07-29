<section class="container-m row px-4 py-4">
    <h1>{{FormTitle}}</h1>
</section>

<section class="container-m row px-4 py-4">
    {{with txn}}
    <form class="col-12 col-m-8 offset-m-2">
        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="orderid">Orden</label>
            <input class="col-12 col-m-9" type="text" id="orderid" readonly value="{{orderid}}" />
        </div>

        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="transdate">Fecha</label>
            <input class="col-12 col-m-9" type="text" id="transdate" readonly value="{{transdate}}" />
        </div>

        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="transstatus">Estado</label>
            <input class="col-12 col-m-9" type="text" id="transstatus" readonly value="{{transstatus}}" />
        </div>

        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="amount">Monto</label>
            <input class="col-12 col-m-9" type="text" id="amount" readonly value="{{amount}}" />
        </div>

        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="currency">Moneda</label>
            <input class="col-12 col-m-9" type="text" id="currency" readonly value="{{currency}}" />
        </div>

    </form>
    {{endwith txn}}
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