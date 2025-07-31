<section class="depth-2 px-4 py-5">
  <h2>{{FormTitle}}</h2>
</section>

<section class="depth-2 px-4 py-4 my-4 grid row">
  <form 
    method="POST"
    action="index.php?page=Admin_Marcas_Marca&mode={{mode}}&id_marca={{id_marca}}"
    class="grid col-12 col-m-8 offset-m-2 col-l-6 offset-l-3"
  >
    <div class="row my-2">
      <label for="id_marca" class="col-12 col-m-4 col-l-3">Id:</label>
      <input 
        type="text"
        name="id_marca"
        id="id_marca"
        value="{{id_marca}}"
        placeholder="Id de marca"
        class="col-12 col-m-8 col-l-9"
        readonly
      />
      <input type="hidden" name="xsrtoken" value="{{xsrtoken}}" />
    </div>

    <div class="row my-2">
      <label for="nombre_marca" class="col-12 col-m-4 col-l-3">Marca:</label>
      <input 
        type="text"
        name="nombre_marca"
        id="nombre_marca"
        value="{{nombre_marca}}"
        placeholder="nombre_marca"
        class="col-12 col-m-8 col-l-9"
        {{readonly}}
      />
      {{if nombre_marca_error}}
        <div class="error col-12">{{nombre_marca_error}}</div>
      {{endif nombre_marca_error}}
    </div>

    <!-- Estado -->
    <div class="row my-2">
      <label for="estado_marca" class="col-12 col-m-4 col-l-3">Estado:</label>
      <select name="estado_marca" id="estado_marca" class="col-12 col-m-8 col-l-9" {{~readonly}}>
        <option value="ACT" {{estado_marca_ACT}}>Activo</option>
        <option value="INA" {{estado_marca_INA}}>Inactivo</option>
      </select>
      {{if estado_marca_error}}
        <div class="error col-12">{{estado_marca_error}}</div>
      {{endif estado_marca_error}}
    </div>

    <!-- Advertencia si tiene productos asociados -->
    {{if showWarningMsg}}
      <div class="row my-2">
        <div class="col-12 warning" style="color: #d9534f; font-weight: bold; background-color: #f9e6e6; padding: 10px; border-radius: 5px;">
          Esta marca no puede ser eliminada porque tiene {{productos_count}} producto(s) asociado(s).
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
      window.location.assign("index.php?page=Admin_Marcas_Marcas");
    });
  });
</script>