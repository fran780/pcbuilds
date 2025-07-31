<section class="depth-2 px-4 py-5">
  <h1>{{FormTitle}}</h1>
</section>

<section class="depth-2 px-4 py-4 my-4 grid">
  <form 
    method="POST"
    action="index.php?page=Admin_RolesUsuarios_RolUsuario&mode={{~mode}}&usercod={{usercod}}&rolescod={{rolescod}}"
    class="grid col-12 col-m-8 offset-m-2 col-l-6 offset-l-3"
  >

    <!-- Usuario -->
    <div class="row my-2">
      <label for="usercod" class="col-12 col-m-4 col-l-3">Usuario:</label>
      <select name="usercod" id="usercod" class="..." {{disabled}}>
        {{foreach usuarios_list}}
          <option value="{{usercod}}" {{selected}}>{{username}}</option>
        {{endfor usuarios_list}}
      </select>
    </div>

    <!-- Rol -->
    <div class="row my-2">
      <label for="rolescod" class="col-12 col-m-4 col-l-3">Rol:</label>
      <select name="rolescod" id="rolescod" class="col-12 col-m-8 col-l-9" {{if ~readonly}} disabled {{endif ~readonly}}>
        {{foreach roles_list}}
          <option value="{{rolescod}}" {{selected}}>{{rolesdsc}}</option>
        {{endfor roles_list}}
      </select>
    </div>

    <!-- Estado asignación -->
    <div class="row my-2">
      <label for="roleuserest" class="col-12 col-m-4 col-l-3">Estado asignación:</label>
      <select name="roleuserest" id="roleuserest" class="col-12 col-m-8 col-l-9" {{if ~readonly}} disabled {{endif ~readonly}}>
        <option value="ACT" {{roleuserest_ACT}}>Activo</option>
        <option value="INA" {{roleuserest_INA}}>Inactivo</option>
      </select>
    </div>

    <!-- Fecha de asignación -->
    <div class="row my-2">
      <label for="roleuserfch" class="col-12 col-m-4 col-l-3">Fecha asignación:</label>
      <input type="text" name="roleuserfch" id="roleuserfch" value="{{roleuserfch}}" class="col-12 col-m-8 col-l-9" {{~readonly}} />
    </div>

    <!-- Fecha de expiración -->
    <div class="row my-2">
      <label for="roleuserexp" class="col-12 col-m-4 col-l-3">Expira:</label>
      <input type="text" name="roleuserexp" id="roleuserexp" value="{{roleuserexp}}" class="col-12 col-m-8 col-l-9" {{~readonly}} />
    </div>

    <!-- Tokens -->
    <input type="hidden" name="xsrtoken" value="{{xsrtoken}}" />

    <!-- Botones -->
    <div class="row my-4 align-center flex-end">
      {{if showCommitBtn}}
      <button class="primary col-12 col-m-2" type="submit" name="btnConfirmar">Confirmar</button>
      &nbsp;
      {{endif showCommitBtn}}
      <button class="col-12 col-m-2" type="button" id="btnCancelar">
        {{if showCommitBtn}}Cancelar{{endif showCommitBtn}}
        {{ifnot showCommitBtn}}Regresar{{endifnot showCommitBtn}}
      </button>
    </div>

  </form>
</section>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("btnCancelar").addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      window.location.assign("index.php?page=Admin_RolesUsuarios_RolesUsuario");
    });
  });
</script>