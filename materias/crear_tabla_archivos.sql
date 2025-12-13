-- Tabla para almacenar archivos de contenido por materia
CREATE TABLE IF NOT EXISTS archivos_materia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    materia_id INT NOT NULL,
    nombre_archivo VARCHAR(255) NOT NULL,
    nombre_original VARCHAR(255) NOT NULL,
    ruta_archivo VARCHAR(500) NOT NULL,
    tipo_archivo VARCHAR(100),
    tamano_bytes INT,
    descripcion TEXT,
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (materia_id) REFERENCES materias(id) ON DELETE CASCADE,
    INDEX idx_materia (materia_id)
);
