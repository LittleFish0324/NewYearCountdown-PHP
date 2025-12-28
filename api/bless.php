<?php
// 设置响应头
header('Content-Type: application/json; charset=utf-8');

// 直接定义数据库配置
$servername = "localhost";
$username = "newyearcountdown";
$password = "NPcm7A9wiGYiSfdT";
$dbname = "newyearcountdown";

// 创建数据库连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => '数据库连接失败']));
}

// 创建祝福表（如果不存在）
$create_bless_table = "CREATE TABLE IF NOT EXISTS blessings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
if (!$conn->query($create_bless_table)) {
    die(json_encode(['success' => false, 'message' => '创建祝福表失败: ' . $conn->error]));
}

// 处理GET请求 - 获取祝福数
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT COUNT(*) as count FROM blessings";
    $result = $conn->query($sql);
    if (!$result) {
        die(json_encode(['success' => false, 'message' => '查询祝福数失败: ' . $conn->error]));
    }
    $row = $result->fetch_assoc();
    $count = $row['count'];
    
    die(json_encode(['success' => true, 'count' => $count]));
}
// 处理POST请求 - 增加祝福
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 检查IP频率限制（每2分钟只能发送一次）
    $two_minutes_ago = date('Y-m-d H:i:s', strtotime('-2 minutes'));
    $sql = "SELECT created_at FROM blessings WHERE created_at >= ? ORDER BY created_at DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die(json_encode(['success' => false, 'message' => '准备限制检查失败: ' . $conn->error]));
    }
    $stmt->bind_param("s", $two_minutes_ago);
    $stmt->execute();
    $stmt->bind_result($last_time);
    $stmt->fetch();
    $stmt->close();
    
    if ($last_time) {
        $remaining_time = 120 - (time() - strtotime($last_time));
        die(json_encode(['success' => false, 'message' => '请间隔2分钟后再发送祝福，还需等待' . $remaining_time . '秒']));
    }
    
    // 插入祝福记录
    $sql = "INSERT INTO blessings () VALUES ()";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die(json_encode(['success' => false, 'message' => '准备插入祝福失败: ' . $conn->error]));
    }
    
    if (!$stmt->execute()) {
        $stmt->close();
        die(json_encode(['success' => false, 'message' => '插入祝福失败: ' . $conn->error]));
    }
    
    $stmt->close();
    
    // 获取最新的祝福数
    $sql = "SELECT COUNT(*) as count FROM blessings";
    $result = $conn->query($sql);
    if (!$result) {
        die(json_encode(['success' => false, 'message' => '查询祝福数失败: ' . $conn->error]));
    }
    $row = $result->fetch_assoc();
    $count = $row['count'];
    
    die(json_encode(['success' => true, 'count' => $count]));
}
else {
    die(json_encode(['success' => false, 'message' => '不支持的请求方法']));
}

// 关闭连接
$conn->close();