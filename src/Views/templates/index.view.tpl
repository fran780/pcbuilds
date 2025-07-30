<div class="container">
    <div class="banner">
        <h1>¡Te llevamos al siguiente nivel!</h1>
        <p>Nuestros expertos legales trabajan con dedicación para ofrecer soluciones personalizadas y efectivas.</p>      
        
        <button class="shop-button" onclick="window.location.href='index.php?page=Tienda'">Tienda</button>

    </div>
    <div class="image">
        <img src="public/imgs/hero/imagen_baner.jpg" alt="Configuración de escritorio gamer">
    </div>
</div>

<div class="product-list">
    <h2>Nuestros Productos</h2>
    {{foreach getBestProducts}}
    <div class="product" data-id_producto="{{id_producto}}">
        <img src="{{imagen}}" alt="{{nombre_producto}}">
        <h2>{{nombre_marca}} {{nombre_producto}}</h2>
        <p>{{descripcion}}</p>
        <span class="price">USD {{precio}}</span>
        <button class="add-to-cart" onclick="window.location.href='index.php?page=Tienda'">Ver Productos</button>
    </div>
    {{endfor getBestProducts}}
</div>

<section class="exclusive-brands">
    <h2>Marcas <span>EXCLUSIVAS</span></h2>
    <div class="brand-grid">
        <div class="brand-card">
            <img src="public/imgs/hero/AMD_Logo.png" alt="Laptop gamer">
            <h3>Gaming extremo</h3>
            <p>Equipos con procesadores de alto rendimiento para videojuegos de última generación.</p>
        </div>
        <div class="brand-card">
            <img src="public/imgs/hero/LG_logo.png" alt="Monitores de edición">
            <h3>Edición profesional</h3>
            <p>Monitores y periféricos para edición de video y diseño gráfico.</p>
        </div>
        <div class="brand-card">
            <img src="public/imgs/hero/Logitech_logo.png" alt="Accesorios tecnológicos">
            <h3>Accesorios premium</h3>
            <p>Audífonos, mouse y adaptadores con diseño exclusivo en negro y rojo.</p>
        </div>
    </div>
</section>