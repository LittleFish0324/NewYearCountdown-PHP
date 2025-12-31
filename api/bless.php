<?php
// 设置响应头
header('Content-Type: application/json; charset=utf-8');

// 引入配置文件
require_once '../config.php';

// 获取数据库连接
$conn = getDbConnection();

// 创建必要的数据库和表
createDatabaseAndTables($conn);

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
    // 获取用户唯一标识（IP + User-Agent的组合，解决同一内网下不同设备的问题）
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    // 使用MD5生成唯一的user_id，避免存储明文IP和User-Agent
    $user_id = md5($user_ip . $user_agent);
    
    // 检查用户频率限制（每2分钟只能发送一次）
    $two_minutes_ago = date('Y-m-d H:i:s', strtotime('-2 minutes'));
    $sql = "SELECT created_at FROM blessings WHERE user_id = ? AND created_at >= ? ORDER BY created_at DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die(json_encode(['success' => false, 'message' => '准备限制检查失败: ' . $conn->error]));
    }
    $stmt->bind_param("ss", $user_id, $two_minutes_ago);
    $stmt->execute();
    $stmt->bind_result($last_time);
    $stmt->fetch();
    $stmt->close();
    
    if ($last_time) {
        $remaining_time = 120 - (time() - strtotime($last_time));
        die(json_encode(['success' => false, 'message' => '请间隔2分钟后再发送祝福，还需等待' . $remaining_time . '秒']));
    }
    
    // 插入祝福记录，包含user_id
    $sql = "INSERT INTO blessings (user_id) VALUES (?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die(json_encode(['success' => false, 'message' => '准备插入祝福失败: ' . $conn->error]));
    }
    $stmt->bind_param("s", $user_id);
    
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