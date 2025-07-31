<h1>Trabajar con Productos</h1>
<section class="grid">
  <div class="row">
    <form class="col-12 col-m-8" action="index.php" method="get">
      <div class="flex align-center">
        <div class="col-8 row">
          <input type="hidden" name="page" value="Admin_Products_Products">
          <label class="col-3" for="partialName">Nombre</label>
          <input class="col-9" type="text" name="partialName" id="partialName" value="{{partialName}}" />
          <label class="col-3" for="estado">Estado</label>
          <select class="col-9" name="estado" id="estado">
              <option value="0" {{status_EMP}}>Todos</option>
              <option value="1" {{status_ACT}}>Activo</option>
              <option value="2" {{status_INA}}>Inactivo</option>
          </select>
        </div>
        <div class="col-4 align-end">
          <button type="submit">Buscar</button>
        </div>

        <!-- VER SI LO DEJO ASÍ, investigado -->
          {{if isNewEnabled}}
            <div class="col-4 align-end">
              <button type="button" onclick="window.location.href='index.php?page=Admin-Products-Product&mode=INS'" class="btn btn-primary">
                Agregar nuevo producto
              </button>
            </div>
          {{endif isNewEnabled}}
        </div>
    </form>
  </div>
</section>

<section class="WWList">
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <th style="text-align: left;">ID</th>
        <th style="text-align: left;">Nombre</th>
        <th >Descripción</th>
        <th style="text-align: left;">Precio</th>
        <th style="text-align: left;">Stock</th>
        <th style="text-align: left;">Categoría</th>
        <th style="text-align: left;">Marca</th>
        <th style="text-align: left;">Estado</th>
        <th >Acciones</th>
      </tr>
    </thead>
    <tbody>
      {{foreach products}}
      <tr>
        <td>{{id_producto}}</td>
        <td>{{nombre_producto}}</td>
        <td>{{descripcion}}</td>
        <td>L {{precio}}</td>
        <td>{{stock}}</td>
        <td>{{nombre_categoria}}</td>
        <td>{{nombre_marca}}</td>
        <td>{{estado}}</td>
        <td style="text-align: center;">
          
          <a href="index.php?page=Admin-Products-Product&mode=DSP&id_producto={{id_producto}}" >Detalles</a> &nbsp;
         
          {{if ~isUpdateEnabled}}
          <a href="index.php?page=Admin-Products-Product&mode=UPD&id_producto={{id_producto}}">Editar</a> &nbsp;
          {{endif ~isUpdateEnabled}}

          {{if ~isDeleteEnabled}}
          <a href="index.php?page=Admin-Products-Product&mode=DEL&id_producto={{id_producto}}">Eliminar</a>
          {{endif ~isDeleteEnabled}}
    </td>
      </tr>
      {{endfor products}}
    </tbody>
  </table>
  {{pagination}}
</section>