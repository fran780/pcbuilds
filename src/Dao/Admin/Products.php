<?php
namespace Dao\Admin;

use Dao\Table;

class Products extends Table {

  public static function getProducts(
    string $partialName = "",
    int $idCategoria = 0,
    int $idMarca = 0,
    int $idEstado = 0,
    string $orderBy = "",
    bool $orderDescending = false,
    int $page = 0,
    int $itemsPerPage = 10
  ) {
    $sqlstr = "SELECT p.id_producto,p.nombre_producto,p.descripcion, p.precio, p.stock, p.imagen,c.nombre_categoria,m.nombre_marca,e.estado
    FROM producto p
    LEFT JOIN categoria_producto c ON p.id_categoria = c.id_categoria
    LEFT JOIN marca_producto m ON p.id_marca = m.id_marca
    LEFT JOIN estado_producto e ON p.id_estado = e.id_estado
    ";

    $sqlstrCount = "SELECT COUNT(*) as count FROM producto p
    LEFT JOIN categoria_producto c ON p.id_categoria = c.id_categoria
    LEFT JOIN marca_producto m ON p.id_marca = m.id_marca
    LEFT JOIN estado_producto e ON p.id_estado = e.id_estado
    ";

    $conditions = [];
    $params = [];

    if ($partialName !== "") {
      $conditions[] = "p.nombre_producto LIKE :partialName";
      $params["partialName"] = "%" . $partialName . "%";
    }
    if ($idCategoria > 0) {
      $conditions[] = "p.id_categoria = :idCategoria";
      $params["idCategoria"] = $idCategoria;
    }
    if ($idMarca > 0) {
      $conditions[] = "p.id_marca = :idMarca";
      $params["idMarca"] = $idMarca;
    }
    if ($idEstado > 0) {
      $conditions[] = "p.id_estado = :idEstado";
      $params["idEstado"] = $idEstado;
    }

    if (count($conditions) > 0) {
      $where = " WHERE " . implode(" AND ", $conditions);
      $sqlstr .= $where;
      $sqlstrCount .= $where;
    }

    $validOrderBy = ["id_producto", "nombre_producto", "precio", "stock"];
    if ($orderBy !== "" && in_array($orderBy, $validOrderBy)) {
      $sqlstr .= " ORDER BY " . $orderBy;
      if ($orderDescending) {
        $sqlstr .= " DESC";
      }
    }

    $totalRecords = self::obtenerUnRegistro($sqlstrCount, $params)["count"];
    $pagesCount = ceil($totalRecords / $itemsPerPage);
    if ($page > $pagesCount - 1) {
      $page = max(0, $pagesCount - 1);
    }

    $sqlstr .= " LIMIT " . ($page * $itemsPerPage) . ", " . $itemsPerPage;

    $records = self::obtenerRegistros($sqlstr, $params);
    return [
      "products" => $records,
      "total" => $totalRecords,
      "page" => $page,
      "itemsPerPage" => $itemsPerPage
    ];
  }

  public static function getProductById(int $idProducto) {
    $sqlstr = "SELECT 
      p.id_producto,
      p.nombre_producto,
      p.descripcion,
      p.precio,
      p.stock,
      p.imagen,
      p.id_categoria,
      p.id_marca,
      p.id_estado
    FROM producto p WHERE p.id_producto = :idProducto";
    $params = ["idProducto" => $idProducto];
    return self::obtenerUnRegistro($sqlstr, $params);
  }

  public static function insertProduct(
    string $nombreProducto,
    string $descripcion,
    float $precio,
    int $stock,
    string $imagen,
    int $idCategoria,
    int $idMarca,
    int $idEstado
  ) {
    $sqlstr = "INSERT INTO producto 
      (nombre_producto, descripcion, precio, stock, imagen, id_categoria, id_marca, id_estado) 
      VALUES 
      (:nombreProducto, :descripcion, :precio, :stock, :imagen, :idCategoria, :idMarca, :idEstado)";
    $params = [
      "nombreProducto" => $nombreProducto,
      "descripcion" => $descripcion,
      "precio" => $precio,
      "stock" => $stock,
      "imagen" => $imagen,
      "idCategoria" => $idCategoria,
      "idMarca" => $idMarca,
      "idEstado" => $idEstado
    ];
    
    return self::executeNonQuery($sqlstr, $params);

    
  }

  public static function updateProduct(
    int $idProducto,
    string $nombreProducto,
    string $descripcion,
    float $precio,
    int $stock,
    string $imagen,
    int $idCategoria,
    int $idMarca,
    int $idEstado
  ) {
    $sqlstr = "UPDATE producto SET
      nombre_producto = :nombreProducto,
      descripcion = :descripcion,
      precio = :precio,
      stock = :stock,
      imagen = :imagen,
      id_categoria = :idCategoria,
      id_marca = :idMarca,
      id_estado = :idEstado
      WHERE id_producto = :idProducto";
    $params = [
      "idProducto" => $idProducto,
      "nombreProducto" => $nombreProducto,
      "descripcion" => $descripcion,
      "precio" => $precio,
      "stock" => $stock,
      "imagen" => $imagen,
      "idCategoria" => $idCategoria,
      "idMarca" => $idMarca,
      "idEstado" => $idEstado
    ];
    return self::executeNonQuery($sqlstr, $params);
  }

  public static function deleteProduct(int $idProducto) {
    $sqlstr = "DELETE FROM producto WHERE id_producto = :idProducto";
    $params = ["idProducto" => $idProducto];
    return self::executeNonQuery($sqlstr, $params);
  }

        public static function getCategorias() {
            $sql = "SELECT id_categoria, nombre_categoria FROM categoria_producto;";
            return self::obtenerRegistros($sql, []);
        }

        public static function getMarcas() {
            $sql = "SELECT id_marca, nombre_marca FROM marca_producto;";
            return self::obtenerRegistros($sql, []);
        }

        public static function getEstados() {
            $sql = "SELECT id_estado, estado FROM estado_producto;";
            return self::obtenerRegistros($sql, []);
        }
//PARA QUE NO SE ELIMINE UNA CATEGORIA ENLAZADA A ALGUN PRODUCTO --INVESTIGADO
        public static function countByCategoria(int $id_categoria): int
        {
            $sql = "SELECT COUNT(*) AS total FROM producto WHERE id_categoria = :id_categoria";
            $params = ["id_categoria" => $id_categoria];
            $row = self::obtenerUnRegistro($sql, $params);
            return intval($row["total"] ?? 0);
        }

        public static function countByMarca(int $idMarca): int
        {
            $sql = "SELECT COUNT(*) AS productos_count FROM producto WHERE id_marca = :idMarca;";
            $row = self::obtenerUnRegistro($sql, ["idMarca" => $idMarca]);
            return intval($row["productos_count"] ?? 0);
        }


}
