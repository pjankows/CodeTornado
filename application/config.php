<?php
/**
 * This configuration replaces the ini file because it is faster
*/
return array(
            'database' => array
            (
                'adapter' => 'PDO_MYSQL',
                'params' => array
                (
                    'host' => '127.0.0.1',
                    'username' => 'root',
                    'password' => 'burn',
                    'dbname' => 'mgr',
                    'driver_options' => array
                    (
                        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
                    )
                )
            ),
            'salt' => '5#6n$v78!9v|n%u6y',
            'git' => array
            (
                'command' => 'git',
                'path' => APPLICATION_PATH . '/../data/'
            ),
            'data' => array
            (
                'path' => APPLICATION_PATH . '/../data/'
            )
        );