<div class="container">
  <div class="left">
    <img src="public/imgs/hero/IMG_HEADER_PRODUCT.jpg" alt="Laptop ROG" />
  </div>
  <div class="right">
    <nav>
      <button class="btn" onclick="window.location.href='index.php'">Home</button>
      <button class="btn" onclick="window.location.href='index.php?page=contactanos'">Contacto</button>
    </nav>
    <h1>Nuestra Tienda</h1>
  </div>
</div>

<section class="grid">
  <div class="row">
    <form class="col-12 col-m-8" action="index.php" method="get">
      <div class="flex align-center">
        <div class="col-8 row">
          <input type="hidden" name="page" value="Tienda">
          <label class="col-3" for="partialName">Nombre</label>
          <input class="col-9" type="text" name="partialName" id="partialName" value="{{partialName}}" />
          
          <label class="col-3" for="id_marca">Marca</label>
          <select class="col-9" name="id_marca" id="id_marca">
            <option value="">-- Todas las marcas --</option>
            {{foreach brands}}
            <option value="{{id_marca}}" {{brand_id_marca}}>{{nombre_marca}}</option>
            {{endfor brands}}
          </select>

          <label class="col-3" for="id_categoria">Categoría</label>
          <select class="col-9" name="id_categoria" id="id_categoria">
            <option value="">-- Todas las categorías --</option>
            {{foreach categories}}
            <option value="{{id_categoria}}" {{category_id_categoria}}>{{nombre_categoria}}</option>
            {{endfor categories}}
          </select>

        </div>
        <div class="col-4 align-end">
          <button type="submit">Filtrar</button>
        </div>
      </div>
    </form>
  </div>
</section>
<div class="product-list">
  {{foreach products}}
  <div class="product" data-id_producto="{{id_producto}}">
    <img src="{{imagen}}" alt="{{nombre_producto}}">
    <h2>{{nombre_producto}}</h2>
    <span class="badge">{{nombre_marca}}</span>
    <p>{{descripcion}}</p>
    <span class="price">L.{{precio}}.00</span>
    <button class="add-to-cart">Ver Producto</button>
  </div>
  {{endfor products}}
  {{pagination}}
</div>