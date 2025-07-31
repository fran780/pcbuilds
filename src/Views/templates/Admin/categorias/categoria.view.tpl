<section class="depth-2 px-4 py-5">
    <h2>{{FormTitle}}</h2>
</section>
<section class="depth-2 px-4 py-4 my-4 grid row">
   <form 
        method="POST"
        action="index.php?page=Admin_Categorias_Categoria&mode={{mode}}&id_categoria={{id_categoria}}"
        class="grid col-12 col-m-8 offset-m-2 col-l-6 offset-l-3"
    >
  
     <div class="row my-2">
            <label for="id" class="col-12 col-m-4 col-l-3">Id:</label>
            <input 
                type="text"
                name="id_categoria"
                id="id_categoria"
                value="{{id_categoria}}"
                placeholder="Id de categoria"
                class="col-12 col-m-8 col-l-9"
                readonly
             />
            
        </div>
         <input type="hidden" name="xsrtoken" value="{{xsrtoken}}" />
             <input type="hidden" name="id_categoria" value="{{id_categoria}}" />

        <div class="row my-2">
            <label for="category" class="col-12 col-m-4 col-l-3">Categoria:</label>
            <input 
                type="text"
                name="nombre_categoria"
                id="nombre_categoria"
                value="{{nombre_categoria}}"
                placeholder="nombre_categoria"
                class="col-12 col-m-8 col-l-9"
                {{readonly}}
             />
             {{if nombre_categoria_error}}
                <div class="error col-12">{{nombre_categoria_error}}</div>
            {{endif nombre_categoria_error}}
        </div>

       <!-- Estado -->
    <div class="row my-2">
      <label for="estado_categoria" class="col-12 col-m-4 col-l-3">Estado:</label>
      <select name="estado_categoria" id="estado_categoria" class="col-12 col-m-8 col-l-9" {{~readonly}}>
        <option value="ACT" {{estado_categoria_ACT}}>Activo</option>
        <option value="INA" {{estado_categoria_INA}}>Inactivo</option>
      </select>
      {{if estado_categoria_error}}<div class="error col-12">{{estado_categoria_error}}</div>{{endif estado_categoria_error}}
    </div>




    <!-- VERIFICAR SI NO HAY CATEGORIA ENLAZADA A UN PRODUCTO -->
    {{if showWarningMsg}}
        <div class="row my-2">
          <div class="col-12 warning" style="color: #d9534f; font-weight: bold; background-color: #f9e6e6; padding: 10px; border-radius: 5px;">
            Esta categoría no puede ser eliminada porque tiene {{productos_count}} producto(s) asociado(s).
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
      window.location.assign("index.php?page=Admin_Categorias_Categorias");
    });
  });
</script>