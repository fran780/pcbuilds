<section class="depth-2 px-4 py-5">
  <h1>{{FormTitle}}</h1>

</section>

<section class="depth-2 px-4 py-4 my-4 grid  ">
  
  <form 
    method="POST"
    action="index.php?page=Admin_Funciones_Funcion&mode={{~mode}}&fncod={{fncod}}"
    class="grid col-12 col-m-8 offset-m-2 col-l-6 offset-l-3"
  >


        <!-- Código -->
    <div class="row my-2">
    <label for="fncod" class="col-12 col-m-4 col-l-3">Permiso:</label>
    <input 
        type="text"
        name="fncod" 
        id="fncod" 
        value="{{fncod}}" 
        {{readonly_fncod}} 

        class="col-12 col-m-8 col-l-9" />
    {{if fncod_error}}
        <div class="error col-12">{{fncod_error}}</div>
    {{endif fncod_error}}
    </div>

    <input type="hidden" name="xsrtoken" value="{{xsrtoken}}" />

    <!-- Descripción -->
    <div class="row my-2">
    <label for="fndsc" class="col-12 col-m-4 col-l-3">Descripción:</label>
    <input 
        type="text"
        name="fndsc" 
        id="fndsc" 
        value="{{fndsc}}" 
        {{~readonly}} 
        class="col-12 col-m-8 col-l-9" />
    {{if fndsc_error}}
        <div class="error col-12">{{fndsc_error}}</div>
    {{endif fndsc_error}}
    </div>

    <!-- Tipo -->
    <div class="row my-2">
      <label for="fntyp" class="col-12 col-m-4 col-l-3">Tipo:</label>
      <select name="fntyp" id="fntyp" class="col-12 col-m-8 col-l-9" {{if ~readonly}} disabled {{endif ~readonly}}>
        <option value="CTR" {{tipo_CTR}}>Controlador</option>
        <option value="FNC" {{tipo_FNC}}>Función</option>
        <option value="MNU" {{tipo_MNU}}>Menú</option>
      </select>
      {{if fntyp_error}}<div class="error col-12">{{fntyp_error}}</div>{{endif fntyp_error}}
    </div>

    <!-- Estado -->
    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="fnest">Estado</label>
      <select name="fnest" id="fnest" class="col-12 col-m-9" {{if ~readonly}} disabled {{endif ~readonly}}>
        <option value="ACT" {{estado_ACT}}>Activo</option>
        <option value="INA" {{estado_INA}}>Inactivo</option>
      </select>
      {{if fnest_error}}<div class="error col-12 col-m-9 offset-m-3">{{fnest_error}}</div>{{endif fnest_error}}
    </div>

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
      window.location.assign("index.php?page=Admin_Funciones_Funciones");
    });
  });
</script>