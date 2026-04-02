document.addEventListener("DOMContentLoaded", function () {

    const botones = document.querySelectorAll(".btn-ficha");
    const modalElement = document.getElementById('modalFicha');
    const modal = new bootstrap.Modal(modalElement);

    const inputCodigo = document.getElementById("inputCodigo");
    const equipoIdInput = document.getElementById("equipoId");
    const btnGuardar = document.getElementById("guardarCodigo");

    let equipoIdActual = null;

    botones.forEach(boton => {
        boton.addEventListener("click", function (e) {

            let codigo = this.getAttribute("data-codigo");
            let href = this.getAttribute("href");

            // extraer ID desde el href
            let id = href.split("id=")[1];

            console.log("Código inventario:", codigo);
            console.log("ID equipo:", id);

            if (!codigo || codigo === "null" || codigo === "") {
                e.preventDefault();

                equipoIdActual = id;
                equipoIdInput.value = id;
                inputCodigo.value = "";

                modal.show();
            }
        });
    });

    function validarCodigo(codigo) {
        // ✅ permitir vacío
        if (codigo === "") return true;

        // ✅ o formato correcto
        const regex = /^\d{2}-\d{2}-\d{2}-\d{4}-\d{5}$/;
        return regex.test(codigo);
    }

    // 🔥 GUARDAR EN BD
    btnGuardar.addEventListener("click", function () {

        let codigo = inputCodigo.value.trim();
        let id = equipoIdInput.value;

        if (!validarCodigo(codigo)) {
            alert("Formato inválido. Usa: 01-08-01-1234-12345 o deja el campo vacio");
            return;
        }

        fetch("/inventario/api/guardar_codigo.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                id: id,
                codigo_inventario: codigo
            })
        })
        .then(res => res.json())
        .then(data => {
            console.log(data);

            alert("Código guardado correctamente");

            modal.hide();

            // 🔥 recargar para ver cambios
            location.reload();
        })
        .catch(err => {
            console.error(err);
            alert("Error al guardar");
        });

    });

});