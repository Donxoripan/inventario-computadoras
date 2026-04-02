<?php
/* Verificar que las credenciales se hayan ingresado */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
/* En el caso de no estarlo redirigir al login*/
if (!isset($_SESSION["usuario"])) {
    header("Location: panel/login.php");
    exit;
}
?>
<!--Formato de HTML por predeterminado -->
<!DOCTYPE html>
<!-- Tipo de idioma de la pagina HTML -->
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <!-- Titulo de encabezado -->
        <title>Inventario</title>
        <!-- Llamada de los estilos al html -->
        <link rel="icon" href="/inventario/panel/images/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="/inventario/panel/css/index.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <!-- Contenido interno de la pagina HTML -->
    <body>
        <?php
        /* Llamada para coneccion con los PHP */   
        require_once __DIR__ . "/../config/conexion.php";
        require_once __DIR__ . "/../agente/logica/cpu.php";
        /* Error en casi de mala conexion con el servidor*/
        if ($conn->connect_error) {
            die("Error de conexión: " . $conn->connect_error);
        }
        /* Mostrar todo los datos de la tabla equipos */
        $result = $conn->query("SELECT * FROM equipos");
        ?>
        <!-- Panel para la creacion de usuario -->
        <div class="user-panel">
            <div class="bienvenida">
                Bienvenido, <strong><?php echo htmlspecialchars($_SESSION["usuario"]); ?></strong>
            </div>
            <div class="acciones">
                <a href="/inventario/api/logout.php" class="btn btn-danger btn-sm">
                    Cerrar sesión
                </a>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalCrearUsuario">
                    Crear usuario
                </button>
            </div>
        </div>
        <!--  Titulo -->
        <h1>Inventario de Equipos</h1>
        <!-- Cosulta al servidor -->
        <p>Total de equipos: <?php echo $result->num_rows; ?></p>
        <!-- Checkbox para modo oscuro -->
        <label class="switch">
            <input type="checkbox" id="themeToggle">
            <span class="slider"></span>
        </label>
        <!-- Crear contenedor de tabla -->
        <div class="tabla-equipo">
            <!-- Evitar que el buscador se autocomplete -->
            <input type="text" style="display:none">
            <input type="password" style="display:none">
            <!-- Buscador -->
            <div class="buscador-container d-flex gap-2">
                <input type="text" id="buscador" class="form-control" placeholder="Buscar..." autocomplete="off" name="no-autocomplete">
                <button type="button" id="btnBuscar" class="btn btn-primary">🔍</button>
            </div>
            <!-- Barra de navegacion dentro de la tabla -->
            <div class="tabla-scroll">
                <!-- Crear formato de tabla equipos para el rango de busqueda -->
                <table>
                    <!-- Encabezado de las columnas -->
                    <thead>
                        <tr>
                            <th>Código Inventario</th>
                            <th>PC</th>
                            <th>Usuario</th>
                            <th>Departamento</th>
                            <th>Sistema</th>
                            <th>anydesk</th>
                            <th>CPU</th>
                            <th>RAM</th>
                            <th>Disco</th>
                            <th>IP</th>
                            <th>Detalles</th>
                            <th>Editar</th>
                        </tr>
                    </thead>
                    <!-- Body para poder recorrer los resultados de la BDD -->
                    <tbody>
                        <?php
                        /* Mostrar las filas correspondientes con el resultado de busqueda */
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                ?>
                                <!-- Generar filas con el contenido de los formatos definidos -->
                                <tr>
                                    <!-- Pequeña funcion pora poner en la columna de codigo inventario -->
                                    <td class="text-center">
                                        <?php
                                        $codigo = trim($row["codigo_inventario"] ?? "");
                                        // Definir formato del codigo de inventario
                                        $regex = '/^\d{2}-\d{2}-\d{2}-\d{3}-\d{5}$/';
                                        // Codigo de inventario pendiente ya que esta vacio
                                        if ($codigo === "" || is_null($row["codigo_inventario"])) {
                                            echo "<span class='badge bg-warning text-dark fs-6'>Pendiente</span>";
                                        // No tiene ningun codigo de inventario
                                        } elseif (strtolower($codigo) === "sin") {
                                            echo "<span class='badge bg-success fs-6'>No Tiene</span>";
                                        // Se muestra el codigo de inventario en la columna
                                        } elseif (preg_match($regex, $codigo)) {
                                            echo htmlspecialchars($codigo);
                                        // Se muestra error ya que no cumple las condiciones
                                        } else {
                                            echo "<span class='badge bg-danger fs-6'>Error</span>";
                                        }
                                        ?>
                                    </td>
                                    <!-- Definir los parametros que se mostraran en la fila y por cada solumna -->
                                    <td><?php echo $row["nombre_pc"]; ?></td>
                                    <td><?php echo $row["usuario"]; ?></td>
                                    <td><?php echo $row["departamento"]; ?></td>
                                    <td><?php echo $row["sistema_operativo"]; ?></td>
                                    <td><?php echo $row["anydesk"]; ?></td>
                                    <td><?php echo formatearCPU($row["cpu"]); ?></td>
                                    <td><?php echo $row["ram"]; ?></td>
                                    <td><?php echo $row["disco_total"]; ?></td>
                                    <td><?php echo $row["ip"]; ?></td>
                                    <!-- Boton para ver detalles en ficha PDF -->
                                    <td class="text-center align-middle">
                                        <a class="btn btn-primary btn-ficha-grande" 
                                        href="/inventario/panel/pdf.php?id=<?php echo $row['id']; ?>" 
                                        target="_blank">
                                            Ver ficha
                                        </a>
                                    </td>
                                    <!-- Boton para editar y ejecutar el script con el modal -->
                                    <td><button type="button" class="btn btn-warning btn-editar" data-id="<?php echo $row['id']; ?>" data-codigo="<?php echo $row['codigo_inventario']; ?>" data-departamento="<?php echo $row['departamento']; ?>"> Editar </button></td>
                                </tr>
                                <?php
                            }
                        /* En el caso de no encontrar datos en la tabla mostrar este mensaje */
                        } else {
                            echo "<tr><td colspan='11'>No hay equipos registrados</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <!--Mensaje en el caso de no obtener resultados de la BDD-->
            <p id="sinResultados" style="display:none;">No se encontraron resultados</p>
        </div>
        <!-- Modal para poder editar -->
        <div class="modal fade" id="modalFicha" tabindex="-1" aria-labelledby="tituloModalFicha" aria-hidden="true">
            <!-- Fijar el modal centrado y el tamaño de este mismo -->
            <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
                <div class="modal-content">
                    <!-- Definir formato del header del modal -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="tituloModalFicha">Editar equipo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <!-- Items que se utilizaran en la edicion -->
                    <div class="modal-body">
                        <!-- Identificador para insertar en BDD -->
                        <input type="hidden" id="equipoId">
                        <!-- Edicion del campo Departamento -->
                        <div class="mb-2 d-flex align-items-center">
                            <label style="min-width: 180px; margin-bottom: 0;">Departamento:</label>
                            <input type="text" id="inputDepartamento" class="form-control form-control-sm">
                        </div>
                        <!-- Edicion del campo Codigo de inventario -->
                        <div class="mb-2 d-flex align-items-center">
                            <label style="min-width: 180px; margin-bottom: 0;">Código Inventario:</label>
                            <input type="text" id="inputCodigo" class="form-control form-control-sm">
                        </div>
                        <hr>
                        <div class="form-group">
                            <!-- Campo para agregar perifericos -->
                            <div id="contenedor-perifericos" style="display:none;"></div>
                            <div class="form-group">
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <button type="button" id="btnAgregarPerifericos" class="btn btn-success btn-accion">
                                        Agregar Perifericos
                                    </button>
                                    <button type="button" id="btnQuitarPerifericos" class="btn btn-danger btn-accion">
                                        Eliminar Perifericos
                                    </button>
                                </div>
                            </div>
                            <hr>
                            <!-- Detalles -->
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                    Detalles
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <textarea class="form-control" rows="3" id="detalleEquipoEditar"
                                        placeholder="Ingrese detalle equipo" style="height: 100px; width: 425px;">
                                    </textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Botones de funcion dentro del modal -->
                    <div class="modal-footer">
                        <!-- Boton para cancelar la edicion de datos -->
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <!-- Boton para guardar lo editado y enviar el UPDATE a la BDD -->
                        <button type="button" class="btn btn-primary" id="guardarCodigo">
                            Guardar cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal crear usuario -->
        <div class="modal fade" id="modalCrearUsuario" tabindex="-1">
            <!-- Fijar el modal centrado y el tamaño de este mismo -->
            <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
                <div class="modal-content">
                    <!-- Definir formato del header del modal -->
                    <div class="modal-header">
                        <h5 class="modal-title">Crear Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <!-- Valores que se piden para la creacion del usuario-->
                    <div class="modal-body">
                        <!-- Definir el capo usuario -->
                        <div class="mb-2">
                            <label>Usuario:</label>
                            <input type="text" id="inputUsuarioNuevo" class="form-control">
                        </div>
                        <!-- Definir el campo contraseña -->
                        <div class="mb-2">
                            <label>Contraseña:</label>
                            <input type="password" id="inputPasswordNuevo" class="form-control">
                        </div>
                    </div>
                    <!-- Botones de funcion dentro del modal -->
                    <div class="modal-footer">
                        <!-- Boton para cancelar la creacion de usuario -->
                        <button class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <!-- Boton para guardar los datos y enviarlos a la BDD -->
                        <button id="btnCrearUsuario" class="btn btn-success">
                            Crear
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- SCRIPTS -->

        <!-- Script de los estilos y funciones de bootstrap -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Script de los estilos y funciones del modo oscuro -->
        <script src="/inventario/panel/js/modo_oscuro.js"></script>
        <!-- Script para la creacion, edicion y eliminacion de datos (CRUD) -->
        <script src="/inventario/panel/js/crud.js"></script>
        <!-- Script para la funcion de busqueda -->
        <script src="/inventario/panel/js/busqueda.js"></script>
        <!-- Script para la creacion de algun usuario en la BDD -->
        <script src="/inventario/panel/js/crear_usuario.js"></script>
        <!-- Script para agregar perifericos -->
        <script src="/inventario/panel/js/perifericos.js"></script>

    </body>
</html>