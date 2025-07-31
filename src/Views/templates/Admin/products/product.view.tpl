<section class="depth-2 px-4 py-5">
  <h1>{{FormTitle}}</h1>
</section>

<section class="depth-2 px-4 py-4 my-4 grid  ">
  
  <form 
    method="POST"
    action="index.php?page=Admin_Products_Product&mode={{~mode}}&id_producto={{id_producto}}"
    class="grid col-12 col-m-8 offset-m-2 col-l-6 offset-l-3"
  >

    <!-- Código -->
    <div class="row my-2">
      <label for="id_producto" class="col-12 col-m-4 col-l-3">Código:</label>
      <input
       type="text"
       name="id_productoD" 
       id="id_producto" 
       value="{{id_producto}}" 
       placeholder="Número de registro de producto"
       class="col-12 col-m-8 col-l-9"
       readonly disabled/>
    </div>
    <input type="hidden" name="id_producto" value="{{id_producto}}" />
    <input type="hidden" name="xsrtoken" value="{{xsrtoken}}" />
    <!-- Nombre -->
    <div class="row my-2">
      <label for="nombre_producto" class="col-12 col-m-4 col-l-3">Nombre:</label>
      <input 
      type="text"
       name="nombre_producto" 
       id="nombre_producto" 
       value="{{nombre_producto}}" 
       {{~readonly}} class="col-12 col-m-8 col-l-9" />
       {{if nombre_producto_error}}
         <div class="error col-12">{{nombre_producto_error}}</div>
       {{endif nombre_producto_error}}
    </div>

    <!-- Descripción -->
    <div class="row my-2">
      <label for="descripcion" class="col-12 col-m-4 col-l-3">Descripción:</label>
      <textarea name="descripcion" id="descripcion" {{~readonly}} class="col-12 col-m-8 col-l-9">{{descripcion}}</textarea>
      {{if descripcion_error}}<div class="error col-12">{{descripcion_error}}</div>{{endif descripcion_error}}
    </div>

    <!-- Precio -->
    <div class="row my-2">
      <label for="precio" class="col-12 col-m-4 col-l-3">Precio:</label>
      <input type="number" name="precio" id="precio" value="{{precio}}" {{~readonly}} class="col-12 col-m-8 col-l-9" />
      {{if precio_error}}<div class="error col-12">{{precio_error}}</div>{{endif precio_error}}
    </div>

    <!-- Stock -->
    <div class="row my-2">
      <label for="stock" class="col-12 col-m-4 col-l-3">Stock:</label>
      <input type="number" name="stock" id="stock" value="{{stock}}" {{~readonly}} class="col-12 col-m-8 col-l-9" />
      {{if stock_error}}<div class="error col-12">{{stock_error}}</div>{{endif stock_error}}
    </div>

    <!-- Imagen -->
    <div class="row my-2">
      <label for="imagen" class="col-12 col-m-4 col-l-3">Imagen:</label>
      <input type="text" name="imagen" id="imagen" value="{{imagen}}" {{~readonly}} class="col-12 col-m-8 col-l-9" />
      {{if imagen_error}}<div class="error col-12">{{imagen_error}}</div>{{endif imagen_error}}
    </div>

    <!-- Categoría -->
    <div class="row my-2">
      <label for="id_categoria" class="col-12 col-m-4 col-l-3">Categoría:</label>
      <select name="id_categoria" id="id_categoria" class="col-12 col-m-8 col-l-9" {{if ~readonly}} disabled {{endif ~readonly}}>
      {{foreach categoria_producto_list}}
        <option value="{{id_categoria}}" {{selected}}>{{nombre_categoria}}</option>
      {{endfor categoria_producto_list}}
      </select>
    </div>

    <!-- Marca -->
     <div class="row my-2">
      <label for="id_marca" class="col-12 col-m-4 col-l-3">Marca:</label>
      <select name="id_marca" id="id_marca" class="col-12 col-m-8 col-l-9" {{if ~readonly}} disabled {{endif ~readonly}}>
        {{foreach marca_producto_list}}
          <option value="{{id_marca}}" {{selected}}>{{nombre_marca}}</option>
        {{endfor marca_producto_list}}
      </select>
      {{if id_marca_error}}<div class="error col-12">{{id_marca_error}}</div>{{endif id_marca_error}}
    </div>

    <!-- Estado -->
      <div class="row my-2 align-center">
        <label class="col-12 col-m-3" for="id_estado">Estado</label>

        {{if readonly}}
          <input type="hidden" name="id_estado" value="{{productStatus}}" />
          <select name="id_estado" id="id_estado" class="col-12 col-m-9">
        {{endif readonly}}

        {{ifnot readonly}}
          <select name="id_estado" id="id_estado" class="col-12 col-m-9">
        {{endifnot readonly}}

          <option value="1" {{productStatus_act}}>Activo</option>
          <option value="2" {{productStatus_ina}}>Inactivo</option>
        </select>

        {{if id_estado_error}}
          <div class="col-12 col-m-9 offset-m-3 error">{{id_estado_error}}</div>
        {{endif id_estado_error}}
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
      window.location.assign("index.php?page=Admin_Products_Products");
    });
  });
</script>