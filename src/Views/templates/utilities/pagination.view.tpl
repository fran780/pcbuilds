<section class="grid flex align-center">
<div class="col-3">
  <form action="{{url}}" method="get" id="pagination">
    <input type="hidden" name="page" value="{{page}}" />
    <select name="itemsPerPage" id="itemsPerPage">
      <option value="12" {{itemsPerPage_12}}>12</option>
      <option value="16" {{itemsPerPage_16}}>16</option>
      <option value="24" {{itemsPerPage_24}}>24</option>
      <option value="32" {{itemsPerPage_32}}>32</option>
      <option value="64" {{itemsPerPage_64}}>64</option>
      <option value="100" {{itemsPerPage_100}}>100</option>
    </select>
  </form>
</div>
<div class="col-3"> 
</div>
<div class="col-6 flex flex-end">
{{foreach pages}}
  <a {{if url}}href="{{url}}"{{endif url}} class="w32 btn mx-2 {{if active}}depth-1 mx-3{{endif active}}">{{page}}</a>
{{endfor pages}}
</div>
</section>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('itemsPerPage').addEventListener('change', function() {
      document.getElementById('pagination').submit();
    });
  });
</script>