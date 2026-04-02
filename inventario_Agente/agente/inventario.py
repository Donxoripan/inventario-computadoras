import socket
import requests
import json

from funciones.permisos import admin
from funciones.usuario import pedir_nombre_completo
from funciones.grupo_trabajo import obtener_grupo_trabajo
from funciones.sistema_operativo import obtener_sistema
from funciones.anydesk import obtener_anydesk
from funciones.cpu import obtener_cpu
from funciones.ram import obtener_ram
from funciones.ip import obtener_ip
from funciones.uuid import obtener_uuid
from funciones.serial import obtener_serial
from funciones.discos.disco  import obtener_disco_principal
from funciones.discos.main import obtener_discos_smart
from funciones.discos.utils import obtener_ruta_smart

admin()

nombre_pc = socket.gethostname()

usuario = pedir_nombre_completo()
usuario_str = f"{usuario['nombre']} {usuario['apellido1']} {usuario['apellido2']}"

departamento = obtener_grupo_trabajo()

sistema = obtener_sistema()

anydesk = obtener_anydesk()
print("anydesk:", anydesk)

cpu = obtener_cpu()

ram = obtener_ram()

disco_principal = obtener_disco_principal()

ip = obtener_ip()

uuid = obtener_uuid()

serial = obtener_serial()

discos_fisicos = obtener_discos_smart()

print("Discos detectados:", discos_fisicos)

ruta = obtener_ruta_smart()
print("Ruta SMART:", ruta)

data = {
    "nombre_pc": nombre_pc,
    "usuario": usuario_str,
    "departamento": departamento,
    "sistema_operativo": sistema,
    "anydesk": anydesk,
    "cpu": cpu,
    "ram": ram,
    "disco_total": disco_principal,
    "ip": ip,
    "uuid": uuid,
    "serial": serial,
    "discos": discos_fisicos,
    "codigo_inventario": None
}

url = "https://bf0f-190-121-9-194.ngrok-free.app/inventario/api/guardar_equipo.php"

try:
    respuesta = requests.post(url, json=data, timeout=60)
    print("Servidor:", respuesta.text)

except requests.exceptions.RequestException as e:
    print("Error enviando datos:", e)

input("\nPresiona ENTER para cerrar...")