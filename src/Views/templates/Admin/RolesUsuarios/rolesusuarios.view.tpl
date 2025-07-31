<h1>Trabajar con Roles de Usuarios</h1>
<section class="grid">
  <div class="row">
    <form class="col-12 col-m-8" action="index.php" method="get">
      <div class="flex align-center">
        <div class="col-8 row">
          <input type="hidden" name="page" value="Admin_RolesUsuarios_RolesUsuario">

          <label class="col-3" for="partialUser">Usuario</label>
          <input class="col-9" type="text" name="partialUser" id="partialUser" value="{{partialUser}}" />

          <label class="col-3" for="estado">Estado</label>
          <select class="col-9" name="estado" id="estado">
            <option value="0" {{status_EMP}}>Todos</option>
            <option value="ACT" {{status_ACT}}>Activo</option>
            <option value="INA" {{status_INA}}>Inactivo</option>
          </select>
        </div>

        <div class="col-4 align-end">
          <button type="submit">Buscar</button>
        </div>

        {{if isNewEnabled}}
        <div class="col-4 align-end">
          <button type="button" onclick="window.location.href='index.php?page=Admin_RolesUsuarios_RolUsuario&mode=INS'" class="btn btn-primary">
            Asignar nuevo rol
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
        <th style="text-align: left;">Usuario</th>
        <th style="text-align: left;">Rol</th>
        <th>Estado</th>
        <th style="text-align: left;">Asignado</th>
        <th style="text-align: left;">Expira</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      {{foreach asignaciones}}
      <tr>
        <td>{{usercod}}</td>
        <td>{{rolescod}}</td>
        <td>{{roleuserest}}</td>
        <td>{{roleuserfch}}</td>
        <td>{{roleuserexp}}</td>
        <td style="text-align: center;">
          <a href="index.php?page=Admin_RolesUsuarios_RolUsuario&mode=DSP&usercod={{usercod}}&rolescod={{rolescod}}">Detalles</a> &nbsp;

          {{if ~isUpdateEnabled}}
          <a href="index.php?page=Admin_RolesUsuarios_RolUsuario&mode=UPD&usercod={{usercod}}&rolescod={{rolescod}}">Editar</a> &nbsp;
          {{endif ~isUpdateEnabled}}

          {{if ~isDeleteEnabled}}
          <a href="index.php?page=Admin_RolesUsuarios_RolUsuario&mode=DEL&usercod={{usercod}}&rolescod={{rolescod}}">Eliminar</a>
          {{endif ~isDeleteEnabled}}
        </td>
      </tr>
      {{endfor asignaciones}}
    </tbody>
  </table>
  {{pagination}}
</section>