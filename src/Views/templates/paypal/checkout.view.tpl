<section class="container-l">
  <section class="depth-4">
    <i class="fa-solid fa-cart-plus"></i>
    <h1 class="title">Carrito de Compras</h1>
  </section>

  <section class="grid">
    <div class="row border-b header-row">
      <span class="col-1 center bold">#</span>
      <span class="col-4 center bold">Item</span>
      <span class="col-2 center bold">Precio</span>
      <span class="col-3 center bold">Cantidad</span>
      <span class="col-2 center bold">Subtotal</span>
    </div>

    {{foreach carretilla}}
    <div class="row border-b">
      <span class="col-1 center">{{row}}</span>
      <span class="col-4 center">{{nombre_producto}}</span>
      <span class="col-2 center">USD {{crrprc}}</span>
      <span class="col-3 center">{{crrctd}}</span>
      <span class="col-2 center">USD {{subtotal}}</span>
    </div>
    {{endfor carretilla}}

    <div class="row total-row">
      <span class="col-3 offset-7 center bold">Total</span>
      <span class="col-2 center bold">USD {{total}}</span>
    </div>

    <div class="checkout-actions">
      <form action="index.php?page=checkout_checkout" method="post" class="col-6">
        <button type="submit" name="cancelPurchase" class="btn-cancel">
          <i class="fas fa-times-circle"></i> Cancelar compra
        </button>
      </form>
      <form action="index.php?page=checkout_checkout" method="post" class="col-6 right">
        <button type="submit" class="btn-confirm">
          <i class="fas fa-check-circle"></i> Realizar pedido
        </button>
      </form>
    </div>
  </section>
</section>
