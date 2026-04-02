// Función para editar
// Función para editar
document.querySelectorAll(".btn-editar").forEach(btn => {
    btn.addEventListener("click", () => {
        // Identificador para enviar a la BDD
        document.getElementById("equipoId").value = btn.dataset.id;
        // Items que se desean modificar
        document.getElementById("inputCodigo").value = btn.dataset.codigo;
        document.getElementById("inputDepartamento").value = btn.dataset.departamento;
        // Mostrar modal editar
        let modal = new bootstrap.Modal(document.getElementById("modalFicha"));
        modal.show();
    });
});

// Funcion para crear usuarios admin
document.getElementById("btnCrearUsuario").addEventListener("click", () => {
    // Almacenar los datos dentro del input
    let usuario = document.getElementById("inputUsuarioNuevo").value;
    let password = document.getElementById("inputPasswordNuevo").value;
    // Enviar datos a la api
    fetch("/inventario/api/crear_usuario.php", {
        method: "POST",
        credentials: "include",
        headers: {
            "Content-Type": "application/json"
        },
         // Convertir texto a JSON
        body: JSON.stringify({
            usuario: usuario,
            password: password
        })
    })
    .then(res => res.text()) // 🔥 DEBUG PRIMERO
    .then(res => {
        console.log("RESPUESTA RAW:", res);
        // Intentar convertir texto a JSON
        try {
            let json = JSON.parse(res);
            if (json.success) {
                alert("Usuario creado correctamente");
                // Ocultar modal
                const modalElement = document.getElementById("modalCrearUsuario");
                const modal = bootstrap.Modal.getInstance(modalElement);
                modal.hide();
                // Vaciar los imnut
                document.getElementById("inputUsuarioNuevo").value = "";
                document.getElementById("inputPasswordNuevo").value = "";
            } else {
                alert(json.message);
            }
        // Capturar error para evitar confuciones
        } catch (e) {
            alert("Error del servidor (no es JSON)");
        }
    })
    // Se ejecuta solo en el caso de perdida de coneccion con servidor
    .catch(() => {
        alert("Error de conexión");
    });
});