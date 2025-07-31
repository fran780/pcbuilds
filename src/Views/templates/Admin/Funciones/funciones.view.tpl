<h1>Gestionar Permisos</h1>

<section class="grid">

  <div class="row">
    <form class="col-12 col-m-8" action="index.php" method="get">
      <div class="flex align-center">
        <div class="col-8 row">
          <input type="hidden" name="page" value="Admin_Funciones_Funciones">
          <label class="col-3" for="partialName">Descripción</label>
          <input class="col-9" type="text" name="partialName" id="partialName" value="{{partialName}}" />
          
          <label class="col-3" for="tipoFuncion">Tipo</label>
          <select class="col-9" name="tipoFuncion" id="tipoFuncion">
              <option value="" {{tipo_EMP}}>Todos</option>
              <option value="CTR" {{tipo_CTR}}>Controlador</option>
              <option value="FNC" {{tipo_FNC}}>Función</option>
              <option value="MNU" {{tipo_MNU}}>Menú</option>
          </select>

          <label class="col-3" for="estado">Estado</label>
          <select class="col-9" name="estado" id="estado">
              <option value="" {{estado_EMP}}>Todos</option>
              <option value="ACT" {{estado_ACT}}>Activo</option>
              <option value="INA" {{estado_INA}}>Inactivo</option>
          </select>
        </div>

        <div class="col-4 align-end">
          <button type="submit">Buscar</button>
        </div>

        {{if isNewEnabled}}
        <div class="col-4 align-end">
          <button type="button" onclick="window.location.href='index.php?page=Admin-Funciones-Funcion&mode=INS'" class="btn btn-primary">
            Agregar nueva función
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
        <th style="text-align: left;">Permiso</th>
        <th style="text-align: left;">Descripción</th>
        <th style="text-align: left;">Tipo</th>
        <th style="text-align: left;">Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      {{foreach funciones}}
      <tr>
        <td>{{fncod}}</td>
        <td>{{fndsc}}</td>
        <td>{{fntyp}}</td>
        <td>{{fnest}}</td>
        <td style="text-align: center;">
          <a href="index.php?page=Admin-Funciones-Funcion&mode=DSP&fncod={{fncod}}">Detalles</a> &nbsp;

          {{if ~isUpdateEnabled}}
          <a href="index.php?page=Admin-Funciones-Funcion&mode=UPD&fncod={{fncod}}">Editar</a> &nbsp;
          {{endif ~isUpdateEnabled}}

          {{if ~isDeleteEnabled}}
          <a href="index.php?page=Admin-Funciones-Funcion&mode=DEL&fncod={{fncod}}">Eliminar</a>
          {{endif ~isDeleteEnabled}}
        </td>
      </tr>
      {{endfor funciones}}
    </tbody>
  </table>
  {{pagination}}
</section>