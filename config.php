<?php
// 数据库配置文件
$servername = "localhost";
$username = "newyearcountdown";
$password = "NPcm7A9wiGYiSfdT";
$dbname = "newyearcountdown";

// API配置
$api_config = [
    // 每页留言数量
    'messages_per_page' => 40,
    // 敏感词检测开关
    'enable_sensitive_check' => true,
    // 低质量内容检测开关
    'enable_low_quality_check' => true
];

/**
 * 获取数据库连接
 * @return mysqli 数据库连接对象
 */
function getDbConnection() {
    global $servername, $username, $password, $dbname;
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // 检查连接
    if ($conn->connect_error) {
        die(json_encode(['success' => false, 'message' => '数据库连接失败: ' . $conn->connect_error]));
    }
    
    // 选择数据库
    $conn->select_db($dbname);
    
    return $conn;
}

/**
 * 创建必要的数据库和表
 * @param mysqli $conn 数据库连接对象
 */
function createDatabaseAndTables($conn) {
    global $dbname;
    
    // 创建数据库（如果不存在）
    $create_db_sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    $conn->query($create_db_sql);
    
    // 选择数据库
    $conn->select_db($dbname);
    
    // 创建留言表（旧版，兼容用）
    $create_table_sql = "CREATE TABLE IF NOT EXISTS messages (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nickname VARCHAR(50) NOT NULL,
        content TEXT NOT NULL,
        ip VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $conn->query($create_table_sql);
    
    // 创建新版留言表
    $create_message_table = "CREATE TABLE IF NOT EXISTS message (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nickname VARCHAR(50) NOT NULL,
        content TEXT NOT NULL,
        user_id VARCHAR(255) NOT NULL COMMENT '用户唯一标识',
        create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $conn->query($create_message_table);
    
    // 创建祝福表
    $create_bless_table = "CREATE TABLE IF NOT EXISTS blessings (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id VARCHAR(255) NOT NULL COMMENT '用户唯一标识',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $conn->query($create_bless_table);
    
    // 检查并添加user_id字段到旧版祝福表
    $check_column_sql = "SHOW COLUMNS FROM blessings LIKE 'user_id'";
    $check_result = $conn->query($check_column_sql);
    if ($check_result && $check_result->num_rows === 0) {
        $alter_table_sql = "ALTER TABLE blessings ADD COLUMN user_id VARCHAR(255) NOT NULL DEFAULT 'unknown' COMMENT '用户唯一标识'";
        $conn->query($alter_table_sql);
    }
    
    // 检查并添加user_id字段到旧版留言表
    $check_column_sql = "SHOW COLUMNS FROM message LIKE 'user_id'";
    $check_result = $conn->query($check_column_sql);
    if ($check_result && $check_result->num_rows === 0) {
        $alter_table_sql = "ALTER TABLE message ADD COLUMN user_id VARCHAR(255) NOT NULL DEFAULT 'unknown' COMMENT '用户唯一标识'";
        $conn->query($alter_table_sql);
    }
    
    // 创建索引
    $check_index_sql = "SHOW INDEX FROM message WHERE Key_name = 'idx_message_user_id_created_at'";
    $check_index_result = $conn->query($check_index_sql);
    if ($check_index_result && $check_index_result->num_rows === 0) {
        $create_index = "CREATE INDEX idx_message_user_id_created_at ON message(user_id, create_time)";
        $conn->query($create_index);
    }
    
    $check_index_sql = "SHOW INDEX FROM blessings WHERE Key_name = 'idx_user_id_created_at'";
    $check_index_result = $conn->query($check_index_sql);
    if ($check_index_result && $check_index_result->num_rows === 0) {
        $create_index = "CREATE INDEX idx_user_id_created_at ON blessings(user_id, created_at)";
        $conn->query($create_index);
    }
}
?>