# realsoftgt/realsoft-fel

Paquete FEL/DTE multi-país (Guatemala & El Salvador) con arquitectura extensible por **CountryAdapter** y **CertifierDriver**.
Incluye validación de esquemas (XSD/JSON-Schema), contingencia (GT), generación de Número de Acceso por establecimiento, y drivers para Infile.

## Requisitos
- PHP 8.1+
- Laravel 10/11/12

## Instalación
```bash
composer require realsoft/realsoft-fel
php artisan vendor:publish --tag=realsoft-fel-config
php artisan migrate
```
