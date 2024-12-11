<?php
class DatabaseConfig {
    private static $host = 'localhost';
    private static $username = 'root';
    private static $password = '';
    private static $dbname = 'hotel_booking_system';

    public static function getConnectionParams() {
        return [
            'host' => self::$host,
            'username' => self::$username,
            'password' => self::$password,
            'dbname' => self::$dbname
        ];
    }
}