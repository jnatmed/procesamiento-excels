## Instalación y Configuración de PhpSpreadsheet en Windows

### Instalación de PhpSpreadsheet

1. Abre una terminal y ejecuta el siguiente comando para instalar PhpSpreadsheet:

   ```sh
   composer require phpoffice/phpspreadsheet

2. En Windows
Abre el archivo php.ini que se encuentra en el directorio de instalación de PHP.
Busca la línea ;extension=zip y elimine el punto y coma (;) al inicio de la línea para descomentarla.
Guarda el archivo php.ini.
Reinicia el servidor web (Apache, Nginx, etc.) para aplicar los cambios.