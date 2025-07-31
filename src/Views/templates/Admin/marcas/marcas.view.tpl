<h1>Trabajar con Marcas</h1>

<section class="grid" style="margin-bottom: 0;">
  <div class="row">
    <form class="col-12 col-m-8" action="index.php" method="get">
      <div class="flex align-center">
        <div class="col-8 row">
        <input type="hidden" name="page" value="Admin_Marcas_Marcas">
        
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

        
        <div class="col-4 align-end">
          <button type="button" onclick="window.location.href='index.php?page=Admin_Marcas_Marca&mode=INS'" class="btn btn-primary">
            Agregar nueva marca
          </button>
        </div>
       
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
      {{foreach marcas}}
        <tr>
            <td style="text-align: center;">{{id_marca}}</td>
            <td style="text-align: center;">{{nombre_marca}}</td>
            <td style="text-align: center;">

            <a href="index.php?page=Admin_Marcas_Marca&mode=DSP&id_marca={{id_marca}}">Detalles</a> &nbsp;
           <a href="index.php?page=Admin_Marcas_Marca&mode=UPD&id_marca={{id_marca}}">Editar</a> &nbsp;
            <a href="index.php?page=Admin_Marcas_Marca&mode=DEL&id_marca={{id_marca}}">Eliminar</a>
            </td>
        </tr>
        {{endfor marcas}}
    </tbody>
  </table>

  {{pagination}}
</section>