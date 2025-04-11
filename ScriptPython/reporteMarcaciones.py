import csv
import argparse
import mysql.connector
from datetime import datetime

def generar_reporte_marcaciones(fecha_inicio, fecha_fin, archivo_salida):
    """
    Genera un reporte CSV con las marcaciones de empleados en un rango de fechas
    
    Args:
        fecha_inicio (str): Fecha de inicio en formato YYYY-MM-DD
        fecha_fin (str): Fecha de fin en formato YYYY-MM-DD
        archivo_salida (str): Ruta del archivo CSV de salida
    """
    
    try:
        # 1. Conexión a la base de datos (ajusta estos parámetros)
        conexion = mysql.connector.connect(
            host="127.0.0.1",  
            port=3306,
            user="root",
            database="GestionEmpleados",
            password=""  # Aquí coloca tu contraseña si tienes una
        )



        
        cursor = conexion.cursor(dictionary=True)
        
        # 2. Consulta SQL para obtener las marcaciones
        query = """
        SELECT m.empleado_id, u.name as nombre, 
               DATE(m.timestamp) as fecha, 
               TIME(m.timestamp) as hora,
               m.tipo_marcacion
        FROM marcacions m
        JOIN users u ON m.empleado_id = u.id
        WHERE DATE(m.timestamp) BETWEEN %s AND %s
        ORDER BY m.empleado_id, m.timestamp
        """
        
        cursor.execute(query, (fecha_inicio, fecha_fin))
        marcaciones = cursor.fetchall()
        
        # 3. Generar el archivo CSV
        with open(archivo_salida, mode='w', newline='', encoding='utf-8') as file:
            writer = csv.writer(file)
            
            # Escribir encabezados
            writer.writerow(['empleado_id', 'nombre', 'fecha', 'hora', 'tipo_marcacion'])
            
            # Escribir datos
            for marcacion in marcaciones:
                writer.writerow([
                    marcacion['empleado_id'],
                    marcacion['nombre'],
                    marcacion['fecha'],
                    marcacion['hora'],
                    marcacion['tipo_marcacion']
                ])
        
        print(f"Reporte generado exitosamente en: {archivo_salida}")
        print(f"Total de registros: {len(marcaciones)}")
        
    except mysql.connector.Error as err:
        print(f"Error de base de datos: {err}")
    except Exception as e:
        print(f"Error inesperado: {e}")
    finally:
        if 'conexion' in locals() and conexion.is_connected():
            cursor.close()
            conexion.close()

def validar_fecha(fecha_str):
    """
    Valida que una cadena tenga el formato de fecha YYYY-MM-DD
    
    Args:
        fecha_str (str): Cadena con la fecha a validar
        
    Returns:
        str: La fecha validada
    Raises:
        argparse.ArgumentTypeError: Si el formato es inválido
    """
    try:
        datetime.strptime(fecha_str, '%Y-%m-%d')
        return fecha_str
    except ValueError:
        raise argparse.ArgumentTypeError(f"Fecha inválida: {fecha_str}. Formato esperado: YYYY-MM-DD")

if __name__ == "__main__":
    # Configurar argumentos de línea de comandos
    parser = argparse.ArgumentParser(description='Generar reporte de marcaciones en CSV')
    parser.add_argument(
        '--inicio', 
        type=validar_fecha, 
        required=True,
        help='Fecha de inicio (YYYY-MM-DD)'
    )
    parser.add_argument(
        '--fin', 
        type=validar_fecha, 
        required=True,
        help='Fecha de fin (YYYY-MM-DD)'
    )
    parser.add_argument(
        '--output', 
        default='reporte_marcaciones.csv',
        help='Nombre del archivo de salida (default: reporte_marcaciones.csv)'
    )
    
    args = parser.parse_args()
    
    # Validar que la fecha de inicio sea menor o igual a la fecha fin
    if args.inicio > args.fin:
        print("Error: La fecha de inicio debe ser menor o igual a la fecha de fin")
        exit(1)
    
    # Generar el reporte
    generar_reporte_marcaciones(args.inicio, args.fin, args.output)

   #Ejecute de esta manera el script desde la terminal: especififcando la fecha de inicio y fin
#python reporteMarcaciones.py --inicio 2025-01-01 --fin 2025-11-30 --output marcaciones_2025.csv