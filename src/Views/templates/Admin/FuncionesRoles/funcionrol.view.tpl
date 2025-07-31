<section class="depth-2 px-4 py-5">
  <h1>{{FormTitle}}</h1>
</section>

<section class="depth-2 px-4 py-4 my-4 grid  ">
  
  <form 
    method="POST"
    action="index.php?page=Admin_FuncionesRoles_FuncionesRol&mode={{~mode}}&rolescod={{rolescod}}&fncod={{fncod}}"
    class="grid col-12 col-m-8 offset-m-2 col-l-6 offset-l-3"
  >



<!-- Rol -->
    <div class="row my-2">
      <label for="rolescod" class="col-12 col-m-4 col-l-3">Rol:</label>
      <select name="rolescod" id="rolescod" class="col-12 col-m-8 col-l-9" {{if ~readonly}} disabled {{endif ~readonly}}>
        {{foreach roles_list}}
          <option value="{{rolescod}}" {{selected}}>{{rolesdsc}}</option>
        {{endfor roles_list}}
      </select>
    </div>

    <!-- Función -->
    <div class="row my-2">
      <label for="fncod" class="col-12 col-m-4 col-l-3">Función:</label>
      <select name="fncod" id="fncod" class="col-12 col-m-8 col-l-9" {{if ~readonly}} disabled {{endif ~readonly}}>
        {{foreach funciones_list}}
         <option value="{{fncod}}" {{selected}}>{{fncod}}</option>
        {{endfor funciones_list}}
      </select>
    </div>

      <!-- Tipo -->
      <div class="row my-2">
        <label for="fntyp" class="col-12 col-m-4 col-l-3">Tipo de permiso:</label>
        <select name="fntyp" id="fntyp" class="col-12 col-m-8 col-l-9">
          <option value="CTR" <?= $viewData["fntyp_CTR"] ?>Paginas pricipales</option>
          <option value="FNC" <?= $viewData["fntyp_FNC"] ?>Acciones</option>
          <option value="ASPI" <?= $viewData["fntyp_ASPI"] ?>Menú</option>
        </select>
        
      </div>

    <!-- Estado asignación -->
    <div class="row my-2">
      <label for="fnrolest" class="col-12 col-m-4 col-l-3">Estado asignación:</label>
      <select name="fnrolest" id="fnrolest" class="col-12 col-m-8 col-l-9" {{if ~readonly}} disabled {{endif ~readonly}}>
        <option value="ACT" {{fnrolest_ACT}}>Activo</option>
        <option value="INA" {{fnrolest_INA}}>Inactivo</option>
        <option value="BLO" {{fnrolest_BLO}}>Bloqueado</option>
      </select>
    </div>

    <!-- Fecha de expiración -->
    <div class="row my-2">
      <label for="fnexp" placeholder="YYYY-MM-DD" class="col-12 col-m-4 col-l-3">Expira:</label>
      <input type="text" name="fnexp" id="fnexp" value="{{fnexp}}" class="col-12 col-m-8 col-l-9" {{~readonly}} />
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
      window.location.assign("index.php?page=Admin_FuncionesRoles_FuncionesRoles");
    });
  });
</script>