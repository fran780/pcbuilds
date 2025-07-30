
<section class="container-l">
  <section class="depth-4">
    <i class="fa-solid fa-cart-plus"></i>
    <h1 class="title">Carretilla de Compras</h1>

  </section>

  <section class="grid">
    <!-- Encabezado -->
    <div class="row border-b header-row">
      <span class="col-1 center bold">#</span>
      <span class="col-4 center bold">Item</span>
      <span class="col-2 center bold">Precio</span>
      <span class="col-3 center bold">Cantidad</span>
      <span class="col-2 center bold">Subtotal</span>
    </div>

    <!-- Productos -->
    {{foreach carretilla}}
    <div class="row border-b">
      <span class="col-1 center">{{row}}</span>
      <span class="col-4 center">{{nombre_producto}}</span>
      <span class="col-2 center">USD {{crrprc}}</span>
      <span class="col-3 center">
        <form action="index.php?page=Carretilla" method="post">
        <div class="quantity-controls">
          <input type="hidden" name="productId" value="{{id_producto}}" />
          
          <button type="submit" name="removeOne" value="1" class="circle">−</button>
          <span class="quantity-value">{{crrctd}}</span>
          <button type="submit" name="addOne" value="1" class="circle">+</button>
        </div>
      </form>

      </span>
      <span class="col-2 center">USD {{subtotal}}</span>
    </div>
    {{endfor carretilla}}

    <!-- Total -->
    <div class="row total-row">
      <span class="col-10 right bold">Total</span>
      <span class="col-2 center bold">USD {{total}}</span>
    </div>

    <!-- Botón Checkout -->
    <div class="checkout-btn-container">
      <a href="{{botonUrl}}" class="checkout-btn">
        <i class="fas fa-{{botonIcono}}"></i>{{botonTexto}}
      </a>
    </div>
  </section>
</section>
