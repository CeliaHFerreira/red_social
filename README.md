MeloDJ
======


MeloDJ es una red social desarrollada en PHP y Javascript con ayuda del Framework [Symfony 3.4](https://symfony.com/)

Esta red social de temática musical tiene una funcionalidad básica: realizar publicaciones (melodías) por parte de unos
usuarios compositores para que después estas sean valoradas y comentadas por el resto de usuarios. Se permite también
la búsqueda y seguimiento de usuarios, además de la configuración de ciertos datos personales de las cuentas de usuario
y el envío de mensajes privados. Como ya se ha comentado, al ser una red social de temática musical, su principal virtud
reside en la posibilidad de realizar composiciones musicales automáticas mediante una red neuronal. El sistema será capaz
de componer melodías en función de unos parámetros definidos por el usuario.


¿Qué contiene?
--------------

La aplicación está organizada en una serie de directorios:

      app/              contiene la configuración de la aplicación
      bin/              contiene comandos de consola
      src/              contiene todo el código PHP de la aplicación (bundles)
      test/             contiene los test generados
      var/              contiene los archivos generados en el tiempo de ejecución
      vendor/           contiene paquetes dependientes de terceras partes
      web/              contiene tests para la aplicación
        assets/         directorio web raíz. Contiene todos los archivos que se pueden acceder públicamente
          css/          archivos css
          js/           archivos js
        uploads/        contiene los archivos subidos por los usuarios


Requisitos
----------

Para su instalación y funcionamientos, se necesita un servidor web que soporte PHP 5 o superior. Además se deberán instalar una
serie de dependencias de terceros mediante el archivo composer.json. Para su correcto funcionamiento deberá aumentarse, en el archivo de configuración php.ini el valor de tiempo máximo de ejecución (EXECUTION_TIME).


Configuración
-------------

Para configurar la base de datos y el servidor de correo, será necesario editar el archivo app/config/parameters.yml con los 
datos correctos, como por ejemplo:

    parameters:
        database_host: 127.0.0.1
        database_port: null
        database_name: red_social
        database_user: root
        database_password: password
        mailer_transport: gmail
        mailer_user: correo@gmail.com
        mailer_password: passwordCorreo
        secret: ThisIsNotSecretChangeIt

Autor
-----

Celia Herrera Ferreira:
Estudiante del Grado en Ingeniería Informática de la Universidad de Salamanca
