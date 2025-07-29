<section class="container-l">
  <section class="depth-4">
    <h1>Carretilla de Compras</h1>
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
      <span class="col-2 center">{{crrprc}}</span>
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
      <span class="col-2 center">{{subtotal}}</span>
    </div>
    {{endfor carretilla}}

    <!-- Total -->
    <div class="row total-row">
      <span class="col-10 right bold">Total</span>
      <span class="col-2 center bold">{{total}}</span>
    </div>

    <!-- Botón Checkout -->
    <div class="checkout-btn-container">
      <a href="{{botonUrl}}" class="checkout-btn">
        <i class="fas fa-{{botonIcono}}"></i>{{botonTexto}}
      </a>
    </div>
  </section>
</section>


<style>
  .container-l {
    max-width: 1000px;
    margin: 2rem auto;
    padding: 1.5rem;
    background-color: #ffffff;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
  }

  .depth-4 h1 {
    font-size: 2rem;
    color: black;
    text-align: center;
    margin-bottom: 1.5rem;
  }

  .grid {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  .row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    background-color: #f8f9fa;
    border-radius: 6px;
    padding: 0.8rem;
  }

  .row.border-b {
    border-bottom: 1px solid #dee2e6;
  }

  /* Columnas */
  .col-1 {
    flex: 1;
  }

  .col-2 {
    flex: 2;
  }

  .col-3 {
    flex: 3;
  }

  .col-4 {
    flex: 4;
  }

  .col-12 {
    flex: 12;
  }

  .offset-7 {
    margin-left: auto;
  }

  .center {
    text-align: center;
  }

  .right {
    text-align: right;
  }

  /* Botones de cantidad (+ y -) */
  .circle {
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    font-size: 1.2rem;
    font-weight: bold;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
  }

  .circle:hover {
    background-color: #0056b3;
  }

  .quantity-controls {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
  }

  .quantity-value {
    font-size: 1rem;
    font-weight: 500;
    min-width: 24px;
    text-align: center;
  }

  /* Total */
  .row:last-of-type span {
    font-size: 1.2rem;
    font-weight: bold;
  }

  /* Botón de Checkout */
  .checkout-btn-container {
    display: flex;
    justify-content: flex-end;
    margin-top: 1rem;
  }

  .checkout-btn {
    background-color: #28a745;
    color: white !important;
    padding: 10px 18px;
    font-size: 1em;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }

  .checkout-btn:hover {
    background-color: #218838;
  }

  .bold {
  font-weight: bold;
}
</style>