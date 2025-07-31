<h1>Funciones por Rol</h1>
<section class="grid">
  <div class="row">
    <form class="col-12 col-m-8" action="index.php" method="get">
      <div class="flex align-center">
        <div class="col-8 row">
          <input type="hidden" name="page" value="Admin_FuncionesRoles_FuncionesRoles">

          <label class="col-3" for="fncod">Código</label>
          <input class="col-9" type="text" name="fncod" id="fncod" value="{{fncod}}" />

          <label class="col-3" for="fndsc">Descripción</label>
          <input class="col-9" type="text" name="fndsc" id="fndsc" value="{{fndsc}}" />

          <label class="col-3" for="rolescod">Rol</label>
          <input class="col-9" type="text" name="rolescod" id="rolescod" value="{{rolescod}}" />
        </div>

        <div class="col-4 align-end">
          <button type="submit">Buscar</button>
        </div>

        {{if isNewEnabled}}
            <div class="col-4 align-end">
              <button type="button" onclick="window.location.href='index.php?page=Admin_FuncionesRoles_FuncionesRol&mode=INS'" class="btn btn-primary">
                Asignar función a un rol
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
        <th style="text-align: left;">Rol</th>
        <th style="text-align: left;">Código Función</th>
        <th>Descripción</th>
        <th style="text-align: left;">Tipo</th>
        <th style="text-align: left;">Estado</th>
        <th style="text-align: left;">Estado Asignación</th>
        <th style="text-align: left;">Expira</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      {{foreach funciones}}
      <tr>
        <td>{{rolescod}}</td>
        <td>{{fncod}}</td>
        <td>{{fndsc}}</td>
        <td>{{fntyp}}</td>
        <td>{{fnest}}</td>
        <td>{{fnrolest}}</td>
        <td>{{fnexp}}</td>
        <td style="text-align: center;">
          <a href="index.php?page=Admin_FuncionesRoles_FuncionesRol&mode=DSP&rolescod={{rolescod}}&fncod={{fncod}}">Detalles</a> &nbsp;

          {{if ~isUpdateEnabled}}
          <a href="index.php?page=Admin_FuncionesRoles_FuncionesRol&mode=UPD&rolescod={{rolescod}}&fncod={{fncod}}">Editar</a> &nbsp;
          {{endif ~isUpdateEnabled}}

          {{if ~isDeleteEnabled}}
          <a href="index.php?page=Admin_FuncionesRoles_FuncionesRol&mode=DEL&rolescod={{rolescod}}&fncod={{fncod}}">Eliminar</a>
          {{endif ~isDeleteEnabled}}
        </td>
      </tr>
      {{endfor funciones}}
    </tbody>
  </table>
  {{pagination}}
</section>