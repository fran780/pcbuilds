<section class="contact-section">
    <div class="logo">
        <img src="public/imgs/hero/PCB_Logo_White.webp" alt="PC Builds Honduras">
    </div>
    <div class="contact-content">
        <h1>Contáctanos</h1>
        <p>Contáctenos sobre cualquier cosa relacionada con nuestra empresa o servicios.<br>
            Haremos todo lo posible para llamarte lo antes posible.</p>
        <div class="buttons">
            <a href="index.php" class="btn">Inicio</a>
            <a href="index.php?page=Tienda" class="btn">Tienda</a>
        </div>
    </div>
</section>

<section class="contact-wrapper">
    <div class="form-container">
        <h2>Formulario</h2>
        <form action="index.php?page=contactanos" method="POST">
            <label for="nombre">Nombre Completo:</label>
            <input type="text" id="nombre" name="nombre" placeholder="Escriba su nombre" required />

            <label for="correo">Correo Electrónico:</label>
            <input type="email" id="correo" name="correo" placeholder="tucorreo@gmail.com" required />

            <label for="telefono">Teléfono de Contacto:</label>
            <input type="tel" id="telefono" name="telefono" placeholder="(504) 9755-0655" required />

            <label for="mensaje">Pregunta:</label>
            <textarea id="mensaje" name="mensaje" rows="4"
                placeholder="Escribe aquí cualquier pregunta o reclamo que tenga" required></textarea>

            <div class="form-buttons">
                <button type="submit" class="btn-enviar">Enviar</button>
            </div>
        </form>
    </div>

    <div class="location-container">
        <h2>Ubicación:</h2>
        <div class="location-icon">
            <img src="public/imgs/hero/ICONO_UBICACION.png" alt="">
        </div>

        <div class="location-info">
            <p><strong>PC Builds Honduras</strong><br>
                Tegucigalpa:<br>
                Col. Modelo Ave. Los Angeles<br>
                1 Cuadra de Diprova<br><br>
                San Pedro Sula:<br>
                Plaza La Salle 18 ave, 4 y 5 Calle NO<br>
                +504 3145-2866<br>
                info@pcbuildshn.com</p>
        </div>
    </div>
</section>