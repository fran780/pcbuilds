<h1>Gestión de Usuarios</h1>
<section class="grid">
  <div class="row">
    <form class="col-12 col-m-8" action="index.php" method="get">
      <div class="flex align-center">
        <div class="col-8 row">
          <input type="hidden" name="page" value="Admin_Usuarios_Usuarios" />

          <label class="col-3" for="partialName">Email</label>
          <input class="col-9" type="text" name="partialName" id="partialName" value="{{partialName}}" />

          <label class="col-3" for="status">Estado</label>
          <select class="col-9" name="status" id="status">
              <option value="" {{status_EMP}}>Todos</option>
              <option value="ACT" {{status_ACT}}>Activo</option>
              <option value="INA" {{status_INA}}>Inactivo</option>
              <option value="BLQ" {{status_BLQ}}>Bloqueado</option>
          </select>
        </div>

        <div class="col-4 align-end">
          <button type="submit">Buscar</button>
        </div>

        {{if isNewEnabled}}
        <div class="col-4 align-end">
          <button type="button" onclick="window.location.href='index.php?page=Admin-Usuarios-Usuario&mode=INS'" class="btn btn-primary">
            Crear nuevo usuario
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
        <th>Email</th>
        <th style="text-align: left;">Nombre</th>
        <th style="text-align: left;">Tipo</th>
        <th style="text-align: left;">Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      {{foreach usuarios}}
      <tr>
        <td>{{usercod}}</td>
        <td>{{useremail}}</td>
        <td>{{username}}</td>
        <td>{{usertipo}}</td>
        <td>{{userest}}</td>
        <td style="text-align: center;">
          <a href="index.php?page=Admin-Usuarios-Usuario&mode=DSP&usercod={{usercod}}">Detalles</a> &nbsp;

          {{if ~isUpdateEnabled}}
          <a href="index.php?page=Admin-Usuarios-Usuario&mode=UPD&usercod={{usercod}}">Editar</a> &nbsp;
          {{endif ~isUpdateEnabled}}

          {{if ~isDeleteEnabled}}
          <a href="index.php?page=Admin-Usuarios-Usuario&mode=DEL&usercod={{usercod}}">Eliminar</a>
          {{endif ~isDeleteEnabled}}
        </td>
      </tr>
      {{endfor usuarios}}

      {{ifnot usuarios}}
      <tr>
        <td colspan="6" style="text-align:center;">No hay usuarios que coincidan con los filtros.</td>
      </tr>
      {{endifnot usuarios}}
    </tbody>
  </table>
  {{pagination}}
</section>