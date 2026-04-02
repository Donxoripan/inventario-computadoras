import os
import shutil

def obtener_ruta_smart():

    ruta_local = os.path.abspath(
        os.path.join(os.path.dirname(__file__), "..", "..", "..", "herramientas", "smartctl.exe")
    )

    if os.path.exists(ruta_local):
        return ruta_local

    # fallback si está instalado en el sistema
    ruta_path = shutil.which("smartctl")

    if ruta_path:
        return ruta_path

    return None