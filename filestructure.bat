@echo off
REM Batch script to create the folder and file structure for the Simple Grocery POS System

echo Creating project structure...

REM Create root level files
echo Creating root files...
type nul > index.php
type nul > login.php
type nul > logout.php
type nul > config.php
type nul > .htaccess

REM Create data directory and files
echo Creating data directory...
mkdir data
echo Creating data files...
type nul > data\products.json
type nul > data\sales.json
type nul > data\users.json

REM Create modules directory structure
echo Creating modules directory...
mkdir modules
mkdir modules\auth
echo Creating auth module files...
type nul > modules\auth\functions.php

mkdir modules\products
echo Creating products module files...
type nul > modules\products\functions.php
type nul > modules\products\manage_products.php

mkdir modules\sales
echo Creating sales module files...
type nul > modules\sales\functions.php
type nul > modules\sales\billing.php
type nul > modules\sales\history.php

mkdir modules\data_handling
echo Creating data_handling module files...
type nul > modules\data_handling\functions.php

mkdir modules\ui
echo Creating ui module files...
type nul > modules\ui\header.php
type nul > modules\ui\footer.php
type nul > modules\ui\components.php

REM Create assets directory structure
echo Creating assets directory...
mkdir assets
mkdir assets\css
echo Creating css asset files...
type nul > assets\css\style.css

mkdir assets\js
echo Creating js asset files...
type nul > assets\js\app.js

echo.
echo Folder and file structure created successfully!

REM Pause to see the output before the window closes
pause