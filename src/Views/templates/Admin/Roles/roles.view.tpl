<h1>Trabajar con Roles</h1>
<section class="grid">
  <div class="row">
    <form class="col-12 col-m-8" action="index.php" method="get">
      <div class="flex align-center">
        <div class="col-8 row">
          <input type="hidden" name="page" value="Admin_Roles_Roles">
          <label class="col-3" for="partialName">Nombre</label>
          <input class="col-9" type="text" name="partialName" id="partialName" value="{{partialName}}" />

          <label class="col-3" for="estado">Estado</label>
          <select class="col-9" name="status" id="estado">
            <option value="" {{status_EMP}}>Todos</option>
            <option value="ACT" {{status_ACT}}>Activo</option>
            <option value="INA" {{status_INA}}>Inactivo</option>
          </select>
        </div>

        <div class="col-4 align-end">
          <button type="submit">Buscar</button>
        </div>

        {{if isNewEnabled}}
          <div class="col-4 align-end">
            <button type="button" onclick="window.location.href='index.php?page=Admin-Roles-Rol&mode=INS'" class="btn btn-primary">
              Agregar nuevo rol
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
        <th style="text-align: left;">Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      {{foreach roles}}
      <tr>
        <td>{{rolescod}}</td>
        <td>{{rolesdsc}}</td>
        <td>{{rolesest}}</td>
        <td style="text-align: center;">
          <a href="index.php?page=Admin-Roles-Rol&mode=DSP&rolescod={{rolescod}}">Detalles</a> &nbsp;

          {{if ~isUpdateEnabled}}
            <a href="index.php?page=Admin-Roles-Rol&mode=UPD&rolescod={{rolescod}}">Editar</a> &nbsp;
          {{endif ~isUpdateEnabled}}

          {{if ~isDeleteEnabled}}
            <a href="index.php?page=Admin-Roles-Rol&mode=DEL&rolescod={{rolescod}}">Eliminar</a>
          {{endif ~isDeleteEnabled}}
        </td>
      </tr>
      {{endfor roles}}
    </tbody>
  </table>
  {{pagination}}
</section>