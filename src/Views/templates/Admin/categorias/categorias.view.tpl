<h1>Trabajar con Categorías</h1>

<section class="grid" style="margin-bottom: 0;">
  <div class="row">
    <form class="col-12 col-m-8" action="index.php" method="get">
      <div class="flex align-center">
        <div class="col-8 row">
        <input type="hidden" name="page" value="Admin_Categorias_Categorias">
        
        <label class="col-3" for="partialName">Nombre</label>
        <input class="col-9" type="text" name="partialName" id="partialName" value="{{partialName}}" />
        
        <label class="col-3" for="estado">Estado</label>
        <select class="col-9" name="estado" id="estado">
          <option value="" {{estado_TODOS}}>Todos</option>
          <option value="ACT" {{estado_ACT}}>Activo</option>
          <option value="INA" {{estado_INA}}>Inactivo</option>
        </select>
      </div>

        <div class="col-4 align-end">
          <button type="submit">Buscar</button>
        </div>

        {{if isNewEnabled}}
        <div class="col-4 align-end">
          <button type="button" onclick="window.location.href='index.php?page=Admin_Categorias_Categoria&mode=INS'" class="btn btn-primary">
            Agregar nueva categoría
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
        <th style="text-align: center;">ID</th>
        <th style="text-align: center;">Nombre</th>
        <th style="text-align: center;">Acciones</th>
      </tr>
    </thead>
    <tbody>
      {{foreach categorias}}
      <tr>
        <td style="text-align: center;">{{id_categoria}}</td>
        <td style="text-align: center;">{{nombre_categoria}}</td>
        <td style="text-align: center;">
          <!-- Acciones -->
          <a href="index.php?page=Admin_Categorias_Categoria&mode=DSP&id_categoria={{id_categoria}}">Detalles</a> &nbsp;
          {{if ~isUpdateEnabled}}<a href="index.php?page=Admin_Categorias_Categoria&mode=UPD&id_categoria={{id_categoria}}">Editar</a> &nbsp;{{endif ~isUpdateEnabled}}
          {{if ~isDeleteEnabled}}<a href="index.php?page=Admin_Categorias_Categoria&mode=DEL&id_categoria={{id_categoria}}">Eliminar</a>{{endif ~isDeleteEnabled}}
        </td>
      </tr>
      {{endfor categorias}}
    </tbody>
  </table>

  {{pagination}}
</section>