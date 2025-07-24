<div class="container">
    <div class="left">
        <img src="public/imgs/hero/IMG_HEADER_PRODUCT.jpg" alt="Laptop ROG" />
    </div>
    <div class="right">
        <nav>
            <button class="btn">Home</button>
            <button class="btn">Contacto</button>
        </nav>
        <h1>Nuestra Tienda</h1>
    </div>
</div>

<div class="product-list">
    <h2>Nuestros Productos</h2>
    {{foreach getAllProducts}}
    <div class="product" data-id_producto="{{id_producto}}">
        <img src="{{imagen}}" alt="{{nombre_producto}}">
        <h2>{{nombre_marca}} {{nombre_producto}}</h2>
        <p>{{descripcion}}</p>
        <span class="price">L.{{precio}}.00</span>
        <button class="add-to-cart">Ver Producto</button>
    </div>
    {{endfor getAllProducts}}
</div>