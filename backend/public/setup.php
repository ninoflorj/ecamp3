<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

use eCamp\Lib\Fixture\FixtureLoader;

if (PHP_SAPI == 'cli') {
    echo PHP_EOL;
    echo '  Run Setup in a Browser!';
    echo PHP_EOL;
    echo PHP_EOL;

    exit();
}

//  ENV:
// ======

$env = getenv('env') ?: 'dev';
if ('dev' !== $env) {
    echo "The environment must be 'dev' to run setup. ";
    echo 'Current environment: '.$env;

    exit();
}

//  COMPOSER:
// ===========

if (!file_exists(__DIR__.'/../vendor/autoload.php')) {
    echo '<br />';
    echo '(01) Composer: ERROR';
    echo '<br />';
    echo '<br />';
    echo 'Run composer on commandline:';
    echo '<br />';
    echo 'composer install';
    echo '<br />';
    echo '<br />';
    echo 'If you have not installed composer:';
    echo '<br />';
    echo "<a href='https://getcomposer.org/'>https://getcomposer.org/</a>";

    exit();
}

include_once __DIR__.'/../vendor/autoload.php';

// Bootstrap application
eCampApp::RegisterWhoops();
$app = eCampApp::CreateSetup();

echo '<br />';
echo '(01) Composer: OK';
echo '<br />';

$sm = $app->getServiceManager();
/** @var \Doctrine\ORM\EntityManager $em */
$em = $sm->get('doctrine.entitymanager.orm_default');
$conn = $em->getConnection();

//  DB-Connection:
// ================

try {
    $conn->connect();
    echo '<br />';
    echo '(02) Database-Connection: OK';
} catch (\Exception $e) {
    echo '<br />';
    echo '(02) Database-Connection: ERROR';
    echo '<br />';
    echo '<br />';
    echo 'Message:';
    echo '<br />';
    echo $e->getMessage();

    if (strstr($e->getMessage(), '1044') > 0
     || strstr($e->getMessage(), '1045') > 0
    ) {
        echo '<br />';
        echo '<br />';
        echo 'Access denied:';
        echo '<br />';
        echo "Check the file 'config/autoload/doctrine.local.dev.dist'";
        echo '<br />';
        echo "Copy this file to 'config/autoload/doctrine.local.dev.php'";
        echo '<br />';
        echo 'Change credentials in the new config-file';
    }

    if (strstr($e->getMessage(), '1049')) {
        echo '<br />';
        echo '<br />';
        echo 'Unknown database:';
        echo '<br />';
        echo 'You must create a new database.';
        echo '<br />';
        echo 'You might use phpmyadmin for this';
    }

    exit();
}

$schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
$allMetadata = $em->getMetadataFactory()->getAllMetadata();

//  Drop & recreate database
// =========================

echo '<br />';
echo "<a href='?drop-data'>Drop database & recreate schema</a>";
echo '<br />';

if (array_key_exists('drop-data', $_GET)) {
    try {
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($allMetadata);

        echo 'OK';
        echo '<br />';
    } catch (\Exception $e) {
        echo '<br />';
        echo '<br />';
        echo $e->getMessage();

        exit();
    }
}

//  Schema-Validation:
// ====================
$schemaValidation = function () use ($schemaTool, $allMetadata) {
    try {
        $updateSqls = $schemaTool->getUpdateSchemaSql($allMetadata, true);
        if (0 !== count($updateSqls)) {
            $msg = 'Some tables are out of sync. ';
            $msg .= '<br />';
            $msg .= 'Schema-Update required. ';
            $msg .= '<br />';
            $msg .= 'Run: vendor/bin/doctrine ';

            throw new Exception($msg);
        }
        echo '<br />';
        echo '(03) Schema-Validation: OK';
    } catch (\Exception $e) {
        echo '<br />';
        echo '(03) Schema-Validation: ERROR';
        echo '<br />';
        echo $e->getMessage();
        echo '<br />';
//    die();
    }
};

if (array_key_exists('update-schema', $_GET)) {
    try {
        $schemaTool->updateSchema($allMetadata);
        $schemaValidation();

        echo '<br />';
        echo "<a href='?update-schema'>Update database schema</a>";
        echo '<br />';
        echo 'OK';
        echo '<br />';
    } catch (\Exception $e) {
        echo '<br />';
        echo '<br />';
        echo $e->getMessage();
    }
} else {
    $schemaValidation();
    echo '<br />';
    echo "<a href='?update-schema'>Update database schema</a>";
    echo '<br />';
}

echo '<br />';
echo '<br />';
echo "<a href='?prod-data'>Load Prod-Data</a>";

if (array_key_exists('prod-data', $_GET)) {
    try {
        $loader = $sm->get(FixtureLoader::class);
        $paths = \Laminas\Stdlib\Glob::glob(__DIR__.'/../module/*/data/prod/*.php');

        foreach ($paths as $path) {
            echo '<br />';
            echo $path;
            $loader->loadFromFile($path);
        }

        $executor = new \Doctrine\Common\DataFixtures\Executor\ORMExecutor(
            $em,
            new \Doctrine\Common\DataFixtures\Purger\ORMPurger()
        );
        $executor->execute($loader->getFixtures(), true);

        echo '<br />';
        echo 'OK';
    } catch (\Exception $e) {
        echo '<br />';
        echo '<br />';
        echo $e->getMessage();

        exit();
    }
}

echo '<br />';
echo '<br />';
echo "<a href='?dev-data'>Load Dev-Data</a>";

if (array_key_exists('dev-data', $_GET)) {
    try {
        $loader = $sm->get(FixtureLoader::class);

        $paths = \Laminas\Stdlib\Glob::glob(__DIR__.'/../module/*/data/prod/*.php');
        foreach ($paths as $path) {
            echo '<br />';
            echo $path;
            $loader->loadFromFile($path);
        }

        $paths = \Laminas\Stdlib\Glob::glob(__DIR__.'/../module/*/data/dev/*.php');
        foreach ($paths as $path) {
            echo '<br />';
            echo $path;
            $loader->loadFromFile($path);
        }

        $executor = new \Doctrine\Common\DataFixtures\Executor\ORMExecutor(
            $em,
            new \Doctrine\Common\DataFixtures\Purger\ORMPurger()
        );
        $executor->execute($loader->getFixtures(), true);

        echo '<br />';
        echo 'OK';
    } catch (\Exception $e) {
        echo '<br />';
        echo '<br />';
        echo $e->getMessage();

        exit();
    }
}

echo '<br />';
echo '<br />';
echo '<br />';
echo 'Back to Index: ';
echo "<a href='.'>Index</a>";

echo '<script>';
echo 'window.history.replaceState({}, document.title, "/setup.php");';
echo '</script>';
