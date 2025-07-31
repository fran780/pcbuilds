<section class="depth-2 px-4 py-5">
  <h1>{{FormTitle}}</h1>
</section>

<section class="depth-2 px-4 py-4 my-4 grid row">
  <form 
    method="POST"
    action="index.php?page=Admin_Usuarios_Usuario&mode={{~mode}}&usercod={{usercod}}"
    class="grid col-12 col-m-8 offset-m-2 col-l-6 offset-l-3"
  >

    <!-- Código -->
    <div class="row my-2">
      <label for="usercodD" class="col-12 col-m-4 col-l-3">Código:</label>
      <input type="text" name="usercodD" value="{{usercod}}"
        class="col-12 col-m-8 col-l-9"
        disabled />
    </div>
    <input type="hidden" name="usercod" value="{{usercod}}" />
    <input type="hidden" name="xsrtoken" value="{{xsrtoken}}" />

    <!-- Email -->
    <div class="row my-2">
      <label for="useremail" class="col-12 col-m-4 col-l-3">Email:</label>
      <input type="email" name="useremail" id="useremail"
        value="{{useremail}}" {{~readonly}} class="col-12 col-m-8 col-l-9" />
      {{if useremail_error}}<div class="error col-12">{{useremail_error}}</div>{{endif useremail_error}}
    </div>

    <!-- Nombre -->
    <div class="row my-2">
      <label for="username" class="col-12 col-m-4 col-l-3">Nombre:</label>
      <input type="text" name="username" id="username"
        value="{{username}}" {{~readonly}} class="col-12 col-m-8 col-l-9" />
      {{if username_error}}<div class="error col-12">{{username_error}}</div>{{endif username_error}}
    </div>

    <!-- Contraseña -->
 
    <div class="row my-2">
        <label for="userpswd" class="col-12 col-m-4 col-l-3">Contraseña:</label>
        <input type="password" name="userpswd" id="userpswd"
          value="{{userpswd}}" class="col-12 col-m-8 col-l-9" />
        {{if userpswd_error}}<div class="error col-12">{{userpswd_error}}</div>{{endif userpswd_error}}
      </div>
   

    <!-- Fecha de ingreso -->
    <div class="row my-2">
      <label for="userfching" class="col-12 col-m-4 col-l-3">Fecha de ingreso:</label>
      <input type="datetime-local" name="userfching" id="userfching"
        value="{{userfching}}" {{~readonly}} class="col-12 col-m-8 col-l-9" />
    </div>

    <!-- Tipo de usuario -->
    <div class="row my-2">
      <label for="usertipo" class="col-12 col-m-4 col-l-3">Tipo de usuario:</label>
      <select name="usertipo" id="usertipo" class="col-12 col-m-8 col-l-9" {{~readonly}}>
        <option value="Administrador" {{usertipo_CON}}>Administrador</option>
        <option value="Cliente" {{usertipo_CLI}}>Cliente</option>
      </select>
    </div>

    <!-- Estado del usuario -->
    <div class="row my-2">
      <label for="userest" class="col-12 col-m-4 col-l-3">Estado del usuario:</label>
      <select name="userest" id="userest" class="col-12 col-m-8 col-l-9" {{~readonly}}>
        <option value="ACT" {{userest_ACT}}>Activo</option>
        <option value="INA" {{userest_INA}}>Inactivo</option>
        <option value="BLQ" {{userest_BLQ}}>Bloqueado</option>
      </select>
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
      window.location.assign("index.php?page=Admin_Usuarios_Usuarios");
    });
  });
</script>