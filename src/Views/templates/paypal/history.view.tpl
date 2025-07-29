<h1>Histórico de Transacciones</h1>

<section class="grid">
    <div class="row">
        <form class="col-12 col-m-8" action="index.php" method="get">
            <div class="flex align-center">
                <div class="col-8 row">
                    <input type="hidden" name="page" value="Checkout_History">

                    <label class="col-3" for="orderid">Orden</label>
                    <input class="col-9" type="text" name="orderid" id="orderid" value="{{orderid}}" />

                    <label class="col-3" for="status">Estado</label>
                    <select class="col-9" name="status" id="status">
                        <option value="ALL" {{status_ALL}}>Todos</option>
                        <option value="COMPLETED" {{status_COMPLETED}}>Completado</option>
                        <option value="PENDING" {{status_PENDING}}>Pendiente</option>
                        <option value="FAILED" {{status_FAILED}}>Fallido</option>
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
                <th>Estado</th>
                <th class="right">Monto</th>
                <th class="center">Moneda</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            {{foreach transactions}}
            <tr>
                <td class="center">{{orderid}}</td>
                <td class="center">{{transdate}}</td>
                <td class="center">
                    <span class="{{statusClass}}">{{transstatus}}</span>
                </td>
                <td class="right">{{amount}}</td>
                <td class="center">{{currency}}</td>
                <td class="center">
                    <a href="index.php?page=Checkout_HistoryDetail&id={{transactionId}}">Ver</a>
                </td>
            </tr>
            {{endfor transactions}}
        </tbody>
    </table>
    {{pagination}}
</section>