create DATABASE app_reporte;
Use app_reporte;

create table notificaciones(
    id_notificacion int AUTO_INCREMENT PRIMARY key,
    tipo VARCHAR(50),
    created_at DATETIME DEFAULT NOW COMMENT 'fecha q se genero la nofificacion ',
    estado VARCHAR(50) DEFAULT 'Reportado' CHECK(estado='Reportado' OR estado='En camino' or estado='Antendiendo' or estado='Finalizado') 
);

ALTER TABLE notificaciones
ADD COLUMN updated_at DATETIME;

