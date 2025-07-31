<section class="depth-2 px-4 py-5">
  <h1>{{FormTitle}}</h1>
</section>

<section class="depth-2 px-4 py-4 my-4 grid  ">
  <form 
    method="POST"
    action="index.php?page=Admin_Roles_Rol&mode={{~mode}}&rolescod={{rolescod}}"
    class="grid col-12 col-m-8 offset-m-2 col-l-6 offset-l-3"
  >

    <!-- Código -->
    <div class="row my-2">
      <label for="rolescod" class="col-12 col-m-4 col-l-3">Código:</label>
      <input 
        type="text"
        name="rolescod" 
        id="rolescod" 
        value="{{rolescod}}" 
        {{readonly_fncod}} 
        class="col-12 col-m-8 col-l-9" />
            {{if rolescod_error}}
              <div class="error col-12">{{rolescod_error}}</div>
            {{endif rolescod_error}}
    </div>
    
    <input type="hidden" name="xsrtoken" value="{{xsrtoken}}" />

    <!-- Nombre -->
    <div class="row my-2">
      <label for="rolesdsc" class="col-12 col-m-4 col-l-3">Nombre:</label>
      <input 
      type="text"
       name="rolesdsc" 
       id="rolesdsc" 
       value="{{rolesdsc}}" 
       {{~readonly}} class="col-12 col-m-8 col-l-9" />
       {{if rolesdsc_error}}
         <div class="error col-12">{{rolesdsc_error}}</div>
       {{endif rolesdsc_error}}
    </div>

    <!-- Estado -->
      <div class="row my-2 align-center">
        <label class="col-12 col-m-3" for="rolesest">Estado</label>

        {{if readonly}}
          <input type="hidden" name="rolesest" value="{{rolStatus}}" />
          <select name="rolesest" id="rolesest" class="col-12 col-m-9">
        {{endif readonly}}

        {{ifnot readonly}}
          <select name="rolesest" id="rolesest" class="col-12 col-m-9">
        {{endifnot readonly}}

          <option value="ACT" {{rolStatus_act}}>Activo</option>
          <option value="INA" {{rolStatus_ina}}>Inactivo</option>
        </select>

        {{if rolesest_error}}
          <div class="col-12 col-m-9 offset-m-3 error">{{rolesest_error}}</div>
        {{endif rolesest_error}}
      </div>

    <!-- Botones -->
    {{if showWarningMsg}}
    <div class="row my-2">
      <div class="col-12 warning" style="color: #d9534f; font-weight: bold; background-color: #f9e6e6; padding: 10px; border-radius: 5px;">
        Este rol no puede ser eliminado porque tiene {{vinculos_count}} vínculo(s) activo(s) con usuarios o funciones.
      </div>
    </div>
    {{endif showWarningMsg}}
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
      window.location.assign("index.php?page=Admin_Roles_Roles");
    });
  });
</script>